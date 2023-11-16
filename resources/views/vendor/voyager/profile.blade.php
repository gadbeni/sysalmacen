@extends('voyager::master')
@section('page_title', 'Perfil')
@section('page_header')
    <div class="container-fluid">
        <div class="row">
            <h1 class="page-title">
                <i class="voyager-person"></i>Perfil - Usuario
            </h1>
        </div>

    </div>
@endsection
@section('content')
<div class="page-content browse container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-bordered" style="">
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
                            <hr>
                        </div>
                        <div class="form-group">
                            <label class="h4">Email</label>
                            <p>{{Auth::user()->email}}</p>
                            <hr>
                        </div>
                        @if (Auth::user()->direction)
                        <div class="form-group">
                            <label class="h4">Dirección Administrativa</label>
                            <p>{{Auth::user()->direction->nombre}}</p>
                            <hr>
                        </div>
                        @endif
                        @if (Auth::user()->unit)
                        <div class="form-group">
                            <label class="h4">Unidad Administrativa</label>
                            <p>{{Auth::user()->unit->nombre}}</p>
                        </div>
                        @endif                    
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection