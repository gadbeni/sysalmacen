@php
    $user = $data ?? $dataTypeContent;
@endphp

<div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
    @if($user->status)
        <span class="label label-success">Activo</span>
    @else
        <span class="label label-danger">Inactivo</span>
    @endif

    @if(auth()->user()->hasPermission('edit_users') && auth()->id() !== $user->id)
        <a
            href="{{ route('users.toggle-status', $user->id) }}"
            class="btn btn-xs {{ $user->status ? 'btn-warning' : 'btn-success' }}"
            title="{{ $user->status ? 'Desactivar usuario' : 'Activar usuario' }}">
            <i class="{{ $user->status ? 'voyager-power' : 'voyager-check' }}"></i>
            {{ $user->status ? 'Desactivar' : 'Activar' }}
        </a>
    @endif
</div>
