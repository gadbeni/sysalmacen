@extends('layouts.template-print-alt')

@section('page_title', 'Reporte Usuarios por Dirección Administrativa')

@section('content')

    {{-- Encabezado institucional --}}
    <table width="100%">
        <tr>
            <td style="width:18%">
                <img src="{{ asset('images/icon.png') }}" alt="GADBENI" width="90px">
            </td>
            <td style="text-align:center; width:64%">
                <h2 style="margin:0; font-size:14pt">
                    GOBIERNO AUTÓNOMO DEPARTAMENTAL DEL BENI
                </h2>
                <h3 style="margin:4px 0 0 0; font-size:11pt">
                    UNIDAD DE ALMACENES, MATERIALES Y SUMINISTROS
                </h3>
                <h4 style="margin:4px 0 0 0; font-size:10pt">
                    {{ strtoupper($sucursal->nombre) }}<br>
                    DISTRIBUCIÓN DE USUARIOS POR DIRECCIÓN Y UNIDAD ADMINISTRATIVA
                </h4>
            </td>
            <td style="text-align:right; width:18%; vertical-align:top">
                <small style="font-size:9px; font-weight:normal">
                    Impreso por:<br>
                    <strong>{{ Auth::user()->name }}</strong><br>
                    {{ date('d/m/Y H:i:s') }}
                </small>
            </td>
        </tr>
    </table>

    <br>

    @forelse ($data as $dirItem)
        @php $dir = $dirItem['direccion']; @endphp

        {{-- Cabecera de Dirección Administrativa --}}
        <table width="100%" style="margin-top: 14px; margin-bottom: 0">
            <tr>
                <td style="background:#2d6a9f; color:#fff; padding:5px 8px; font-size:11pt; font-weight:bold">
                    DIRECCIÓN ADMINISTRATIVA:
                    {{ strtoupper($dir->nombre) }}
                    @if($dir->sigla) &nbsp;({{ $dir->sigla }}) @endif
                </td>
            </tr>
        </table>

        <table style="width:100%; font-size:9px; border-collapse:collapse" border="1" cellspacing="0" cellpadding="3">
            <thead>
                <tr style="background:#e8f0fe">
                    <th style="width:3%; text-align:center">N°</th>
                    <th style="width:22%">UNIDAD ADMINISTRATIVA</th>
                    <th style="width:6%; text-align:center">SIGLA</th>
                    <th style="width:7%; text-align:center">CI</th>
                    <th style="width:22%">NOMBRE COMPLETO</th>
                    <th style="width:14%">CORREO</th>
                    <th style="width:10%">ROL</th>
                    <th style="width:16%; text-align:center">ÚLTIMO ACCESO</th>
                </tr>
            </thead>
            <tbody>
                @php $numUnidad = 1; @endphp
                @forelse ($dirItem['unidades'] as $uItem)
                    @php
                        $unidad    = $uItem['unidad'];
                        $usuarios  = $uItem['usuarios'];
                        $userCount = count($usuarios);
                    @endphp
                    @if($userCount > 0)
                        @foreach ($usuarios as $usr)
                            <tr>
                                @if($loop->first)
                                    <td style="text-align:center; vertical-align:middle" rowspan="{{ $userCount }}">{{ $numUnidad }}</td>
                                    <td style="vertical-align:middle" rowspan="{{ $userCount }}">{{ $unidad->nombre }}</td>
                                    <td style="text-align:center; vertical-align:middle" rowspan="{{ $userCount }}">{{ $unidad->sigla ?? '-' }}</td>
                                @endif
                                <td style="text-align:center; vertical-align:middle">{{ $usr->ci ?? '—' }}</td>
                                <td style="vertical-align:middle">{{ trim(($usr->first_name ?? '') . ' ' . ($usr->last_name ?? '')) }}</td>
                                <td style="vertical-align:middle">{{ $usr->email ?? '—' }}</td>
                                <td style="vertical-align:middle">{{ $usr->rol ?? '—' }}</td>
                                <td style="text-align:center; vertical-align:middle">
                                    {{ $usr->last_login_at ? \Carbon\Carbon::parse($usr->last_login_at)->format('d/m/Y H:i') : '—' }}
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td style="text-align:center">{{ $numUnidad }}</td>
                            <td>{{ $unidad->nombre }}</td>
                            <td style="text-align:center">{{ $unidad->sigla ?? '-' }}</td>
                            <td colspan="5" style="text-align:center; font-style:italic; color:#999">Sin usuarios asignados</td>
                        </tr>
                    @endif
                    @php $numUnidad++; @endphp
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center; font-style:italic; color:#999">
                            No hay unidades registradas para esta dirección.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    @empty
        <p style="text-align:center; font-style:italic; color:#999; margin-top:20px">
            No se encontraron direcciones administrativas asignadas a este almacén.
        </p>
    @endforelse


@endsection

@section('css')
    <style>
        table, th, td { border-collapse: collapse; }
        table.print-friendly tr td,
        table.print-friendly tr th { page-break-inside: avoid; }
        @media print {
            thead { display: table-header-group; }
            tfoot { display: table-footer-group; }
        }
    </style>
@stop
