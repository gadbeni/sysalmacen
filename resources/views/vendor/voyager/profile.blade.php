@extends('voyager::master')

@section('css')
    <style>
        .user-email {
            font-size: .85rem;
            margin-bottom: 1.5em;
        }
        #gorro{
            position: absolute;
            width: 80px;
            height: 80px;
            top: -40px;
            left: 62%;
            rotate: 345deg;
            transform: translateX(-50%);
        }
        .container_perfil{
            position: relative;
            width: 150px;
            height: 150px;
            margin: 0 auto;
        }
    </style>
@stop

@section('content')
    <div style="background-size:cover; background-image: url({{ Voyager::image( Voyager::setting('admin.bg_image'), voyager_asset('/images/bg.jpg')) }}); background-position: center center;position:absolute; top:0; left:0; width:100%; height:300px;"></div>
    <div style="height:160px; display:block; width:100%"></div>
    <div style="position:relative; z-index:9; text-align:center;">
        <div class="container_perfil">
            <img src="@if( !filter_var(Auth::user()->avatar, FILTER_VALIDATE_URL)){{ Voyager::image( Auth::user()->avatar ) }}@else{{ Auth::user()->avatar }}@endif"
             class="avatar"
             style="border-radius:50%; width:150px; height:150px; border:5px solid #fff;"
             alt="{{ Auth::user()->name }} avatar">
            @if(setting('configuracion.navidad'))
            <img id="gorro" src="{{asset('navidad/image/gorro_navide.png')}}" alt="gorrito navideño">
            @endif
        </div>
        @if(setting('configuracion.navidad'))
         <h2 style="color:rgb(154, 24, 4); margin-top:20px;">Feliz Navidad</h2>
        @endif
        <h4>{{ ucwords(Auth::user()->name) }}</h4>
        <div class="user-email text-muted">{{ ucwords(Auth::user()->email) }}</div>
        <p>{{ Auth::user()->bio }}</p>
        {{-- @if ($route != '') --}}
            <a href="{{route('sessions')}}" class="btn btn-primary">Seguridad Usuario</a>
        {{-- @endif --}}
    </div>
@stop