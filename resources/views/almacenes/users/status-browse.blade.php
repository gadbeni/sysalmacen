@php
    $user = $data ?? $dataTypeContent;
@endphp

<div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
    @if($user->status)
        <span class="label label-success">Activo</span>
    @else
        <span class="label label-danger">Inactivo</span>
    @endif

</div>
