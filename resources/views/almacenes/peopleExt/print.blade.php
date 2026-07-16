@extends('layouts.template-print-alt')

@section('page_title', 'Reporte Personas Externas')

@section('content')

    {{-- Encabezado institucional --}}
    <table width="100%">
        <tr>
            <td style="width:18%">
                <img src="{{ asset('images/icon.png') }}" alt="GADBENI" width="90px">
            </td>
            <td style="text-align:center; width:64%">
                <h2 style="margin:0; font-size:14pt">
                    REPORTE DE PERSONAS EXTERNAS
                </h2>
                <h3 style="margin:4px 0 0 0; font-size:11pt">
                    SISTEMA SYSALMACEN
                </h3>
                <h4 style="margin:4px 0 0 0; font-size:10pt">
                    {{ $estadoLabel }}
                </h4>
            </td>
            <td style="text-align:right; width:18%; vertical-align:top">
                <small style="font-size:9px; font-weight:normal">
                    {{ date('d/m/Y H:i:s') }}
                </small>
            </td>
        </tr>
    </table>

    <br>

    <table style="width:100%; font-size:9px; border-collapse:collapse" border="1" cellspacing="0" cellpadding="3">
        <thead>
            <tr style="background:#e8f0fe">
                <th style="width:3%; text-align:center">N°</th>
                <th style="width:7%; text-align:center">CI</th>
                <th style="width:16%">NOMBRE COMPLETO</th>
                <th style="width:12%">CARGO</th>
                <th style="width:14%">CORREO USUARIO</th>
                <th style="width:9%">ROL</th>
                <th style="width:18%">ALMACÉN / SUB-ALMACÉN</th>
                <th style="width:16%">DIRECCIÓN / UNIDAD</th>
                <th style="width:7%; text-align:center">ESTADO</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $item)
                @php
                    $nombre = trim(($item->people->first_name ?? '') . ' ' . ($item->people->middle_name ?? '') . ' ' . ($item->people->paternal_surname ?? '') . ' ' . ($item->people->maternal_surname ?? '') . ' ' . ($item->people->married_surname ?? ''));
                    $estado = $item->status == 1 ? 'ACTIVO' : 'INACTIVO';
                    $userCount = count($item->users);
                @endphp
                @if($userCount > 0)
                    @foreach ($item->users as $usuario)
                        <tr>
                            @if($loop->first)
                                <td style="text-align:center; vertical-align:middle" rowspan="{{ $userCount }}">{{ $loop->parent->iteration }}</td>
                                <td style="text-align:center; vertical-align:middle" rowspan="{{ $userCount }}">{{ $item->people->ci ?? '—' }}</td>
                                <td style="vertical-align:middle" rowspan="{{ $userCount }}">{{ $nombre }}</td>
                                <td style="vertical-align:middle" rowspan="{{ $userCount }}">{{ $item->cargo }}</td>
                            @endif
                            <td style="vertical-align:middle">{{ $usuario->email }}</td>
                            <td style="vertical-align:middle">{{ $usuario->role->display_name ?? '—' }}</td>
                            <td style="vertical-align:middle">
                                {{ $usuario->sucursal->nombre ?? '—' }}
                                @if($usuario->subAlmacen) <br>{{ $usuario->subAlmacen->name }}@endif
                            </td>
                            <td style="vertical-align:middle">
                                {{ $usuario->direction->nombre ?? '—' }}
                                @if($usuario->unit) <br>{{ $usuario->unit->nombre }}@endif
                            </td>
                            @if($loop->first)
                                <td style="text-align:center; vertical-align:middle" rowspan="{{ $userCount }}">{{ $estado }}</td>
                            @endif
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td style="text-align:center">{{ $loop->iteration }}</td>
                        <td style="text-align:center">{{ $item->people->ci ?? '—' }}</td>
                        <td>{{ $nombre }}</td>
                        <td>{{ $item->cargo }}</td>
                        <td colspan="4" style="text-align:center; font-style:italic; color:#999">Sin usuario asignado</td>
                        <td style="text-align:center">{{ $estado }}</td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="9" style="text-align:center; font-style:italic; color:#999">
                        No se encontraron registros.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <br>
    <small style="font-size:9px">Total registros: <strong>{{ count($data) }}</strong></small>

@endsection

@section('css')
    <style>
        table, th, td { border-collapse: collapse; }
        @media print {
            thead { display: table-header-group; }
            tfoot { display: table-footer-group; }
        }
    </style>
@stop
