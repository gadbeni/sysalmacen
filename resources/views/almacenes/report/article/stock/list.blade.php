
<div class="col-md-12 text-right">

    <button type="button" onclick="report_excel()" class="btn btn-success"><i class="fa-solid fa-file-excel"></i> Exportar a Excel</button>
    <button type="button" onclick="report_print()" class="btn btn-dark"><i class="glyphicon glyphicon-print"></i> Imprimir</button>

</div>
<div class="col-md-12">
<div class="panel panel-bordered">
    <div class="panel-body">
        <div class="table-responsive">
            <table id="dataTableStyle" style="width:100%"  class="table table-bordered table-striped table-sm">
                <thead>
                    <tr>
                        <th>N&deg;</th>
                        <th style="width:15%">F. INGRESO</th>
                        <th style="width:25%">ENTIDAD + NRO COMPRA</th>
                        <th style="width:25%">PROVEEDOR</th>
                        <th style="width:25%">NRO</th>
                        <th style="width:25%">ARTICULO</th>
                        <th style="width:25%">CODIGO ART</th>
                        <th style="width:25%">PRESENTACION</th>
                        <th style="width:25%">PRECIO</th>
                        <th style="width:25%">CANT.</th>
                        <th style="width:15%">SUBTOTAL</th>
                    </tr>
                </thead>
                <tbody>
                        @php
                            $count = 1;
                            $total = 0;
                            $cant = 0;
                        @endphp
                        @forelse ($data as $item)
                            <tr style="text-align: center">
                                <td>{{ $count }}</td>
                                <td>{{date('d/m/Y', strtotime($item->fechaingreso))}}</td>
                                <td>{{ $item->modalidad }} <br>{{$item->nrosolicitud}} </td>
                                @if ($item->proveedor)
                                    <td>{{ $item->proveedor }}</td>
                                @else
                                    <td>SIN PROVEEDOR</td>
                                @endif
                                
                                <td>{{ $item->tipofactura=='Orden_Compra'? 'Orden de Compra':'Nro Factura'}}<br>{{$item->nrofactura}}</td>
                                
                                <td>{{ $item->articulo }}</td>
                                <td>{{ $item->article_id }}</td>
                                <td>{{ $item->presentacion }}</td>
                                <td>{{ $item->precio }}</td>
                                <td>{{ $item->cantrestante }}</td>
                                <td>{{ $item->totalbs }}</td>


                                                                                          
                            </tr>
                            @php
                                $count++;
                                $total = $total + ($item->cantrestante * $item->precio);
                                $cant = $cant + $item->cantrestante;
                            @endphp
                        @empty
                            <tr style="text-align: center">
                                <td colspan="10">No se encontraron registros.</td>
                            </tr>
                        @endforelse
                        <tr>
                            <th colspan="8" class="text-right"><strong>TOTAL</strong></th>
                            <th><strong>{{number_format($cant,2)}}</strong></th>
                            <th><strong>{{number_format($total,2)}}</strong></th>
                        </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>

<script>
$(document).ready(function(){

})
</script>