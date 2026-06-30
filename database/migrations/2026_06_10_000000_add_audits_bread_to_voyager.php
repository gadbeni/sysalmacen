<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use TCG\Voyager\Models\Permission;

class AddAuditsBreadToVoyager extends Migration
{
    public function up()
    {
        $now = now();

        $dataTypeId = DB::table('data_types')->where('slug', 'audits')->value('id');

        if (!$dataTypeId) {
            $dataTypeId = DB::table('data_types')->insertGetId([
                'name' => 'audits',
                'slug' => 'audits',
                'display_name_singular' => 'Bitácora',
                'display_name_plural' => 'Bitácora',
                'icon' => 'voyager-list',
                'model_name' => 'OwenIt\\Auditing\\Models\\Audit',
                'policy_name' => null,
                'controller' => null,
                'description' => 'Registro de auditoría del sistema',
                'generate_permissions' => 1,
                'server_side' => 1,
                'details' => json_encode([
                    'order_column' => 'created_at',
                    'order_display_column' => 'created_at',
                    'order_direction' => 'desc',
                    'default_search_key' => 'event',
                    'scope' => null,
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $rows = [
            ['field' => 'id', 'type' => 'number', 'display_name' => 'ID', 'browse' => 1, 'read' => 1],
            ['field' => 'user_type', 'type' => 'text', 'display_name' => 'Tipo de usuario', 'browse' => 0, 'read' => 1],
            ['field' => 'user_id', 'type' => 'number', 'display_name' => 'Usuario ID', 'browse' => 1, 'read' => 1],
            ['field' => 'event', 'type' => 'text', 'display_name' => 'Acción', 'browse' => 1, 'read' => 1],
            ['field' => 'auditable_type', 'type' => 'text', 'display_name' => 'Modelo', 'browse' => 1, 'read' => 1],
            ['field' => 'auditable_id', 'type' => 'number', 'display_name' => 'Registro ID', 'browse' => 1, 'read' => 1],
            ['field' => 'old_values', 'type' => 'text_area', 'display_name' => 'Valores anteriores', 'browse' => 0, 'read' => 1],
            ['field' => 'new_values', 'type' => 'text_area', 'display_name' => 'Valores nuevos', 'browse' => 0, 'read' => 1],
            ['field' => 'url', 'type' => 'text_area', 'display_name' => 'URL', 'browse' => 0, 'read' => 1],
            ['field' => 'ip_address', 'type' => 'text', 'display_name' => 'IP', 'browse' => 1, 'read' => 1],
            ['field' => 'user_agent', 'type' => 'text_area', 'display_name' => 'Navegador', 'browse' => 0, 'read' => 1],
            ['field' => 'tags', 'type' => 'text', 'display_name' => 'Etiquetas', 'browse' => 0, 'read' => 1],
            ['field' => 'created_at', 'type' => 'timestamp', 'display_name' => 'Fecha', 'browse' => 1, 'read' => 1],
            ['field' => 'updated_at', 'type' => 'timestamp', 'display_name' => 'Actualizado', 'browse' => 0, 'read' => 1],
        ];

        foreach ($rows as $order => $row) {
            DB::table('data_rows')->updateOrInsert(
                ['data_type_id' => $dataTypeId, 'field' => $row['field']],
                [
                    'type' => $row['type'],
                    'display_name' => $row['display_name'],
                    'required' => 0,
                    'browse' => $row['browse'],
                    'read' => $row['read'],
                    'edit' => 0,
                    'add' => 0,
                    'delete' => 0,
                    'details' => '{}',
                    'order' => $order + 1,
                ]
            );
        }

        $adminMenuId = DB::table('menus')->where('name', 'admin')->value('id') ?: 1;
        $toolsParentId = DB::table('menu_items')
            ->where('menu_id', $adminMenuId)
            ->where('title', 'Herramientas')
            ->value('id');

        DB::table('menu_items')->updateOrInsert(
            ['route' => 'voyager.audits.index'],
            [
                'menu_id' => $adminMenuId,
                'title' => 'Bitácora',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'voyager-list',
                'color' => null,
                'parent_id' => $toolsParentId,
                'order' => 99,
                'parameters' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        $permissionIds = collect(['browse_audits', 'read_audits'])
            ->map(fn ($key) => Permission::firstOrCreate([
                'key' => $key,
                'table_name' => 'audits',
            ])->id);

        DB::table('roles')
            ->where('name', 'admin')
            ->orWhere('id', 1)
            ->pluck('id')
            ->each(function ($roleId) use ($permissionIds) {
                $permissionIds->each(function ($permissionId) use ($roleId) {
                    DB::table('permission_role')->updateOrInsert([
                        'permission_id' => $permissionId,
                        'role_id' => $roleId,
                    ]);
                });
            });
    }

    public function down()
    {
        $permissionIds = DB::table('permissions')
            ->where('table_name', 'audits')
            ->pluck('id');

        DB::table('permission_role')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('permissions')->whereIn('id', $permissionIds)->delete();
        DB::table('menu_items')->where('route', 'voyager.audits.index')->delete();

        $dataTypeId = DB::table('data_types')->where('slug', 'audits')->value('id');

        if ($dataTypeId) {
            DB::table('data_rows')->where('data_type_id', $dataTypeId)->delete();
            DB::table('data_types')->where('id', $dataTypeId)->delete();
        }
    }
}
