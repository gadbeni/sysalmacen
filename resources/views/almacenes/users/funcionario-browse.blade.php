@php
    // Voyager incluye esta vista en dos contextos:
    // - browse: hereda $data (fila del @foreach del listado)
    // - read (ver usuario): no existe $data, el registro llega en $dataTypeContent
    $usuario = $data ?? $dataTypeContent ?? null;
@endphp
@if($usuario)
<div style="display:flex; align-items:center; gap:10px;">
    <img src="{{ $usuario->photo_url }}" style="width:40px; height:40px; border-radius:50%; object-fit:cover; flex-shrink:0;">
    <div>
        <strong>{{ $content }}</strong>
        <br>
        <small class="text-muted">{{ $usuario->email }}</small>
    </div>
</div>
@else
    {{ $content ?? '' }}
@endif
