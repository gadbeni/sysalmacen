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
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <h2 class="page-title">
                        <i class="fa fa-id-card"></i>Datos
                    </h2>
                </div>
                <div class="panel-body">
                    <div class="">
                        <div class="form-group">
                            <label class="h4">Nombre</label>
                            <p>{{Auth::user()->name}}</p>
                        </div>
                        <div class="form-group">
                            <hr>
                            <label class="h4">Email</label>
                            <p>{{Auth::user()->email}}</p>
                        </div>
                        @if (Auth::user()->direction)
                        <div class="form-group">
                            <hr>
                            <label class="h4">Dirección Administrativa</label>
                            <p>{{Auth::user()->direction->nombre}}</p>
                        </div>
                        @endif
                        @if (Auth::user()->unit)
                        <div class="form-group">
                            <hr>
                            <label class="h4">Unidad Administrativa</label>
                            <p>{{Auth::user()->unit->nombre}}</p>
                        </div>
                        @endif                    
                    </div>
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
                    <form action="{{route('change_password')}}" method="post">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="">Nueva Contraseña:</label>
                            <input style="max-width: 700px;" type="password" name="password" class="form-control" id="" required>
                        </div>
                        <div class="form-group">
                            <label for="">Confirmar Contraseña:</label>
                            <input style="max-width: 700px;" type="password" name="password_confirmation"" class="form-control" id="" required>
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
@endsection
@section('css')
<style>
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
</style>
@section('javascript')
<script type="text/javascript">
    $('#ModalEliminar').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget) 
        var id = button.data('id') 
        var modal = $(this)
        modal.find('.modal-body #id').val(id);
    })
</script>
@endsection