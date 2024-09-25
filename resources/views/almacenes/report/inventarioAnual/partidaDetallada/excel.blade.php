<div style="margin-top: 20px">
    <table>
        <thead>
            <tr>
                <th rowspan="2"><strong>N&deg;</strong></th>
                <th rowspan="2"><strong>DESCRIPCION</strong></th>
                <th rowspan="2"><strong>CODIGO</strong></th>
                <th rowspan="2"><strong>PRESENTACIÓN</strong></th>
                <th colspan="2"><strong>COMPRAS</strong></th>
            </tr>
            <tr>
                <th><strong>CANTIDAD</strong></th>
                <th><strong>TOTAL BS</strong></th>
            </tr>
        </thead>
        <tbody>
                    @php
                        $count = 1;
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
                        @endphp
                    @empty
                        <tr>
                            <td colspan="6">No se encontraron registros.</td>
                        </tr>
                    @endforelse
            {{-- <tr>
                <th colspan="2" style="text-align: right">Total</th>
                <th style="text-align: right">{{number_format($cant1,2,',', '.')}}</th>
                <th style="text-align: right">{{number_format($total1,2,',', '.')}}</th>
                <th style="text-align: right">{{number_format($cant2,2,',', '.')}}</th>
                <th style="text-align: right">{{number_format($total2,2,',', '.')}}</th>
            </tr> --}}
        </tbody>
    
    </table>
</div>
