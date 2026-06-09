<div class="col-md-12 text-right" style="margin-bottom: 8px">
    <button type="button" onclick="report_print()" class="btn btn-dark">
        <i class="glyphicon glyphicon-print"></i> Imprimir
    </button>
</div>

<div class="col-md-12">
    <div class="panel panel-bordered">
        <div class="panel-body">

            {{-- Encabezado del almacén --}}
            <div style="background:#1a3a5c; color:#fff; padding:10px 14px; border-radius:4px; margin-bottom:16px; font-size:15px">
                <i class="voyager-shop"></i>
                &nbsp;<strong>ALMACÉN:</strong> {{ strtoupper($sucursal->nombre) }}
            </div>

            @forelse ($data as $dirItem)
                @php $dir = $dirItem['direccion']; @endphp

                {{-- Fila de cabecera: Dirección Administrativa --}}
                <div style="background:#2d6a9f; color:#fff; padding: 6px 10px; margin-top: 12px; border-radius: 4px 4px 0 0">
                    <strong>
                        <i class="voyager-folder"></i>
                        DIRECCIÓN ADMINISTRATIVA: {{ strtoupper($dir->nombre) }}
                        @if($dir->sigla) ({{ $dir->sigla }}) @endif
                    </strong>
                </div>

                <div class="table-responsive" style="margin-bottom: 0">
                    <table class="table table-bordered table-sm" style="margin-bottom: 0; font-size: 13px">
                        <thead style="background: #e8f0fe">
                            <tr>
                                <th style="width:4%; text-align:center">N°</th>
                                <th style="width:30%">UNIDAD ADMINISTRATIVA</th>
                                <th style="width:8%; text-align:center">SIGLA</th>
                                <th style="width:10%; text-align:center">CI</th>
                                <th style="width:25%">NOMBRE COMPLETO</th>
                                <th style="width:18%">CORREO</th>
                                <th style="width:15%">ROL</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $numUnidad = 1; @endphp
                            @forelse ($dirItem['unidades'] as $uItem)
                                @php
                                    $unidad    = $uItem['unidad'];
                                    $usuarios  = $uItem['usuarios'];
                                    $userCount = count($usuarios);
                                @endphp
                                @if($userCount > 0)
                                    @foreach ($usuarios as $usr)
                                        <tr>
                                            @if($loop->first)
                                                <td style="text-align:center; vertical-align:middle" rowspan="{{ $userCount }}">{{ $numUnidad }}</td>
                                                <td style="vertical-align:middle" rowspan="{{ $userCount }}">{{ $unidad->nombre }}</td>
                                                <td style="text-align:center; vertical-align:middle" rowspan="{{ $userCount }}">
                                                    {{ $unidad->sigla ?? '-' }}
                                                </td>
                                            @endif
                                            <td style="text-align:center; vertical-align:middle">{{ $usr->ci ?? '—' }}</td>
                                            <td style="vertical-align:middle">{{ $usr->nombre ?? '' }}</td>
                                            <td style="vertical-align:middle">{{ $usr->email ?? '—' }}</td>
                                            <td style="vertical-align:middle">
                                                @if($usr->rol)
                                                    <span class="label label-default" style="font-size:10px">{{ $usr->rol }}</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td style="text-align:center; vertical-align:middle">{{ $numUnidad }}</td>
                                        <td style="vertical-align:middle">{{ $unidad->nombre }}</td>
                                        <td style="text-align:center; vertical-align:middle">
                                            {{ $unidad->sigla ?? '-' }}
                                        </td>
                                        <td colspan="4" class="text-center text-muted" style="font-style:italic">
                                            <i class="voyager-warning"></i> Sin usuarios asignados
                                        </td>
                                    </tr>
                                @endif
                                @php $numUnidad++; @endphp
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted" style="font-style:italic">
                                        No hay unidades registradas para esta dirección.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @empty
                <div class="alert alert-warning">
                    No se encontraron direcciones administrativas asignadas a este almacén.
                </div>
            @endforelse

        </div>
    </div>
</div>

<script>
$(document).ready(function () {});
</script>
