@extends('voyager::master')

@section('page_title', 'Viendo DA sucursal')

@if(auth()->user()->hasPermission('browse_sucursals'))
    @section('page_header')
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <h1 id="subtitle" class="page-title">
                        <i class="fa-solid fa-file"></i> Direcciones Adminsitrativa [{{$sucursal->nombre}}]
                    </h1>
                    @if(auth()->user()->hasPermission('add_sucursals'))
                        <a data-toggle="modal" data-target="#modal-create" class="btn btn-success btn-add-new">
                            <i class="voyager-plus"></i> <span>Crear</span>
                        </a>
                    @endif
                    <a href="{{ route('voyager.sucursals.index') }}" class="btn btn-warning btn-add-new">
                        <i class="fa-solid fa-arrow-rotate-left"></i> <span>Volver</span>
                    </a>
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
                                <div class="row" style="margin-bottom:20px;">
                                    {{-- Card: Unidades Principales --}}
                                    <div class="col-md-6">
                                        <div class="panel panel-default" style="border-radius:6px; box-shadow:0 1px 4px rgba(0,0,0,.08);">
                                            <div class="panel-heading" style="background:#f8f9fa; border-bottom:1px solid #e0e0e0; padding:10px 15px; display:flex; justify-content:space-between; align-items:center; border-radius:6px 6px 0 0;">
                                                <span style="font-weight:600; font-size:13px;"><i class="fa-solid fa-shop" style="color:#00874C;"></i> Unidades Principales</span>
                                                <a data-toggle="modal" data-target="#modal-almacen" class="btn btn-xs btn-success">
                                                    <i class="voyager-plus"></i> Agregar
                                                </a>
                                            </div>
                                            <div class="panel-body" style="padding:0;">
                                                <table class="table table-hover table-condensed" style="margin:0;">
                                                    <thead style="background:#00874C; color:white;">
                                                        <tr>
                                                            <th style="padding:8px 12px;">Unidad</th>
                                                            <th style="padding:8px 12px; text-align:center; width:60px;">Acción</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse ($principal as $item)
                                                            <tr>
                                                                <td style="padding:8px 12px; font-size:13px;">{{$item->unidad->nombre}}</td>
                                                                <td style="text-align:center; padding:6px;">
                                                                    <a data-toggle="modal" data-target="#modal-deleteUnidad" data-id="{{$item->id}}" class="btn btn-xs btn-danger view" title="Eliminar">
                                                                        <i class="fa-solid fa-trash"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr><td colspan="2" class="text-center text-muted" style="padding:14px; font-size:12px;">Sin unidades asignadas</td></tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Card: Sub Almacenes --}}
                                    <div class="col-md-6">
                                        <div class="panel panel-default" style="border-radius:6px; box-shadow:0 1px 4px rgba(0,0,0,.08);">
                                            <div class="panel-heading" style="background:#f8f9fa; border-bottom:1px solid #e0e0e0; padding:10px 15px; display:flex; justify-content:space-between; align-items:center; border-radius:6px 6px 0 0;">
                                                <span style="font-weight:600; font-size:13px;"><i class="fa-solid fa-warehouse" style="color:#00874C;"></i> Sub Almacenes</span>
                                                <a data-toggle="modal" data-target="#modal-subalmacen" class="btn btn-xs btn-success">
                                                    <i class="voyager-plus"></i> Agregar
                                                </a>
                                            </div>
                                            <div class="panel-body" style="padding:0;">
                                                <table class="table table-hover table-condensed" style="margin:0;">
                                                    <thead style="background:#00874C; color:white;">
                                                        <tr>
                                                            <th style="padding:8px 12px;">Nombre</th>
                                                            <th style="padding:8px 12px; text-align:center; width:60px;">Acción</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse ($sub as $item)
                                                            <tr>
                                                                <td style="padding:8px 12px; font-size:13px;">{{$item->name}}</td>
                                                                <td style="text-align:center; padding:6px;">
                                                                    <a data-toggle="modal" data-target="#modal-deleteSubalmacen" data-id="{{$item->id}}" class="btn btn-xs btn-danger view" title="Eliminar">
                                                                        <i class="fa-solid fa-trash"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr><td colspan="2" class="text-center text-muted" style="padding:14px; font-size:12px;">Sin sub almacenes</td></tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Tabla principal: Direcciones Administrativas --}}
                                <div class="row">
                                    <div class="col-md-12">
                                        <table id="dataTable" class="dataTable table-hover">
                                            <thead>
                                                <tr>
                                                    <th style="text-align:center">Nro&deg;</th>
                                                    <th style="text-align:center">Dirección Administrativa</th>
                                                    <th style="text-align:center">Estado</th>
                                                    <th style="text-align:center">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($data as $item)
                                                    <tr>
                                                        <td>{{$item->id}}</td>
                                                        <td style="text-align:center">{{$item->nombre}}</td>
                                                        <td style="text-align:center">
                                                            @if ($item->status == 1)
                                                                <label class="label label-success">Activo</label>
                                                            @else
                                                                <label class="label label-danger">Inactivo</label>
                                                            @endif
                                                        </td>
                                                        <td style="text-align:center">
                                                            <div class="no-sort no-click bread-actions text-right">
                                                                @if($item->status == 1)
                                                                    <a data-toggle="modal" data-target="#modal-inhabilitar" data-id="{{$item->id}}" class="btn btn-sm btn-warning view">
                                                                        <i class="fa-solid fa-thumbs-down"></i> Inhabilitar
                                                                    </a>
                                                                @else
                                                                    <a data-toggle="modal" data-target="#modal-habilitar" data-id="{{$item->id}}" class="btn btn-sm btn-success view">
                                                                        <i class="fa-solid fa-thumbs-up"></i> Habilitar
                                                                    </a>
                                                                @endif
                                                                <a data-toggle="modal" data-target="#myModalEliminar" data-id="{{$item->id}}" class="btn btn-sm btn-danger view">
                                                                    <i class="fa-solid fa-trash"></i> Eliminar
                                                                </a>
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
            </div>
            {{-- @php
    $data = \App\Models\SucursalUser::where('user_id',Auth::user()->id)->where('condicion',1)->first();
    dd($data->sucursal_id);
@endphp --}}
            <div class="modal fade modal-success" role="dialog" id="modal-create">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">                
                        <!-- Modal Header -->
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title"><i class="voyager-plus"></i>Registrar</h4>
                        </div>
                        {!! Form::open(['route' => 'sucursal-da.store','class' => 'was-validated'])!!}
                            <!-- Modal body -->
                            <input type="hidden" value="{{$sucursal->id}}" name="sucursal_id">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><b>Direcciones Administrativa:</b></span>
                                        </div>
                                        <select name="direccion_id" class="form-control select2" required>
                                            <option value="">Seleccione una dirección..</option>
                                            @foreach($da as $item)
                                                    <option value="{{$item->id}}">{{$item->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>     
        
                            </div>
                            
                            <!-- Modal footer -->
                            <div class="modal-footer justify-content-between">
                                <button type="button text-left" class="btn btn-danger" data-dismiss="modal" data-toggle="tooltip" title="Volver">Cancelar
                                </button>
                                <button type="submit" class="btn btn-success btn-sm" title="Registrar..">
                                    Registrar
                                </button>
                            </div>
                        {!! Form::close()!!} 
                        
                    </div>
                </div>
            </div>
            <!-- Modal -->
            <div class="modal modal-danger fade" tabindex="-1" id="myModalEliminar" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        {!! Form::open(['route' => 'sucursal-da.destroy', 'method' => 'DELETE']) !!}
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title"><i class="voyager-trash"></i> Desea eliminar la Dirección Administrativa?</h4>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id" id="id">
                            <input type="hidden" value="{{$sucursal->id}}" name="sucursal_id">
                            <div class="text-center" style="text-transform:uppercase">
                                <i class="voyager-trash" style="color: red; font-size: 5em;"></i>
                                <br>
                                
                                <p><b>Desea eliminar el siguiente registro?</b></p>
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
            {{-- modal para inhabilitar --}}
            <div class="modal modal-warning fade" tabindex="-1" id="modal-inhabilitar" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        {!! Form::open(['route' => 'sucursal-da.inhabilitar', 'method' => 'POST']) !!}
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title"><i class="fa-solid fa-triangle-exclamation"></i> Desea Inhabilitar la Dirección Administrativa?</h4>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id" id="id">
                            <input type="hidden" value="{{$sucursal->id}}" name="sucursal_id">
                            <div class="text-center" style="text-transform:uppercase">
                                <i class="fa-solid fa-triangle-exclamation" style="color: #fabe28; font-size: 5em;"></i>
                                <br>
                                
                                <p><b>Desea inhabilitar el siguiente registro?</b></p>
                            </div>
                        </div>                
                        <div class="modal-footer">
                            
                                <input type="submit" class="btn btn-warning pull-right delete-confirm" value="Sí, Inhabilitar">
                            
                            <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancelar</button>
                        </div>
                        {!! Form::close()!!} 
                    </div>
                </div>
            </div>
            {{-- modal para habilitar --}}
            <div class="modal fade" tabindex="-1" id="modal-habilitar" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content modal-success">
                        {!! Form::open(['route' => 'sucursal-da.habilitar', 'method' => 'POST']) !!}
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title"><i class="fa-solid fa-check"></i> Desea Habilitar la Dirección Administrativa?</h4>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id" id="id">
                            <input type="hidden" value="{{$sucursal->id}}" name="sucursal_id">
                            <div class="text-center" style="text-transform:uppercase">
                                <i class="fa-solid fa-check" style="color: #42d07e; font-size: 5em;"></i>
                                <br>
                                
                                <p><b>Desea habilitar el siguiente registro?</b></p>
                            </div>
                        </div>                
                        <div class="modal-footer">
                            
                                <input type="submit" class="btn btn-success pull-right delete-confirm" value="Sí, Habilitar">
                            
                            <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancelar</button>
                        </div>
                        {!! Form::close()!!} 
                    </div>
                </div>
            </div>


            {{-- Para agregar unidades como almacen princial --}}
            <div class="modal fade modal-success" role="dialog" id="modal-almacen">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">                
                        <!-- Modal Header -->
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title"><i class="voyager-plus"></i>Almacenes Principales</h4>
                        </div>
                        {!! Form::open(['route' => 'sucursal-unidad.store','class' => 'was-validated'])!!}
                            <!-- Modal body -->
                            <input type="hidden" value="{{$sucursal->id}}" name="sucursal_id">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><b>Direcciones Administrativa:</b></span>
                                        </div>
                                        <select name="direccion_id" id="direccion_id" class="form-control select2" required>
                                            <option value="">Seleccione una dirección..</option>
                                            @foreach($data as $item)
                                                    <option value="{{$item->direccion_id}}">{{$item->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>     
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><b>Unidad Administrativa:</b></span>
                                        </div>
                                        <select name="unidad_id" id="unidad_id" class="form-control select2" required>
                                            <option value="">Seleccione una unidad..</option>
                                            
                                        </select>
                                    </div>
                                </div> 

                            </div>
                            
                            <!-- Modal footer -->
                            <div class="modal-footer justify-content-between">
                                <button type="button text-left" class="btn btn-danger" data-dismiss="modal" data-toggle="tooltip" title="Volver">Cancelar
                                </button>
                                <button type="submit" class="btn btn-success btn-sm" title="Registrar..">
                                    Agregar
                                </button>
                            </div>
                        {!! Form::close()!!} 
                        
                    </div>
                </div>
            </div>
            {{-- para eliminar la unidad principal --}}
            <div class="modal modal-danger fade" tabindex="-1" id="modal-deleteUnidad" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        {!! Form::open(['route' => 'sucursal-unidad.destroy', 'method' => 'DELETE']) !!}
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title"><i class="voyager-trash"></i> Desea eliminar la Unidad Administrativa?</h4>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id" id="sucursalUnidad">
                            <input type="hidden" value="{{$sucursal->id}}" name="sucursal_id">
                            <div class="text-center" style="text-transform:uppercase">
                                <i class="voyager-trash" style="color: red; font-size: 5em;"></i>
                                <br>
                                
                                <p><b>Desea eliminar el siguiente registro?</b></p>
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



            {{-- Para registrar sub almacen de cada almacen --}}
            <div class="modal fade modal-success" role="dialog" id="modal-subalmacen">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">                
                        <!-- Modal Header -->
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title"><i class="voyager-plus"></i>Sub Almacenes</h4>
                        </div>
                        {!! Form::open(['route' => 'sucursal-subalmacen.store','class' => 'was-validated'])!!}
                            <!-- Modal body -->
                            <input type="hidden" value="{{$sucursal->id}}" name="sucursal_id">
                            <div class="modal-body">   
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><b>Nombre Del Sub Almacen:</b></span>
                                        </div>
                                        {{-- <select name="unidad_id" id="unidad_id" class="form-control select2" required>
                                            <option value="">Seleccione una unidad..</option>
                                            
                                        </select> --}}
                                        <input type="text" id="name" name="name" required class="form-control">
                                    </div>
                                </div> 

                            </div>
                            
                            <!-- Modal footer -->
                            <div class="modal-footer justify-content-between">
                                <button type="button text-left" class="btn btn-danger" data-dismiss="modal" data-toggle="tooltip" title="Volver">Cancelar
                                </button>
                                <button type="submit" class="btn btn-success btn-sm" title="Registrar..">
                                    Agregar
                                </button>
                            </div>
                        {!! Form::close()!!} 
                        
                    </div>
                </div>
            </div>
            <div class="modal modal-danger fade" tabindex="-1" id="modal-deleteSubalmacen" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        {!! Form::open(['route' => 'sucursal-subalmacen.destroy', 'method' => 'DELETE']) !!}
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title"><i class="voyager-trash"></i> Eliminar Sub Almacen</h4>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id" id="subalmacen_id">
                            <input type="hidden" value="{{$sucursal->id}}" name="sucursal_id">
                            <div class="text-center" style="text-transform:uppercase">
                                <i class="voyager-trash" style="color: red; font-size: 5em;"></i>
                                <br>
                                
                                <p><b>Desea eliminar el siguiente registro?</b></p>
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
                $(function()
                {
                    $('#direccion_id').on('change', unidad_administrativa);
                })
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
                $('#modal-inhabilitar').on('show.bs.modal', function (event) {
                    var button = $(event.relatedTarget) //captura valor del data-empresa=""

                    var id = button.data('id')

                    var modal = $(this)
                    modal.find('.modal-body #id').val(id)
                    
                });
                $('#modal-habilitar').on('show.bs.modal', function (event) {
                    var button = $(event.relatedTarget) //captura valor del data-empresa=""

                    var id = button.data('id')

                    var modal = $(this)
                    modal.find('.modal-body #id').val(id)
                    
                });


                function unidad_administrativa()
                {
                    var id =  $(this).val();    
                    // alert(id)
                    if(id >=1)
                    {
                        $.get('{{route('ajax_unidad_administrativa')}}/'+id, function(data){
                            var html_unidad=    '<option value="">Seleccione una unidad..</option>'
                                for(var i=0; i<data.length; ++i)
                                html_unidad += '<option value="'+data[i].id+'">'+data[i].nombre+'</option>'

                            $('#unidad_id').html(html_unidad);;            
                        });
                    }
                    else
                    {
                        var html_unidad=    ''       
                        $('#unidad_id').html(html_unidad);
                    }
                }
                
                $('#modal-deleteUnidad').on('show.bs.modal', function (event) {
                    var button = $(event.relatedTarget) //captura valor del data-empresa=""

                    var id = button.data('id')

                    var modal = $(this)
                    modal.find('.modal-body #sucursalUnidad').val(id)
                    
                });
                $('#modal-deleteSubalmacen').on('show.bs.modal', function (event) {
                    var button = $(event.relatedTarget) //captura valor del data-empresa=""

                    var id = button.data('id')

                    var modal = $(this)
                    modal.find('.modal-body #subalmacen_id').val(id)
                    
                });

            </script>
    @stop

@else
    @section('content')
        <h1>No tienes permiso</h1>
    @stop
@endif
