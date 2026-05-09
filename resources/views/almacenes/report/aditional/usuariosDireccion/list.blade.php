<div class="col-md-12 text-right" style="margin-bottom: 8px">
    <button type="button" onclick="report_print()" class="btn btn-dark">
        <i class="glyphicon glyphicon-print"></i> Imprimir
    </button>
</div>

<div class="col-md-12">
    <div class="panel panel-bordered">
        <div class="panel-body">

            {{-- Encabezado del almacén --}}
            <h4 style="margin-bottom: 15px">
                <i class="voyager-shop"></i>
                <strong>Almacén:</strong> {{ $sucursal->nombre }}
            </h4>

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
                                <th>USUARIOS ASIGNADOS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $numUnidad = 1; @endphp
                            @forelse ($dirItem['unidades'] as $uItem)
                                @php
                                    $unidad   = $uItem['unidad'];
                                    $usuarios = $uItem['usuarios'];
                                @endphp
                                <tr>
                                    <td style="text-align:center; vertical-align:middle">{{ $numUnidad }}</td>
                                    <td style="vertical-align:middle">{{ $unidad->nombre }}</td>
                                    <td style="text-align:center; vertical-align:middle">
                                        <span class="badge" style="background:#2d6a9f">
                                            {{ $unidad->sigla ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        @forelse ($usuarios as $usr)
                                            <div style="padding: 2px 0; border-bottom: 1px dotted #ddd">
                                                <i class="voyager-person" style="color:#2d6a9f"></i>
                                                <strong>{{ $usr->ci ?? '—' }}</strong>
                                                — {{ $usr->first_name ?? '' }} {{ $usr->last_name ?? $usr->username }}
                                                @if($usr->rol)
                                                    <span class="label label-default" style="font-size:10px; margin-left:4px">
                                                        {{ $usr->rol }}
                                                    </span>
                                                @endif
                                            </div>
                                        @empty
                                            <span class="text-muted" style="font-style:italic">
                                                <i class="voyager-warning"></i> Sin usuarios asignados
                                            </span>
                                        @endforelse
                                    </td>
                                </tr>
                                @php $numUnidad++; @endphp
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted" style="font-style:italic">
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
