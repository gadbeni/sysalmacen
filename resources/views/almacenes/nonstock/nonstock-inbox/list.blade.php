<div class="col-md-12">
    <div class="table-responsive">
        <table id="dataTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th style="text-align: center">Nro&deg;</th>
                    <th style="text-align: center">Solicitante</th>
                    <th style="text-align: center">Oficina</th>
                    <th style="text-align: center">Nro Pedido</th>
                    <th class="text-align: center">Fecha Solicitud</th>

                    <th style="text-align: center">Fecha Registro</th>
                    @if(auth()->user()->hasRole(['admin']))
                        <th style="text-align: center">Sucursal</th>
                    @endif
                    <th class="text-align: center">Estado</th>
                    {{-- @if(auth()->user()->hasPermission('read_egres')||auth()->user()->hasPermission('edit_egres')||auth()->user()->hasPermission('delete_egres')) --}}
                        <th style="width: 120px">Accion</th>
                    {{-- @endif --}}
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $item)
                    <tr style="text-align: center">
                        <td>{{$item->id}}</td>
                        <td style="text-align: center">{{strtoupper($item->user->name.' - '.$item->job)}}</td>                        

                        <td style="text-align: center">
                            <p><small>Gestion: {{$item->gestion}}</small></p>
                            <p><small>{{$item->unit_name}}</small></p>
                            <p><small>{{$item->direction_name}}</small></p>
                        </td>
                        <td>
                            <p><small>{{$item->nro_request}}</small></p>
                        </td>

                        <td>{{date('d/m/Y', strtotime($item->date_request))}}</td>
                        <td style="text-align: center">{{date('d/m/Y H:i:s', strtotime($item->created_at))}}<br><small>{{\Carbon\Carbon::parse($item->created_at)->diffForHumans()}}.</small></td>


                    
                        @if(auth()->user()->hasRole(['admin']))
                            <td style="text-align: center"><label class="label label-dark">{{$item->sucursal->nombre}}</label></td>
                        @endif
                        <td style="text-align: center">
                            @if ($item->status == 'enviado')
                                <label class="label label-warning">Pendiente</label>
                            @endif
                            @if ($item->status == 'rechazado')
                                <label class="label label-danger">Rechazado</label>
                            @endif
                            @if ($item->status == 'aprobado')
                                <label class="label label-info">Aprobado</label>
                            @endif
                            @if ($item->status == 'entregado')
                                <label class="label label-success">Entregado</label>
                            @endif
                        </td>
                        <td style="text-align: right">
                            <div class="no-sort no-click bread-actions text-right">


                                @if (auth()->user()->hasPermission('read_inbox'))
                                    <a href="{{route('nonstock.inbox.view',$item->id)}}" title="Ver Solicitud" class="btn btn-sm btn-warning view">
                                        <i class="fa-solid fa-eye"></i> Ver
                                    </a>  
                                @endif
                                
                            </div>
                        </td>                        
                    </tr>
                @empty
                    <tr>
                        @if(auth()->user()->hasRole(['admin']))
                            <td colspan="9">
                                <h5 class="text-center" style="margin-top: 50px">
                                    <img src="{{ asset('images/empty.png') }}" width="120px" alt="" style="opacity: 0.5"> <br>
                                    No hay resultados
                                </h5>
                            </td>
                        @else
                            <td colspan="8">
                                <h5 class="text-center" style="margin-top: 50px">
                                    <img src="{{ asset('images/empty.png') }}" width="120px" alt="" style="opacity: 0.5"> <br>
                                    No hay resultados
                                </h5>
                            </td>
                        @endif
                    </tr>
                @endforelse                                               
            </tbody>
        </table>
    </div>
</div>

<div class="col-md-12">
    <div class="col-md-4" style="overflow-x:auto">
        @if(count($data)>0)
            <p class="text-muted">Mostrando del {{$data->firstItem()}} al {{$data->lastItem()}} de {{$data->total()}} registros.</p>
        @endif
    </div>
    <div class="col-md-8" style="overflow-x:auto">
        <nav class="text-right">
            {{ $data->links() }}
        </nav>
    </div>
</div>

<script>
   
   var page = "{{ request('page') }}";
    $(document).ready(function(){
        $('.page-link').click(function(e){
            e.preventDefault();
            let link = $(this).attr('href');
            if(link){
                page = link.split('=')[1];
                list(page);
            }
        });
    });
</script>