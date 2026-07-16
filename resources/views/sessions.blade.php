@extends('voyager::master')
@section('page_title', 'Seguridad de usuario')
@section('page_header')
    <div class="container-fluid">
        <div class="row">
            <h1 class="page-title">
                <i class="voyager-lock"></i>Seguridad - Usuario
            </h1>
        </div>

    </div>
@endsection
@section('content')
<div class="page-content browse container-fluid">
    <div class="row">
        <div class="col-lg-7">
            @php
                $u = Auth::user();
                $foto = $u->photo_url;
            @endphp
            <div class="profile-card">
                <div class="profile-card__cover">
                    <form id="photo-form" action="{{ route('update_photo') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <label class="profile-card__avatar-wrap" title="Cambiar foto">
                            <img class="profile-card__avatar" id="avatar-preview" src="{{ $foto }}" alt="Foto de {{ $u->name }}">
                            <span class="profile-card__avatar-overlay"><i class="fa fa-camera"></i></span>
                            <input type="file" name="photo" id="photo-input" class="photo-input-hidden" accept="image/jpeg,image/png,image/webp" hidden>
                        </label>
                    </form>
                </div>
                <div class="profile-card__head">
                    <h3 class="profile-card__name">{{ ucwords($u->name) }}</h3>
                    <span class="profile-card__mail"><i class="fa fa-envelope"></i> {{ $u->email }}</span>
                    @if ($u->avatar && $u->avatar !== 'users/default.png')
                    <div class="profile-card__actions">
                        <button type="button" class="btn btn-link profile-card__remove" data-toggle="modal" data-target="#ModalQuitarFoto">
                            <i class="fa fa-trash"></i> Quitar foto
                        </button>
                    </div>
                    @endif
                </div>
                <div class="profile-grid">
                    @if ($u->direction)
                    <div class="profile-item">
                        <span class="profile-item__icon"><i class="fa fa-building"></i></span>
                        <div>
                            <span class="profile-item__label">Dirección Administrativa</span>
                            <span class="profile-item__value">{{ $u->direction->nombre }}</span>
                        </div>
                    </div>
                    @endif
                    @if ($u->unit)
                    <div class="profile-item">
                        <span class="profile-item__icon"><i class="fa fa-sitemap"></i></span>
                        <div>
                            <span class="profile-item__label">Unidad Administrativa</span>
                            <span class="profile-item__value">{{ $u->unit->nombre }}</span>
                        </div>
                    </div>
                    @endif
                    @if ($u->sucursal)
                    <div class="profile-item">
                        <span class="profile-item__icon"><i class="fa fa-archive"></i></span>
                        <div>
                            <span class="profile-item__label">Almacén</span>
                            <span class="profile-item__value">{{ $u->sucursal->nombre }}</span>
                        </div>
                    </div>
                    @endif
                    @if ($u->subAlmacen)
                    <div class="profile-item">
                        <span class="profile-item__icon"><i class="fa fa-inbox"></i></span>
                        <div>
                            <span class="profile-item__label">Sub-almacén</span>
                            <span class="profile-item__value">{{ $u->subAlmacen->name }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <h2 class="page-title">
                        <i class="fa fa-id-card"></i>Cambio de contraseña
                    </h2>
                </div>
                <div class="panel-body">
                    
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form id="change-password-form" action="{{route('change_password')}}" method="post">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="password">Nueva Contraseña:</label>
                            <div class="password-field">
                                <input type="password" name="password" class="form-control" id="password" required>
                                <span class="password-toggle-wrap">
                                    <button class="password-toggle" type="button" id="toggle-password" title="Mostrar/ocultar contraseña">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                </span>
                            </div>
                        </div>
                        <input type="submit" class="btn btn-success" value="Cambiar contraseña">
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="panel panel-bordered">
                <div class="panel-body">
                    <hr>
                    <div>
                        <h2 class="page-title">
                            <i class="fa fa-laptop"></i>Sesiones activas
                        </h2>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col" style="background-color: #2ecc71;color:aliceblue">
                                            Información
                                        </th>
                                        <th scope="col" style="background-color: #2ecc71;color:aliceblue">
                                            Dirección IP
                                        </th>
                                        <th scope="col" style="background-color: #2ecc71;color:aliceblue">
                                            Ultima Actividad
                                        </th>
                                        <th scope="col" style="background-color: #2ecc71;color:aliceblue">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sessions as $session)
                                    <tr>
                                        <td>{{ $session->user_agent }}</td>
                                        <td>{{ $session->ip_address }}</td>
                                        <td>{{ \Carbon\Carbon::createFromTimeStamp($session->last_activity)->diffForhumans() }}</td>
                                        <td class="text-actual">
                                            @if ($session->id == session()->getId())
                                                <span class="lbl-session">Actual</span>
                                            @else
                                                {{-- <form method="POST" action="{{route('delete_session')}}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="id" value="{{ $session->id }}">
                                                    <button type="submit" class="btn btn-danger">Eliminar</button>
                                                </form> --}}
                                                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#ModalEliminar" data-id="{{ $session->id }}">Eliminar</button>
                                            @endif
                                            
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal modal-danger fade" tabindex="-1" id="ModalEliminar" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            {!! Form::open(['route' => 'delete_session', 'method' => 'post']) !!}
            @method('DELETE')
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><i class="voyager-trash"></i> - Desea eliminar la sesion?</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="id">

                <div class="text-center" style="text-transform:uppercase">
                    <i class="voyager-trash" style="color: red; font-size: 5em;"></i>
                    <br>
                    
                    <p><b>Se cerrara la sesion en el dispositivo correspondiente</b></p>
                </div>
            </div>                
            <div class="modal-footer">
                
                    <input type="submit" class="btn btn-danger pull-right delete-confirm" value="Sí, eliminar">
                
                <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancelar</button>
            </div>
            {!! Form::close()!!}
        </div>
    </div>
</div>
<div class="modal modal-danger fade" tabindex="-1" id="ModalQuitarFoto" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('remove_photo') }}" method="post">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><i class="voyager-trash"></i> - ¿Quitar foto de perfil?</h4>
                </div>
                <div class="modal-body">
                    <div class="text-center" style="text-transform:uppercase">
                        <i class="voyager-trash" style="color: red; font-size: 5em;"></i>
                        <br>
                        <p><b>Se restaurará la imagen por defecto</b></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" class="btn btn-danger pull-right" value="Sí, quitar">
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('css')
<style>
    .profile-card{
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 6px 24px rgba(0,0,0,.08);
        overflow: hidden;
        margin-bottom: 20px;
    }
    .profile-card__cover{
        position: relative;
        height: 110px;
        background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
    }
    .profile-card__avatar{
        position: absolute;
        left: 50%;
        bottom: -55px;
        transform: translateX(-50%);
        width: 110px;
        height: 110px;
        border-radius: 50%;
        object-fit: cover;
        border: 5px solid #fff;
        box-shadow: 0 4px 14px rgba(0,0,0,.18);
        background: #fff;
    }
    .profile-card__avatar-wrap{
        position: absolute;
        left: 50%;
        bottom: -55px;
        transform: translateX(-50%);
        width: 110px;
        height: 110px;
        cursor: pointer;
        margin: 0;
        display: block;
    }
    .profile-card__avatar-wrap .profile-card__avatar{
        position: static;
        left: auto;
        bottom: auto;
        transform: none;
        display: block;
    }
    .profile-card__avatar-overlay{
        position: absolute;
        inset: 5px;
        border-radius: 50%;
        background: rgba(0,0,0,.45);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        opacity: 0;
        transition: opacity .18s ease;
    }
    .profile-card__avatar-wrap:hover .profile-card__avatar-overlay{ opacity: 1; }
    .photo-input-hidden{ display: none !important; }
    .profile-card__head{
        text-align: center;
        padding: 65px 20px 18px;
    }
    .profile-card__name{
        margin: 0 0 6px;
        font-weight: 700;
        color: #1f2d3d;
    }
    .profile-card__mail{
        color: #7f8c8d;
        font-size: .95rem;
    }
    .profile-card__mail i{ color: #2ecc71; margin-right: 4px; }
    .profile-card__actions{ margin-top: 10px; }
    .profile-card__remove{
        color: #e74c3c;
        font-size: .85rem;
        padding: 4px 8px;
        text-decoration: none;
    }
    .profile-card__remove:hover,
    .profile-card__remove:focus{ color: #c0392b; text-decoration: underline; }
    .profile-grid{
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1px;
        background: #eef2f5;
        border-top: 1px solid #eef2f5;
    }
    @media (max-width: 600px){ .profile-grid{ grid-template-columns: 1fr; } }
    .profile-item{
        display: flex;
        align-items: center;
        gap: 12px;
        background: #fff;
        padding: 16px 18px;
    }
    .profile-item__icon{
        flex-shrink: 0;
        width: 42px;
        height: 42px;
        border-radius: 10px;
        background: rgba(46,204,113,.12);
        color: #2ecc71;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }
    .profile-item__label{
        display: block;
        font-size: .72rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #95a5a6;
        margin-bottom: 2px;
    }
    .profile-item__value{
        display: block;
        color: #1f2d3d;
        font-weight: 600;
        line-height: 1.25;
    }
    .text-actual{
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .lbl-session{
        color: aliceblue;
        background: #2ecc71; 
        font-size: 1rem; 
        padding: 0.5rem 1rem;
        border-radius: 5px;
    }
    .password-field{
        display: flex;
        max-width: 700px;
        align-items: stretch;
    }
    .password-field .form-control{
        height: 40px;
        border-right: 0;
        border-radius: 4px 0 0 4px;
        box-shadow: none;
    }
    .password-toggle-wrap{
        display: flex;
    }
    .password-toggle{
        width: 46px;
        height: 40px;
        border: 1px solid #d2d6de;
        border-left: 0;
        border-radius: 0 4px 4px 0;
        background: #f8fafc;
        color: #52616b;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background-color .15s ease, color .15s ease;
    }
    .password-toggle:hover,
    .password-toggle:focus{
        background: #eef3f7;
        color: #111827;
        outline: none;
    }
</style>
@section('javascript')
<script type="text/javascript">
    $('#ModalEliminar').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget) 
        var id = button.data('id') 
        var modal = $(this)
        modal.find('.modal-body #id').val(id);
    })

    $('#photo-input').on('change', function () {
        var file = this.files[0];
        if (!file) return;
        var max = 4 * 1024 * 1024;
        if (file.size > max) {
            alert('La imagen no debe superar los 4MB.');
            this.value = '';
            return;
        }
        var reader = new FileReader();
        reader.onload = function (e) { $('#avatar-preview').attr('src', e.target.result); };
        reader.readAsDataURL(file);
        $('#photo-form').submit();
    });

    $('#toggle-password').on('click', function () {
        var input = $('#password');
        var icon = $(this).find('i');

        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
</script>
@endsection
