# CLAUDE.md

Este archivo provee orientación a Claude Code (claude.ai/code) cuando trabaja con el código de este repositorio.

---

## Comandos del proyecto

```bash
# Instalar dependencias PHP
composer install

# Instalar dependencias JS
npm install

# Instalación inicial completa (genera key, migra, siembra y publica Voyager)
php artisan template:install

# Instalación manual paso a paso
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh
php artisan db:seed
php artisan storage:link

# Levantar servidor de desarrollo
php artisan serve          # http://localhost:8000

# Compilar assets frontend
npm run dev                # desarrollo con watch: npm run watch
npm run prod               # producción minificada

# Base de datos
php artisan migrate
php artisan db:seed

# Limpiar caché (también disponible en el navegador: GET /admin/clear-cache)
php artisan optimize:clear

# Tests
php artisan test
./vendor/bin/phpunit tests/Feature/ExampleTest.php   # un solo archivo
```

---

## Descripción general del sistema

**SYSALMACEN** es un sistema de gestión de almacenes desarrollado en **Laravel 8** para una entidad del gobierno boliviano (**GOBE**). Gestiona inventario de materiales, ingresos por compra, egresos (salidas), donaciones, solicitudes de pedido entre unidades y reportes anuales. Maneja múltiples almacenes simultáneamente (llamados **sucursales**).

El panel administrativo usa **TCG Voyager** (basado en Laravel), por lo que la URL base de la aplicación es `/admin`.

---

## Arquitectura de tres bases de datos

Este es el aspecto más crítico del sistema. Hay **tres conexiones MySQL** definidas en [config/database.php](config/database.php):

| Conexión | Base de datos | Propósito |
|---|---|---|
| `mysql` (por defecto) | `sysalmacen` | Todos los datos propios de la app (almacenes, facturas, egresos, etc.) |
| `mamore` | BD externa (`sysadmin` o similar) | Datos de personal: `people`, `contracts`, `direcciones`, `unidades` |
| `mysqlgobe` | `sysadmin` | Tabla `cargo` (descripción del puesto del funcionario) |

Las variables de entorno correspondientes en `.env`:
- `DB_*` → conexión `mysql` (sysalmacen)
- `DB_HOST_GOBE`, `DB_DATABASE_GOBE`, etc. → conexión `mysqlgobe`
- `DB_HOST_MAMORE`, `DB_DATABASE_MAMORE`, etc. → conexión `mamore` (no aparece en `.env.example` pero sí se usa)

El controlador base [app/Http/Controllers/Controller.php](app/Http/Controllers/Controller.php) expone los métodos compartidos que hacen consultas cross-database:

- `getPeople($id)` — datos completos del funcionario (nombre, dirección, unidad, cargo) cruzando `mamore` + `mysqlgobe`
- `getWorker($id)` — similar a `getPeople` pero también busca en `people_exts` si no encuentra contrato firmado
- `getDirecciones()` — lista de direcciones administrativas desde `mamore`
- `getDireccion($id)` — una dirección específica
- `getUnidades($id)` — unidades de una dirección desde `mamore`
- `getDireccionSucursal($id)` — direcciones asignadas a un almacén
- `getGestione($id)` — gestiones de inventario de un almacén

Todos los controladores extienden este `Controller.php`.

---

## Autenticación y middleware

- El login redirige a `/admin/login` (Voyager). No hay login propio.
- `User` extiende `TCG\Voyager\Models\User`. Los roles y permisos son manejados por Voyager.
- El middleware personalizado `loggin` ([app/Http/Middleware/Loggin.php](app/Http/Middleware/Loggin.php)) se ejecuta en **todas las rutas del grupo `/admin`** y hace las siguientes verificaciones en orden:

  1. Si el sistema está en modo mantenimiento (`configuracion.maintenance`) y el usuario no es `admin` → redirige a `/maintenance`
  2. Si el usuario no tiene `sucursal_id` asignada y no es `admin` ni `almacen_admin` → redirige a `/error`
  3. Si el usuario no tiene `unidadAdministrativa_id` o `direccionAdministrativa_id` asignados y no es `admin` → redirige a `/contact`
  4. Si el `funcionario_id` del usuario no resuelve a un contrato activo en `mamore` y no es `admin` → redirige a `/notpeople`
  5. Registra todas las peticiones HTTP en `storage/logs/requests.log`

**Roles existentes en el sistema:**
- `admin` — acceso total, salta todas las restricciones del middleware
- `almacen_admin` — administrador de almacén, salta algunas restricciones
- Roles de usuario normal — deben tener sucursal, dirección y unidad asignadas

---

## Modelo de dominio

### Estructura de almacén (Sucursal)

```
Sucursal (almacén físico)
 ├── SucursalDireccion       → vincula la sucursal con Direcciones Administrativas (BD mamore)
 ├── SucursalUnidadPrincipal → unidad(es) administrativas principales del almacén (máx. 2)
 └── SucursalSubAlmacen      → sub-almacenes dentro de la sucursal
```

Cada `User` tiene: `sucursal_id`, `subSucursal_id`, `direccionAdministrativa_id`, `unidadAdministrativa_id`, `funcionario_id`.

### Flujo de Ingreso (compras)

```
SolicitudCompra (cabecera del ingreso)
  → Factura (datos de la factura: proveedor, monto, número, autorización)
    → DetalleFactura (línea por artículo: article_id, cantsolicitada, cantrestante, precio)
```

- `SolicitudCompra.stock` = 1 si aún hay stock disponible, 0 si todo fue egresado
- `SolicitudCompra.condicion` = 1 si activa, 0 si cerrada
- `DetalleFactura.cantrestante` es el campo **crítico** de stock: se decrementa con cada egreso y se incrementa si se anula un egreso
- `DetalleFactura.hist` = 0 para registros activos de la gestión corriente, `hist` = 1 para registros históricos (copiados al cerrar gestión)
- El campo `condicion` de `DetalleFactura` indica si el artículo tiene stock disponible (1) o agotado (0)

### Flujo de Egreso

**Tipo 1 — Egreso directo:**
```
SolicitudEgreso (cabecera)
  → DetalleEgreso (detallefactura_id, cantsolicitada, precio)
```
Al guardar un egreso directo, se llama a `DetalleFactura::decrement('cantrestante', $cantidad)`.

**Tipo 2 — Egreso por solicitud de pedido (flujo completo):**
```
SolicitudPedido (creada por el funcionario desde Outbox)
  → SolicitudPedidoDetalle (artículos solicitados)
       ↓ aprobada en Inbox
  → SolicitudEgreso (generada al entregar)
    → DetalleEgreso
```
Ver ciclo de estados de `SolicitudPedido` más abajo.

### Ciclo de estados de SolicitudPedido

```
Pendiente → Enviado → Aprobado → Entregado
                ↘ Rechazado
Entregado → pendienteeliminacion → eliminado (si el almacén confirma la anulación)
         ↗ cancelarEliminacion (si se cancela la solicitud de anulación)
```

Los `SolicitudPedidoDetalle` usan el campo `jsonDetails_id` (JSON) para guardar temporalmente la selección de items de `DetalleFactura` que se entregarán, con `cantentregada`.

### Inventario anual (Gestiones)

```
InventarioAlmacen
  - gestion: año (ej. 2024)
  - status: 1=abierta, 0=cerrada
  - start/finish: fechas de apertura y cierre
```

**Al cerrar una gestión** (`InventarioAlmacenController::finish`): todos los `DetalleFactura` con `cantrestante > 0` se **copian** como nuevos registros con `hist=1`, `gestion=año+1` y `parent_id` apuntando al original. Esto traspasa el saldo al próximo año.

**Al reabrir una gestión** (`reabrir`): se eliminan (soft delete) esos registros históricos `hist=1` generados, guardando un historial en `HistInvDelete`.

Solo puede haber **una gestión activa** por sucursal a la vez. Sin gestión activa, no se pueden registrar ingresos ni egresos.

### Módulo de Donaciones (SEDEGES)

Flujo paralelo e independiente del flujo de compras:

```
DonacionIngreso (cabecera de ingreso por donación)
  → DonacionIngresoDetalle (DonacionArticulo, cantidad, categoría)
  → DonacionArchivo (archivos adjuntos)

Donadores: DonadorPersona / DonadorEmpresa
Categorías: DonacionCategoria
Centros de acogida: Centro / CentroCategoria

DonacionEgreso (salida de artículos donados)
  → DonacionEgresoDetalle
```

Las solicitudes de donación entre unidades usan `DonacionSolicitudController` y `DonationStockController`.

---

## Controladores principales

| Controlador | Ruta | Descripción |
|---|---|---|
| `IncomeController` | `/admin/income` | CRUD de ingresos (SolicitudCompra + Factura + DetalleFactura) |
| `EgressController` | `/admin/egres` | CRUD de egresos directos + entrega de solicitudes aprobadas |
| `SolicitudPedidoController` | `/admin/outbox` | Creación y seguimiento de solicitudes por el funcionario |
| `SolicitudBandejaController` | `/admin/inbox` | Revisión y aprobación/rechazo de solicitudes por el almacén |
| `InventarioAlmacenController` | `/admin/inventory/{id}` | Gestión del inventario anual por sucursal |
| `SucursalController` | `/admin/sucursals` | Configuración de sucursales, DA, unidades principales y sub-almacenes |
| `ReportAlmacenController` | `/admin/print/...` | Generación de reportes (Excel + impresión) |
| `UserController` | `/admin/register-users` | Registro y actualización de usuarios del sistema |
| `IncomeDonorController` | `/admin/incomedonor` | Ingresos por donación |
| `EgressDonorController` | `/admin/egressdonor` | Egresos de donaciones |
| `ArticleController` | `/admin/articles` | CRUD de artículos (vía Voyager + listados AJAX) |
| `ProviderController` | `/admin/providers` | Listado de proveedores |
| `PeopleExtController` | `/admin/people_ext` | Personas externas sin contrato en `mamore` |
| `ExistingProductController` | `/admin/existingproducts` | Vista de productos en existencia |
| `NotificationController` | notificación AJAX | Alerta de donaciones por vencer |
| `MaintenanceController` | páginas de error | Vistas de mantenimiento, error, contacto, notpeople |

---

## Rutas AJAX — patrón estándar

Las vistas cargan datos dinámicos mediante AJAX. El patrón consistente es:

```
GET /admin/<recurso>/ajax/list/{type}/{search?}   → retorna vista parcial con paginación
GET /admin/ajax/get/<dato>/{id?}                  → retorna JSON
```

Ejemplos importantes:
- `GET /admin/income/ajax/list/{type}/{search?}` — tipos: `todo`, `constock`, `sinstock`
- `GET /admin/egres/ajax/list/{type}/{search?}` — tipos: `egreso`, `solicitud`
- `GET /admin/outbox/ajax/list` — con query params `type` y `search`
- `GET /admin/inbox/ajax/list/{type}/{search?}` — tipos: `pendiente`, `aprobado`, `entregado`, `rechazado`, `todo`
- `GET /admin/egres/ajax/articleunidad/{unidad}/{article}` — artículos de una unidad para egresar
- `GET /admin/egres/ajax/articlealmacen/{article}/{unidad_id}/{unidad1}/{unidad2}` — artículos del almacén central
- `GET /admin/outbox/article/stock/ajax` — búsqueda de artículos disponibles para pedido
- `GET /admin/ajax/get/direccionsucursal/{id}` — direcciones de una sucursal
- `GET /admin/ajax/get/subsucursal/{id}` — sub-almacenes de una sucursal
- `GET /admin/ajax/get/unidadDirection/{id}` — unidades de una dirección (desde `mamore`)

---

## Lógica de stock — reglas críticas

**Al registrar un egreso**, en cada `DetalleFactura` afectado:
1. Se hace `decrement('cantrestante', $cantidad)`
2. Si `cantrestante` llega a 0 → `condicion = 0` (agotado)
3. Si todos los `DetalleFactura` de la `Factura` tienen `cantrestante = 0` → `SolicitudCompra.condicion = 0` y `stock = 0`

**Al eliminar/anular un egreso**, se revierten los decrementos:
1. Se hace `increment('cantrestante', $cantidad)` en cada `DetalleFactura`
2. Si `cantrestante > 0` → `condicion = 1`
3. Si algún `DetalleFactura` del ingreso recupera stock → `SolicitudCompra.stock = 1`
4. Si todos vuelven a su cantidad original → `SolicitudCompra.condicion = 1`

**Comportamiento especial de sucursales con IDs hardcodeados:**
En `EgressController::ajax_solicitud_compra`, las sucursales con `id=1`, `id=13` e `id=6` tienen unidades administrativas "aliadas" hardcodeadas (IDs 192, 221 y 304 respectivamente), que se incluyen en las consultas de stock disponible. Esto es una configuración específica del entorno de producción GOBE.

---

## Frontend

- **Blade + Voyager**: las vistas están en [resources/views/almacenes/](resources/views/almacenes/) y [resources/views/donacion-sedeges/](resources/views/donacion-sedeges/)
- **Vue 2**: compilado via Laravel Mix. Archivos fuente en [resources/js/](resources/js/), compilado a `public/js/app.js`
- **Scripts standalone** (NO compilados por Mix):
  - `public/js/egreso.js` — lógica del formulario de egresos
  - `public/js/main.js` — utilidades generales
  - `public/js/vue.js` — Vue 2 directo para vistas que lo necesiten
- **Select2**: `public/js/select2.min.js` — usado para búsquedas tipo AJAX en selects (proveedores, funcionarios, artículos)
- **DataTables**: para las tablas de listados
- **Laravel Echo + Socket.io**: configurado en [laravel-echo-server.json](laravel-echo-server.json) para notificaciones en tiempo real (alertas de donaciones por caducar)
- **Toastr** (`brian2694/laravel-toastr`): notificaciones flash, siempre con `['message' => '...', 'alert-type' => 'success|error|warning|info']`
- **Template de impresión**: [resources/views/layouts/template-print-alt.blade.php](resources/views/layouts/template-print-alt.blade.php) para reportes imprimibles

### Patrón de vistas por módulo

Cada módulo tiene típicamente:
- `browse.blade.php` — vista principal con contenedor de lista
- `list.blade.php` — tabla paginada cargada por AJAX
- `add.blade.php` / `edit-add.blade.php` — formulario de creación/edición
- `edit.blade.php` — formulario de edición
- `report.blade.php` — reporte para imprimir
- `read.blade.php` — vista de solo lectura

---

## Reportes y exportaciones

Todos los reportes están en `ReportAlmacenController`. Cada reporte tiene 3 rutas:
1. `GET /admin/print/<nombre>` — renderiza la vista del formulario de filtros
2. `POST /admin/print/<nombre>/list` — retorna los datos filtrados (vista de tabla)
3. Desde la vista `list`, botones para **imprimir** (PDF via dompdf o ventana del navegador) y **exportar a Excel**

**Clases de exportación** en [app/Exports/](app/Exports/):
- `AnualDaExport` — inventario anual por dirección administrativa
- `AnualPartidaExport` — inventario anual por partida presupuestaria
- `AnualDetalleExport` — inventario anual detalle general
- `ArticleStockExport` — stock de artículos
- `ArticleListExport` — listado de artículos
- `ArticleIncomeOfficeExport` — ingresos por oficina
- `ArticleEgressOfficeExport` — egresos por oficina
- `ProviderListExport` — listado de proveedores

---

## Voyager (panel administración)

- **FormFields personalizados**: [app/FormFields/DireccionAdministrativaFormField.php](app/FormFields/DireccionAdministrativaFormField.php) y [SucursalFormField.php](app/FormFields/SucursalFormField.php) — campos custom para el BREAD de Voyager
- **Vistas sobreescritas de Voyager**: en [resources/views/vendor/voyager/](resources/views/vendor/voyager/) — navbar, sidebar, login, master layout, formularios de usuario
- **Configuración Voyager**: [config/voyager.php](config/voyager.php) y [config/hooks.php](config/hooks.php)
- Los seeders de Voyager (`VoyagerDatabaseSeeder`, `DataTypesTableSeeder`, `DataRowsTableSeeder`, `MenusTableSeeder`, etc.) configuran el panel: menús, BREAD, roles y permisos

---

## Modelos y relaciones clave

| Modelo | Tabla | Relaciones principales |
|---|---|---|
| `User` | `users` | extiende Voyager User; pertenece a `Sucursal`, `Direction`, `Unit` |
| `Sucursal` | `sucursals` | hasMany `SucursalDireccion`, `SucursalUser` |
| `SucursalSubAlmacen` | `sucursal_sub_almacens` | belongsTo `Sucursal` |
| `SucursalUnidadPrincipal` | `sucursal_unidad_principals` | unidades principales del almacén |
| `Article` | `articles` | belongsTo `Partida`, `Sucursal` |
| `Partida` | `partidas` | partidas presupuestarias (código tipo `3.x.x`) |
| `Provider` | `providers` | proveedores de compra |
| `Modality` | `modalities` | modalidades de compra |
| `SolicitudCompra` | `solicitud_compras` | hasMany `Factura`; pertenece a `Sucursal`, `Modality` |
| `Factura` | `facturas` | belongsTo `SolicitudCompra`, `Provider`; hasMany `DetalleFactura` |
| `DetalleFactura` | `detalle_facturas` | belongsTo `Factura`, `Article`; campo clave: `cantrestante` |
| `SolicitudEgreso` | `solicitud_egresos` | hasMany `DetalleEgreso` |
| `DetalleEgreso` | `detalle_egresos` | belongsTo `SolicitudEgreso`, `DetalleFactura` |
| `SolicitudPedido` | `solicitud_pedidos` | hasMany `SolicitudPedidoDetalle`; estados: Pendiente/Enviado/Aprobado/Entregado/Rechazado/eliminado/pendienteeliminacion |
| `SolicitudPedidoDetalle` | `solicitud_pedido_detalles` | belongsTo `SolicitudPedido`; campo `jsonDetails_id` guarda la selección de DetalleFactura |
| `InventarioAlmacen` | `inventario_almacens` | gestión anual por sucursal; `status=1` = abierta |
| `HistInvDelete` | `hist_inv_deletes` | historial de reaperturas de gestión |
| `Direction` | `directions` | espejo local de direcciones (también en `mamore`) |
| `Unit` | `units` | espejo local de unidades administrativas |
| `PeopleExt` | `people_exts` | personas externas sin contrato activo en `mamore` |

**Modelos de Donación:**

| Modelo | Tabla | Descripción |
|---|---|---|
| `DonacionIngreso` | `donacion_ingresos` | cabecera de ingreso por donación |
| `DonacionIngresoDetalle` | `donacion_ingreso_detalles` | detalle de artículos donados recibidos |
| `DonacionEgreso` | `donacion_egresos` | salida de artículos donados |
| `DonacionEgresoDetalle` | `donacion_egreso_detalles` | detalle de salida |
| `DonacionArticulo` | `donacion_articulos` | catálogo de artículos de donación |
| `DonacionCategoria` | `donacion_categorias` | categorías de artículos donados |
| `DonadorPersona` / `DonadorEmpresa` | respectivas | donantes |
| `DonacionArchivo` | `donacion_archivos` | documentos adjuntos a donaciones |
| `Centro` / `CentroCategoria` | respectivas | centros de acogida |

---

## Patrones y convenciones del código

### Transacciones de base de datos
Toda operación de escritura importante usa `DB::beginTransaction()` / `DB::commit()` / `DB::rollBack()`. Siempre dentro de un `try/catch`.

### Soft deletes
No se usa `SoftDeletes` de Eloquent. En cambio, se actualiza manualmente `deleted_at = Carbon::now()` y `deleteuser_id`. Para consultar registros activos siempre filtrar con `->where('deleted_at', null)`.

### Numeración automática de solicitudes
El número de solicitud/pedido se genera con el formato `SIGLA-0001/GESTION`:
```php
$format = "%04d";
$nro = strtoupper($unidad->sigla) . '-' . sprintf($format, $count+1) . '/' . $gestion->gestion;
```

### Filtros por sucursal
El `query_filter` se construye dinámicamente:
- Usuario normal: `sucursal_id = X` (o `sucursal_id = X and subSucursal_id = Y`)
- Admin: `1` (sin filtro, ve todo)

### Consultas raw con búsqueda
Las búsquedas usan `->whereRaw("campo like '%$search%'")` directamente. Tener en cuenta que hay interpolación directa de variables en SQL (inyección potencial, código heredado).

### Mantenimiento del código
Hay **mucho código comentado** a lo largo de los controladores. Este código es histórico de versiones anteriores del flujo y no debe eliminarse sin confirmación del desarrollador, ya que documenta lógica anterior.

---

## Reporte: Usuarios por Dirección Administrativa

**Agregado:** Nuevo reporte que muestra, dado un almacén, todas sus Direcciones Administrativas con sus Unidades y los usuarios asignados a cada unidad (muestra también unidades sin usuarios).

| Archivo | Ruta |
|---|---|
| Controlador | [app/Http/Controllers/ReportUsuariosDireccionController.php](app/Http/Controllers/ReportUsuariosDireccionController.php) |
| Vista formulario | [resources/views/almacenes/report/aditional/usuariosDireccion/report.blade.php](resources/views/almacenes/report/aditional/usuariosDireccion/report.blade.php) |
| Vista lista (AJAX) | [resources/views/almacenes/report/aditional/usuariosDireccion/list.blade.php](resources/views/almacenes/report/aditional/usuariosDireccion/list.blade.php) |
| Vista impresión | [resources/views/almacenes/report/aditional/usuariosDireccion/print.blade.php](resources/views/almacenes/report/aditional/usuariosDireccion/print.blade.php) |

Rutas:
- `GET  /admin/print/almacen-usuarios-direccion` → `almacen-usuarios-direccion.report`
- `POST /admin/print/almacen/usuarios/direccion/list` → `almacen-usuarios-direccion.list`

**Flujo de datos:**
1. Obtiene las `sucursal_direccions` del almacén seleccionado → `mamore.direcciones`
2. Por cada dirección obtiene sus `mamore.unidades`
3. Por cada unidad obtiene los `sysalmacen.users` con `unidadAdministrativa_id` coincidente, cruzando con `mamore.people` para CI y nombre completo
4. Si una unidad no tiene usuarios la muestra igual con el texto "Sin usuarios asignados"

---

## Auditoría

`owen-it/laravel-auditing` está instalado. Los modelos que requieran registro de cambios deben usar el trait `ImplementsAuditable` y la interfaz `Auditable`. La tabla de auditoría se crea con la migración `2022_04_11_132719_create_audits_table.php`.

---

## Variables de entorno relevantes

```
APP_COLOR      — color del tema (#5EAF4A por defecto)
APP_VERSION    — versión mostrada en la UI
APP_DEMO       — modo demo (true/false)
DB_*           — conexión principal sysalmacen
DB_HOST_GOBE / DB_DATABASE_GOBE / DB_USERNAME_GOBE / DB_PASSWORD_GOBE  — conexión mysqlgobe
# La conexión 'mamore' también necesita variables (no están en .env.example pero sí en config/database.php):
# DB_HOST_MAMORE / DB_DATABASE_MAMORE / DB_USERNAME_MAMORE / DB_PASSWORD_MAMORE
```

---

## Notas importantes para el desarrollo

- **Sin gestión activa no funciona nada**: antes de registrar ingresos/egresos/solicitudes, el almacén debe tener una `InventarioAlmacen` con `status=1`. El sistema lo verifica en cada create/store.
- **La BD `mamore` debe estar disponible**: el middleware `loggin` valida el funcionario contra `mamore` en cada petición. Si `mamore` cae, los usuarios no admin no pueden entrar.
- **`subSucursal_id` es requerido en la mayoría de operaciones**: el usuario debe tener un sub-almacén asignado.
- **`SolicitudPedidoDetalle.jsonDetails_id`**: es un JSON con la estructura `{almacen: [...], detalle_id: [...], cantidad: [...]}`. Se usa temporalmente mientras el almacenero selecciona qué items de `DetalleFactura` usar para entregar la solicitud.
- **Partidas presupuestarias**: los artículos pertenecen a una `Partida` (código presupuestario boliviano tipo `30000`, `30100`, etc.). Esto es relevante para los reportes anuales.
- **`hist` en DetalleFactura**: `hist=0` son los registros activos de la gestión corriente. `hist=1` son copias históricas creadas al cierre de gestión para traspasar stock al año siguiente. Solo los registros `hist=0` participan en cálculos de stock en tiempo real.
