<div style="display:flex; align-items:center; gap:10px;">
    <img src="{{ \Voyager::image($data->avatar) }}" style="width:40px; height:40px; border-radius:50%; object-fit:cover; flex-shrink:0;">
    <div>
        <strong>{{ $content }}</strong>
        <br>
        <small class="text-muted">{{ $data->email }}</small>
    </div>
</div>
