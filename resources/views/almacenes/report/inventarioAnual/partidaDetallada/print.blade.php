@extends('layouts.template-print-alt')

@section('page_title', 'Reporte')

@section('content')

    <table width="100%">
        <tr>
            <td style="width: 20%"><img src="{{ asset('images/icon.png') }}" alt="GADBENI" width="100px"></td>
            <td style="text-align: center;  width:70%">
                <h3 style="margin-bottom: 0px; margin-top: 5px">
                    GOBIERNO AUTONOMO DEPARTAMENTAL DEL BENI<br>
                </h3>
                <h4 style="margin-bottom: 0px; margin-top: 5px">
                        RESUMEN DE COMPRAS POR PARTIDA {{$partida->codigo}} - {{$partida->nombre}}
                        <br>
                        {{$sucursal->nombre}}
                    {{-- Stock Disponible {{date('d/m/Y', strtotime($start))}} Hasta {{date('d/m/Y', strtotime($finish))}} --}}
                </h4>
                <small style="margin-bottom: 0px; margin-top: 5px">
                     Gestión {{$gestion}}
                    <br>
                    (Expresado en Bolivianos)
                </small>
            </td>
            <td style="text-align: right; width:30%">
                <h3 style="margin-bottom: 0px; margin-top: 5px">
                   
                    <small style="font-size: 11px; font-weight: 100">Impreso por: {{ Auth::user()->name }} <br> {{ date('d/M/Y H:i:s') }}</small>
                </h3>
            </td>
        </tr>
    </table>
    <br><br>
    <table style="width: 100%; font-size: 12px" border="1" cellspacing="0" cellpadding="5">
        <thead>
            <tr>
                <th style="width:5px" rowspan="2">N&deg;</th>
                <th style="text-align: center" rowspan="2">DESCRIPCION</th>
                <th style="text-align: center" rowspan="2">CODIGO</th>
                <th style="text-align: center" rowspan="2">PRESENTACIÓN</th>
                <th style="text-align: center" colspan="2">COMPRAS</th>
            </tr>
            <tr>
                <th style="text-align: center">CANTIDAD</th>
                <th style="text-align: center">SUB TOTAL BS</th>
            </tr>
        </thead>
        <tbody>
                    @php
                        $count = 1;
                        $cantidad_total = 0;
                        $total_bs = 0;
                    @endphp
                    @forelse ($data as $item)
                        <tr>
                            <td>{{ $count }}</td>
                            <td>{{ $item->nombre }}</td>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->presentacion }}</td>
                            <td style="text-align: right">{{ number_format($item->total_cantsolicitada,2, ',', '.')}}</td>
                            <td style="text-align: right">{{ number_format($item->total_totalbs,2, ',', '.')}}</td>


                                                                                    
                        </tr>
                        @php
                            $count++;
                            $cantidad_total += $item->total_cantsolicitada;
                            $total_bs += $item->total_totalbs;
                        @endphp
                    @empty
                        <tr style="text-align: center">
                            <td colspan="6">No se encontraron registros.</td>
                        </tr>
                    @endforelse
                    <tr>
                        <th colspan="4" style="text-align: right">Total</th>
                        <th style="text-align: right">{{number_format($cantidad_total,2, ',', '.')}}</th>
                        <th style="text-align: right">{{number_format($total_bs,2, ',', '.')}}</th>
                    </tr>
        </tbody>
       
    </table>

    <div class="text">
        <p style="font-size: 13px;"><b>NOTA:</b> La información expuesta en el presente cuadro cuenta con la documentación de soporte correspondiente, en el marco de las Normas Básicas del Sistema de Contabilidad Integrada.</p>
    </div>
    <br>
    <br><br>
    <br>
    <table width="100%">
        <tr>
            <td style="text-align: center">
                ______________________
                <br>
                <b>Firma Contabilidad</b>
            </td>
            <td style="text-align: center">
                {{-- ______________________
                <br>
                <b>Firma Responsable</b> --}}
            </td>
            <td style="text-align: center">
                ______________________
                <br>
                <b>Firma Responsable</b>
            </td>
        </tr>
    </table>
    <br>
    <table width="100%">
        <tr>
            <td style="text-align: center">
                {{-- ______________________
                <br>
                <b>Firma Contabilidad</b> --}}
            </td>
            <td style="text-align: center">
                ______________________
                <br>
                <b>Firma DGAA - DAF</b>
            </td>
            <td style="text-align: center">
                {{-- ______________________
                <br>
                <b>Firma DGAA - DAF</b> --}}
            </td>
        </tr>
    </table>

@endsection