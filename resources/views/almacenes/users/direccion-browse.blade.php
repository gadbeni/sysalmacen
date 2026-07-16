@php
    // browse: hereda $data (fila del listado); read (ver usuario): llega $dataTypeContent
    $usuario = $data ?? $dataTypeContent ?? null;
@endphp
<div>
    <small>{{ optional(optional($usuario)->direction)->nombre ?? '—' }}</small>
    <br>
    <small class="text-muted">{{ optional(optional($usuario)->unit)->nombre ?? '—' }}</small>
</div>
