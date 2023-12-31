@extends('voyager::master')

@section('page_title', 'Viendo Proveedores')

@if(auth()->user()->hasPermission('browse_providers'))
    @section('page_header')
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <h1 id="subtitle" class="page-title">
                        <i class="voyager-milestone"></i> Proveedores
                    </h1>
                    @if(auth()->user()->hasPermission('add_providers') && !auth()->user()->hasRole('admin'))
                        <a href="{{ route('voyager.providers.create') }}" class="btn btn-success btn-add-new">
                            <i class="voyager-plus"></i> <span>Crear</span>
                        </a>
                    @endif
                </div>
                <div class="col-md-4">

                </div>
            </div>
        </div>
    @stop

    @section('content')
            <div class="page-content browse container-fluid">
                @include('voyager::alerts')
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-bordered">
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table id="dataTable" class="dataTable table-hover">
                                        <thead>
                                            <tr>
                                                <th style="text-align: center">Nro&deg;</th>
                                                <th style="text-align: center">Nit</th>
                                                <th style="text-align: center">Razón Social</th>
                                                <th style="text-align: center">Responsable</th>
                                                {{-- <th style="text-align: center">direcion</th> --}}
                                                @if (auth()->user()->hasRole('admin'))
                                                    <th style="text-align: center">Sucursal</th>
                                                @endif
                                                <th style="text-align: center">Estado</th>
                                                <th style="text-align: right">Aciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($provider as $item)
                                                <tr>
                                                    <td>{{$item->id}}</td>
                                                    <td style="text-align: center">{{$item->nit}}</td>
                                                    <td style="text-align: center">{{$item->razonsocial}}</td>
                                                    <td style="text-align: center">{{$item->responsable}}</td>
                                                    {{-- <td style="text-align: center">{{$item->direccion}}</td> --}}
                                                    @if (auth()->user()->hasRole('admin'))
                                                        <td style="text-align: center"><label class="label label-primary">{{$item->sucursal->nombre}}</label></td>                                                        
                                                    @endif
                                                    <td style="text-align: center">
                                                        @if ($item->condicion == 1)
                                                            <label class="label label-success">Activo</label>
                                                        @else
                                                            <label class="label label-danger">Inactivo</label>
                                                        @endif
                                                    </td>
                                                    <td style="text-align: right">
                                                        <div class="no-sort no-click bread-actions text-right">
                                                            @if(auth()->user()->hasPermission('read_providers'))
                                                                <a href="{{route('voyager.providers.show',$item->id)}}" title="Ver" class="btn btn-sm btn-warning view">
                                                                    <i class="voyager-eye"></i> <span class="hidden-xs hidden-sm">Ver</span>
                                                                </a>                                                          
                                                            @endif
                                                            @if(auth()->user()->hasPermission('edit_providers'))
                                                                <a href="{{route('voyager.providers.edit',$item->id)}}" title="Editar" class="btn btn-sm btn-primary view">
                                                                    <i class="voyager-edit"></i> <span class="hidden-xs hidden-sm">Editar</span>
                                                                </a>                                                          
                                                            @endif
                                                            {{-- @if(auth()->user()->hasPermission('edit_sucursals'))
                                                                <a href="{{route('sucursal-da.index',['sucursal'=>$item->id])}}" title="Direcciones Administrativa" class="btn btn-sm btn-warning view">
                                                                    <i class="fa-solid fa-file"></i> <span class="hidden-xs hidden-sm">DA</span>
                                                                </a>                                                          
                                                            @endif --}}
                                                        </div>
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
           
    @stop


    @section('css')
    <style>
        #subtitle{
        font-size: 18px;
        color: rgb(12, 12, 12);
        font-weight: bold;
    }
        #dataTable {
        font-family: Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
        }

        #dataTable td, #dataTable th {
        border: 1px solid #ddd;
        padding: 8px;
        }

        #dataTable tr:nth-child(even){background-color: #f2f2f2;}

        #dataTable tr:hover {background-color: #ddd;}

        #dataTable th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: #04AA6D;
            color: white;
        }
        
        #subtitle{
            font-size: 18px;
            color: rgb(12, 12, 12);
            font-weight: bold;
        }
        small{font-size: 12px;
        color: rgb(12, 12, 12);
        font-weight: bold;
    }

    </style>
    @stop

    @section('javascript')
            <script>
                $(document).ready(function(){
                    $('.dataTable').DataTable({
                        language: {
                            sProcessing: "Procesando...",
                            sLengthMenu: "Mostrar _MENU_ registros",
                            sZeroRecords: "No se encontraron resultados",
                            sEmptyTable: "Ningún dato disponible en esta tabla",
                            sInfo: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                            sInfoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
                            sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
                            sSearch: "Buscar:",
                            sInfoThousands: ",",
                            sLoadingRecords: "Cargando...",
                            oPaginate: {
                                sFirst: "Primero",
                                sLast: "Último",
                                sNext: "Siguiente",
                                sPrevious: "Anterior"
                            },
                            oAria: {
                                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                            },
                            buttons: {
                                copy: "Copiar",
                                colvis: "Visibilidad"
                            }
                        },
                        order: [[ 0, 'desc' ]],
                    })
                });


                $('#myModalEliminar').on('show.bs.modal', function (event) {
                    var button = $(event.relatedTarget) //captura valor del data-empresa=""

                    var id = button.data('id')

                    var modal = $(this)
                    modal.find('.modal-body #id').val(id)
                    
                });

            </script>
    @stop

@else
    @section('content')
        <h1>No tienes permiso</h1>
    @stop
@endif
