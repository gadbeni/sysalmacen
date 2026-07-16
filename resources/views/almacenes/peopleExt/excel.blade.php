<table border="1">
    <thead>
        <tr>
            <th colspan="13" style="text-align:center; font-weight:bold">
                PERSONAS EXTERNAS
            </th>
        </tr>
        <tr>
            <th>ID</th>
            <th>CI</th>
            <th>NOMBRE COMPLETO</th>
            <th>CARGO</th>
            <th>INICIO</th>
            <th>FIN</th>
            <th>ESTADO</th>
            <th>CORREO USUARIO</th>
            <th>ROL</th>
            <th>ALMACÉN</th>
            <th>SUB-ALMACÉN</th>
            <th>DIRECCIÓN</th>
            <th>UNIDAD</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $item)
            @php
                $nombre = trim(($item->people->first_name ?? '') . ' ' . ($item->people->middle_name ?? '') . ' ' . ($item->people->paternal_surname ?? '') . ' ' . ($item->people->maternal_surname ?? '') . ' ' . ($item->people->married_surname ?? ''));
                $estado = $item->status == 1 ? 'ACTIVO' : 'INACTIVO';
            @endphp
            @forelse ($item->users as $usuario)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->people->ci ?? '-' }}</td>
                    <td>{{ $nombre }}</td>
                    <td>{{ $item->cargo }}</td>
                    <td>{{ $item->start }}</td>
                    <td>{{ $item->finish }}</td>
                    <td>{{ $estado }}</td>
                    <td>{{ $usuario->email }}</td>
                    <td>{{ $usuario->role->display_name ?? '-' }}</td>
                    <td>{{ $usuario->sucursal->nombre ?? '-' }}</td>
                    <td>{{ $usuario->subAlmacen->name ?? '-' }}</td>
                    <td>{{ $usuario->direction->nombre ?? '-' }}</td>
                    <td>{{ $usuario->unit->nombre ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->people->ci ?? '-' }}</td>
                    <td>{{ $nombre }}</td>
                    <td>{{ $item->cargo }}</td>
                    <td>{{ $item->start }}</td>
                    <td>{{ $item->finish }}</td>
                    <td>{{ $estado }}</td>
                    <td colspan="6">Sin usuario asignado</td>
                </tr>
            @endforelse
        @endforeach
    </tbody>
</table>
