@php
    $isPasswordChangePage = request()->routeIs('sessions');
@endphp

<div class="modal modal-warning fade" tabindex="-1" id="modal-change-password" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                @if($isPasswordChangePage)
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                @endif
                <h4 class="modal-title"><i class="voyager-lock"></i> Actualice su contrasena</h4>
            </div>
            <div class="modal-body text-center">
                <div style="margin: 10px auto 20px;">
                    <span style="display:inline-block; width:90px; height:90px; line-height:90px; border-radius:50%; background:#fff3cd; color:#f0ad4e;">
                        <i class="voyager-lock" style="font-size:44px; line-height:90px;"></i>
                    </span>
                </div>
                <h4 style="margin-top:0;">Hola, {{ ucwords(auth()->user()->name) }}</h4>
                <p class="text-muted" style="font-size:15px; max-width:420px; margin:10px auto 0;">
                    @if($isPasswordChangePage)
                        Para continuar, escriba una nueva contrasena y guarde los cambios.
                    @else
                        Debe cambiar su contrasena antes de continuar usando el sistema.
                    @endif
                </p>
            </div>
            <div class="modal-footer" style="text-align:center;">
                @if($isPasswordChangePage)
                    <button type="button" class="btn btn-warning" data-dismiss="modal">
                        <i class="voyager-edit"></i> Entendido
                    </button>
                @else
                    <a href="{{ route('sessions') }}" class="btn btn-warning">
                        <i class="voyager-edit"></i> Cambiar ahora
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

@push('javascript')
    <script>
        $(document).ready(function () {
            $('#modal-change-password').modal({
                backdrop: 'static',
                keyboard: false,
                show: true
            });
        });
    </script>
@endpush
