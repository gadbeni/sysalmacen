@extends('voyager::master')

@section('page_title', 'Historial de usuario')

@section('css')
<style>
    /* Cabecera con datos del usuario */
    .hist-user-card {
        display: flex; align-items: center; gap: 15px;
        background: #fff; border: 1px solid #e4eaec; border-radius: 6px;
        padding: 15px 20px; margin-bottom: 25px;
    }
    .hist-user-card img {
        width: 60px; height: 60px; border-radius: 50%; object-fit: cover;
        border: 2px solid #e4eaec;
    }
    .hist-user-card h4 { margin: 0 0 4px 0; font-weight: 600; }
    .hist-user-card small { color: #76838f; }

    /* Línea de tiempo */
    .hist-timeline { position: relative; padding-left: 45px; }
    .hist-timeline:before {
        content: ''; position: absolute; left: 15px; top: 5px; bottom: 5px;
        width: 3px; background: #e4eaec; border-radius: 3px;
    }
    .hist-evento { position: relative; margin-bottom: 25px; }
    .hist-dot {
        position: absolute; left: -45px; top: 12px;
        width: 33px; height: 33px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-size: 15px; z-index: 1;
        box-shadow: 0 0 0 4px #f1f4f5;
    }
    .hist-dot.creado      { background: #4caf50; }
    .hist-dot.actualizado { background: #2196f3; }
    .hist-dot.activado    { background: #62a8ea; }
    .hist-dot.desactivado { background: #f2a654; }
    .hist-dot.eliminado   { background: #e74c3c; }
    .hist-dot.restaurado  { background: #9b59b6; }

    /* Tarjeta del evento */
    .hist-card {
        background: #fff; border: 1px solid #e4eaec; border-radius: 6px;
        overflow: hidden;
    }
    .hist-card-head {
        display: flex; justify-content: space-between; align-items: center;
        flex-wrap: wrap; gap: 8px;
        padding: 12px 18px; background: #f8fafb; border-bottom: 1px solid #e4eaec;
    }
    .hist-titulo { font-size: 15px; font-weight: 600; margin: 0; }
    .hist-fecha { color: #76838f; font-size: 13px; }
    .hist-fecha strong { color: #37474f; }
    .hist-autor {
        display: flex; align-items: center; gap: 8px;
        background: #fff; border: 1px solid #e4eaec; border-radius: 20px;
        padding: 3px 12px 3px 4px; font-size: 12px;
    }
    .hist-autor img { width: 26px; height: 26px; border-radius: 50%; object-fit: cover; }

    /* Resumen de campos modificados */
    .hist-resumen { padding: 10px 18px; border-bottom: 1px solid #eef2f4; font-size: 13px; color: #76838f; }
    .hist-chip {
        display: inline-block; background: #fff3cd; color: #8a6d3b;
        border: 1px solid #ffe69c; border-radius: 12px;
        padding: 1px 10px; font-size: 11px; font-weight: 600; margin: 1px 2px;
    }

    /* Tabla antes / después */
    .hist-tabla { margin: 0; }
    .hist-tabla th {
        background: #fff; color: #526069; font-size: 12px;
        text-transform: uppercase; letter-spacing: .5px;
    }
    .hist-tabla td { vertical-align: middle !important; font-size: 13px; }
    .hist-row-cambio { background: #fffbf0 !important; }
    .hist-antes-cambio { color: #c0392b; text-decoration: line-through; }
    .hist-despues-cambio { color: #27ae60; font-weight: 700; }
    .hist-flecha { color: #27ae60; margin-right: 4px; }
    .hist-sin-valor { color: #bcc5cc; }
    .hist-sin-cambio { color: #76838f; }

    .hist-vacio {
        background: #fff; border: 2px dashed #e4eaec; border-radius: 6px;
        padding: 40px; text-align: center; color: #76838f;
    }
</style>
@stop

@section('page_header')
    <h1 class="page-title">
        <i class="voyager-watch"></i> Historial de cambios
    </h1>
    <a href="{{ route('voyager.users.index') }}" class="btn btn-default">
        <i class="voyager-double-left"></i> Volver a usuarios
    </a>
@stop

@section('content')
    @php
        \Carbon\Carbon::setLocale('es');
        $titulos = [
            'creado' => 'Usuario creado',
            'actualizado' => 'Datos actualizados',
            'activado' => 'Usuario activado',
            'desactivado' => 'Usuario desactivado',
            'eliminado' => 'Usuario eliminado',
            'restaurado' => 'Usuario restaurado',
        ];
        $iconos = [
            'creado' => 'voyager-person',
            'actualizado' => 'voyager-edit',
            'activado' => 'voyager-check',
            'desactivado' => 'voyager-power',
            'eliminado' => 'voyager-trash',
            'restaurado' => 'voyager-refresh',
        ];
    @endphp
    <div class="page-content browse container-fluid">
        <div class="row">
            <div class="col-md-12">

                {{-- Datos del usuario --}}
                <div class="hist-user-card">
                    <img src="{{ $user->photo_url }}" alt="Foto de {{ $user->name }}">
                    <div>
                        <h4>{{ ucwords($user->name) }}</h4>
                        <small><i class="voyager-mail"></i> {{ $user->email }}</small>
                        &nbsp;
                        @if($user->status)
                            <span class="label label-success">Activo</span>
                        @else
                            <span class="label label-danger">Inactivo</span>
                        @endif
                    </div>
                    <div style="margin-left:auto; text-align:right;">
                        <h4 style="margin:0;">{{ $historial->total() }}</h4>
                        <small>{{ $historial->total() == 1 ? 'registro' : 'registros' }} en el historial</small>
                    </div>
                </div>

                @if($historial->isEmpty())
                    <div class="hist-vacio">
                        <h4><i class="voyager-watch"></i> Este usuario aún no tiene historial</h4>
                        <p>Los cambios se registran cada vez que se crea, actualiza, activa o desactiva el usuario.</p>
                    </div>
                @else
                    <div class="hist-timeline">
                        @foreach($historial as $registro)
                            @php
                                $antes = $registro->antes ?? [];
                                $despues = $registro->despues ?? [];
                                $campos = array_unique(array_merge(array_keys($antes), array_keys($despues)));
                                $cambiados = array_keys($registro->cambios);
                                $accion = $registro->accion;
                            @endphp
                            <div class="hist-evento">
                                <div class="hist-dot {{ $accion }}">
                                    <i class="{{ $iconos[$accion] ?? 'voyager-edit' }}"></i>
                                </div>
                                <div class="hist-card">
                                    <div class="hist-card-head">
                                        <div>
                                            <p class="hist-titulo">{{ $titulos[$accion] ?? ucfirst($accion) }}</p>
                                            <span class="hist-fecha">
                                                <i class="voyager-calendar"></i>
                                                <strong>{{ $registro->created_at->isoFormat('D [de] MMMM [de] YYYY') }}</strong>
                                                a las {{ $registro->created_at->format('H:i') }}
                                                &middot; {{ $registro->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                        <span class="hist-autor" title="Quién realizó el cambio">
                                            <img src="{{ $registro->changedBy ? $registro->changedBy->photo_url : asset('images/usuario.png') }}" alt="">
                                            Modificado por
                                            <strong>{{ $registro->changedBy ? ucwords($registro->changedBy->name) : 'Sistema' }}</strong>
                                        </span>
                                    </div>

                                    {{-- Resumen de lo que cambió --}}
                                    <div class="hist-resumen">
                                        @if($accion == 'creado')
                                            <i class="voyager-info-circled"></i> Registro inicial del usuario con los datos que se muestran abajo.
                                        @elseif(count($cambiados))
                                            <i class="voyager-info-circled"></i>
                                            {{ count($cambiados) == 1 ? 'Se modificó 1 campo:' : 'Se modificaron '.count($cambiados).' campos:' }}
                                            @foreach($cambiados as $c)
                                                <span class="hist-chip">{{ $c }}</span>
                                            @endforeach
                                        @else
                                            <i class="voyager-info-circled"></i> Se guardó sin modificar ningún dato.
                                        @endif
                                    </div>

                                    <table class="table hist-tabla">
                                        <thead>
                                            <tr>
                                                <th style="width:24%;">Campo</th>
                                                <th style="width:38%;">Antes</th>
                                                <th style="width:38%;">Después</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($campos as $campo)
                                                @php
                                                    $valorAntes = $antes[$campo] ?? null;
                                                    $valorDespues = $despues[$campo] ?? null;
                                                    $cambio = in_array($campo, $cambiados);
                                                @endphp
                                                <tr class="{{ $cambio ? 'hist-row-cambio' : '' }}">
                                                    <td><strong>{{ $campo }}</strong></td>
                                                    <td>
                                                        @if(is_null($valorAntes) || $valorAntes === '')
                                                            <span class="hist-sin-valor">—</span>
                                                        @elseif($cambio)
                                                            <span class="hist-antes-cambio">{{ $valorAntes }}</span>
                                                        @else
                                                            <span class="hist-sin-cambio">{{ $valorAntes }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if(is_null($valorDespues) || $valorDespues === '')
                                                            <span class="hist-sin-valor">—</span>
                                                        @elseif($cambio)
                                                            <i class="voyager-forward hist-flecha"></i><span class="hist-despues-cambio">{{ $valorDespues }}</span>
                                                        @else
                                                            <span class="hist-sin-cambio">{{ $valorDespues }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="text-center">
                    {{ $historial->links() }}
                </div>

            </div>
        </div>
    </div>
@stop
