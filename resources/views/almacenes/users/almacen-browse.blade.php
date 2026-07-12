@php
    // browse: hereda $data (fila del listado); read (ver usuario): llega $dataTypeContent
    $usuario = $data ?? $dataTypeContent ?? null;
    $sucursal = $usuario ? \App\Models\Sucursal::find($usuario->sucursal_id) : null;
    $sub = $usuario ? \App\Models\SucursalSubAlmacen::find($usuario->subSucursal_id) : null;
@endphp
<div>
    <small>{{ $sucursal ? $sucursal->nombre : '—' }}</small>
    <br>
    <small class="text-muted">{{ $sub ? $sub->name : '—' }}</small>
</div>
