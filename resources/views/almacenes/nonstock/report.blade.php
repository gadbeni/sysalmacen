@extends('layouts.template-print-alt')

@section('page_title', 'Reporte')

@section('content')

    @if ($nonStockRequest->status == 'eliminado')
        <div id="watermark">
            <img src="{{ asset('images/anulado.png') }}" /> 
        </div>
    @endif

    @if ($nonStockRequest->status == 'rechazado')
        <div id="watermark">
            <img src="{{ asset('images/rechazado.png') }}" /> 
        </div>
    @endif
    @if ($nonStockRequest->status == 'aprobado')
        <div id="watermark" class="w-opacity">
            <img src="{{ asset('images/inexistencia.png') }}" /> 
        </div>
    @endif
    @php
        $months = array('', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');    
    @endphp

    <table  cellspacing="0" width="100%">
        <tr>
            <td style="width: 15%"><img src="{{ asset('images/icon.png') }}" alt="GADBENI" width="100px"></td>
            <td style="text-align: center;  width:70%">
                <h3 style="margin-bottom: 0px; margin-top: 5px; font-size: 15px">
                    SOLICITUD DE CONTRATACIONES DE OBRAS, BIENES, SERVICIOS GENERALES Y CONSULTARIA/FORMULARIO PEDIDO DE MATERIALES Y/O SERVICIOS <br>
                    FORMULARIO (INEXISTENCIA)<br>
                </h3>     
            </td>
            <td style="text-align: right; width:15%">
                <h3 style="margin-bottom: 0px; margin-top: 5px; font-size: 15px">
                    N° {{$nonStockRequest->nro_request}}
                    <br>
                    {{date('d/m/Y', strtotime($nonStockRequest->date_request))}}
                    {{-- <br> --}}
                   
                    {{-- <small style="font-size: 11px; font-weight: 100">Impreso por: {{ Auth::user()->name }} <br> {{ date('d/M/Y H:i:s') }}</small> --}}
                </h3>
            </td>
        </tr>
    </table>
    <br>
    <table border="1" cellspacing="0" cellpadding="5" class="text-center" width="100%" style="font-size: 8pt">
        <tr>
            <th style="text-align: left; width:120px">SOLICITANTE</th>
            <td style="text-align: left">{{strtoupper($nonStockRequest->registerUser_name.' '.$nonStockRequest->job)}}</td>                        
        </tr>
        <tr>
            <th style="text-align: left">UNIDAD SOLICITANTE</th>
            <td style="text-align: left">
                @if ($nonStockRequest->unit_name)
                    {{strtoupper($nonStockRequest->unit_name.' - '.$nonStockRequest->direction_name)}}
                @else
                    {{strtoupper($nonStockRequest->direction_name)}}
                @endif
            </td>                        
        </tr>
    </table>
    <br>
    <table style="width: 100%; font-size: 12px" border="1" cellspacing="0" cellpadding="5">
        <thead>
            <tr>
                <th style="width:5px">N&deg;</th>
                <th style="text-align: center">DESCRIPCION</th>
                <th style="text-align: center; width:100px">UNIDAD</th>
                <th style="text-align: center; width:5px">CANTIDAD</th>
                <th style="text-align: center; width:5px">PRECIO UNITARIO</th>
                <th style="text-align: center; width:5px">PRECIO REFERENCIAL</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($nonRequestArticles as $data)
                
                <tr>
                    <td style="text-align: right">{{$loop->iteration}}</td>
                    {{-- <td style="text-align: center">{{$data->}}</td> --}}
                    @if ($data->article_id)
                        <td style="text-align: left">{{strtoupper($data->article->nombre)}}</td>
                        <td style="text-align: center">{{strtoupper($data->article->presentacion)}}</td>
                    @else
                        <td style="text-align: left">{{strtoupper($data->nonStockArticle->name_description)}}</td>
                        <td style="text-align: center">{{strtoupper($data->articlePresentation->name_presentation)}}</td>
                    @endif
                    <td style="text-align: right">{{number_format($data->quantity, 2, ',', ' ')}}</td>
                    <td style="text-align: right">
                        {{-- {{number_format($data->unit_price, 2, ',', ' ')}} --}}
                    </td>
                    <td style="text-align: right">
                        {{-- {{number_format($data->reference_price, 2, ',', ' ')}} --}}
                    </td>
                </tr>
                
            @empty
                <tr style="text-align: center">
                    <td colspan="5">No se encontraron registros.</td>
                </tr>
            @endforelse
         
        </tbody>
       
    </table>

    {{-- <div class="text">
        <p style="font-size: 13px;"><b>NOTA:</b> La información expuesta en el presente cuadro cuenta con la documentación de soporte correspondiente, en el marco de las Normas Básicas del Sistema de Contabilidad Integrada.</p>
    </div> --}}
    <br>
    <br><br>
    <br>
    <table width="100%">
        <tr>
            <td style="text-align: center">
                ______________________
                <br>
                <b style="font-size: 14px">Unidad Solicitante</b> <br>
                <b style="font-size: 11px">{{strtoupper($nonStockRequest->registerUser_name)}} <br>
                    {{strtoupper($nonStockRequest->job)}}</b>
            </td>
            <td style="text-align: center">
                {{-- ______________________
                <br>
                <b>Firma Responsable</b> --}}
            </td>
            <td style="text-align: center">
                ______________________
                <br>
                <b style="font-size: 14px">{{$nonStockRequest->sucursal_id!=1?'Recibi Conforme':'Dirección Administrativa'}}</b>
                <br>
                <br>
                <br>
            </td>
        </tr>
    </table>

@endsection

@section('css')
    <style>
        table, th, td {
            border-collapse: collapse;
        }
        body{
            margin: 1rem 2rem;
        }
        .w-opacity{
            opacity: 0.2 !important;
        }
          
    </style>
@stop