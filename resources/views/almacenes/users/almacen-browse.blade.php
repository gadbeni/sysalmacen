@php
    $sucursal = \App\Models\Sucursal::find($data->sucursal_id);
    $sub = \App\Models\SucursalSubAlmacen::find($data->subSucursal_id);
@endphp
<div>
    <small>{{ $sucursal ? $sucursal->nombre : '—' }}</small>
    <br>
    <small class="text-muted">{{ $sub ? $sub->name : '—' }}</small>
</div>
