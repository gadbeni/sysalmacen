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
                                            Dispositivo
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
                                        <td class="text-center">
                                          <form method="POST" action="{{route('delete_session')}}">
                                              @csrf
                                              @method('DELETE')
                                              <input type="hidden" name="id" value="{{ $session->id }}">
                                              <button type="submit" class="btn btn-danger">Eliminar</button>
                                          </form>
                                          {{-- <button type="button" name="button" class="btn btn-danger delete-session" data-id="{{ $session->id }}">?️</button> --}}
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

@endsection
 
@section('js')
<script type="text/javascript">
    $(".delete-session").click(function(){
        var id = $(this).data("id");
        var token = $("meta[name='csrf']").attr("content");
        $.ajax({
            url: "/delete-session",
            type: 'POST',
            data: {
                "id": id,
                "_token": token,
            },
            success: function (){
                location.reload();
            }
        });
    });
</script>
@endsection