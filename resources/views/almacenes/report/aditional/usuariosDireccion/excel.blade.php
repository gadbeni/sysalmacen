<table border="1">
    <thead>
        <tr>
            <th colspan="8" style="text-align:center; font-weight:bold">
                {{ strtoupper($sucursal->nombre) }} - DISTRIBUCIÓN DE USUARIOS POR DIRECCIÓN Y UNIDAD ADMINISTRATIVA
            </th>
        </tr>
        <tr>
            <th>DIRECCIÓN ADMINISTRATIVA</th>
            <th>UNIDAD ADMINISTRATIVA</th>
            <th>SIGLA</th>
            <th>CI</th>
            <th>NOMBRE COMPLETO</th>
            <th>CORREO</th>
            <th>ROL</th>
            <th>ÚLTIMO ACCESO</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $dirItem)
            @php $dir = $dirItem['direccion']; @endphp
            @php $dirNombre = strtoupper($dir->nombre) . ($dir->sigla ? ' (' . $dir->sigla . ')' : ''); @endphp
            @forelse ($dirItem['unidades'] as $uItem)
                @php
                    $unidad    = $uItem['unidad'];
                    $usuarios  = $uItem['usuarios'];
                    $userCount = count($usuarios);
                @endphp
                @if($userCount > 0)
                    @foreach ($usuarios as $usr)
                        <tr>
                            <td>{{ $dirNombre }}</td>
                            <td>{{ $unidad->nombre }}</td>
                            <td>{{ $unidad->sigla ?? '-' }}</td>
                            <td>{{ $usr->ci ?? '-' }}</td>
                            <td>{{ $usr->nombre ?? '' }}</td>
                            <td>{{ $usr->email ?? '-' }}</td>
                            <td>{{ $usr->rol ?? '-' }}</td>
                            <td>{{ $usr->last_login_at ? \Carbon\Carbon::parse($usr->last_login_at)->format('d/m/Y H:i') : '-' }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td>{{ $dirNombre }}</td>
                        <td>{{ $unidad->nombre }}</td>
                        <td>{{ $unidad->sigla ?? '-' }}</td>
                        <td colspan="5">Sin usuarios asignados</td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td>{{ $dirNombre }}</td>
                    <td colspan="7">No hay unidades registradas para esta dirección.</td>
                </tr>
            @endforelse
        @endforeach
    </tbody>
</table>
