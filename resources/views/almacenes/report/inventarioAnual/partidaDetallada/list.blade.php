
<div class="col-md-12 text-right">

    <button type="button" onclick="report_excel()" class="btn btn-success"><i class="fa-solid fa-file-excel"></i> Excel</button>
    <button type="button" onclick="report_print()" class="btn btn-dark"><i class="glyphicon glyphicon-print"></i> Imprimir</button>

</div>


<div class="col-md-12">
<div class="panel panel-bordered">
    <div class="panel-body">
        <div class="table-responsive">
            <table id="dataTableStyle" style="width:100%"  class="table table-bordered table-striped table-sm">
                <thead>
                    <tr>
                        <th style="width:5px" rowspan="2">N&deg;</th>
                        <th style="text-align: center" rowspan="2">DESCRIPCION</th>
                        <th style="text-align: center" rowspan="2">CODIGO</th>
                        <th style="text-align: center" rowspan="2">PRESENTACIÓN</th>
                        <th style="text-align: center" colspan="2">COMPRAS</th>
                        <th style="text-align: center" colspan="2">SALIDA</th>
                    </tr>
                    <tr>
                        <th style="text-align: center">CANTIDAD</th>
                        <th style="text-align: center">SUB TOTAL BS</th>
                        <th style="text-align: center">CANTIDAD</th>
                        <th style="text-align: center">TOTAL BS</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $count = 1;
                        $cantidad_total = 0;
                        $total_bs = 0;
                        $cant_to_sal = 0;
                        $total_bs_sal = 0;
                    @endphp

                    @forelse ($data as $item)
                        <tr style="text-align: center">
                            <td>{{ $count }}</td>
                            <td>{{ $item->nombre }}</td>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->presentacion }}</td>
                            <td style="text-align: right">{{ number_format($item->total_cantsolicitada,2, ',', '.')}}</td>
                            <td style="text-align: right">{{ number_format($item->total_totalbs,2, ',', '.')}}</td>
                            <td style="text-align: right">{{ number_format($item->total_cantidad_salida,2, ',', '.')}}</td>
                            <td style="text-align: right">{{ number_format($item->total_totalbs_salida, 2, ',', '.')}}</td>
                        </tr>
                        @php
                            $count++;
                            $cantidad_total += $item->total_cantsolicitada;
                            $total_bs += $item->total_totalbs;
                            $cant_to_sal += $item->total_cantidad_salida;
                            $total_bs_sal += $item->total_totalbs_salida;
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
                        <th>{{number_format($cant_to_sal,2,',','.')}}</th>
                        <th>{{number_format($total_bs_sal,2,',','.')}}</th>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
<style>
.table-responsive {
    position: relative;
    height: 700px;
    overflow: auto;
}
.table thead th {
    position: sticky;
    top: 0;
    background-color: white; 
    z-index: 1;
    border-bottom: 2px solid #dee2e6;
}
.table thead tr:nth-child(2) th {
    top: 45px; 
    z-index: 1;
}
</style>
<script>
$(document).ready(function(){

})
</script>