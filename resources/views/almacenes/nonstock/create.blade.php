@extends('voyager::master')
@section('page_title', 'Solicitud de Inexistencia')
@section('page_header')
<div class="container-fluid">
    <div class="row">
        <h1 id="subtitle" class="page-title">
            <i class="voyager-basket"></i> Solicitud de Inexistencia
        </h1>
        <a href="{{ route('nonstock.index') }}" class="btn btn-warning btn-add-new">
            <i class="fa-solid fa-file"></i> <span>Volver</span>
        </a>
    </div>
</div>
@endsection
@section('content')
<div>
    <div class="container-fluid">
        <div class="panel panel-bordered">
            <div class="panel-body">
                <div class="row">

                </div>
                <div class="row">
                    <div class="col-lg-7">
                        <label for="customer_id">Almacen:</label>          
                        <div class="form-group">
                            <div class="form-line">
                                <select name="sucursal_id" class="form-control select2" required>
                                    <option value="">-- Seleccione --</option>
                                    {{-- @if ($sucursal)
                                        <option value="{{$sucursal->id}}">{{$sucursal->nombre}}</option>                                                    
                                    @endif --}}
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <label for="customer_id">Tipo Almacen:</label>          
                        <div class="form-group">
                            <div class="form-line">
                                <select name="subSucursal_id" id="subSucursal_id" class="form-control select2" required>
                                    <option value="" selected disabled>--Seleccione una opción--</option>
                                    {{-- @foreach ($sub as $item)
                                        <option value="{{$item->id}}">{{$item->name}}</option>                                                    
                                    @endforeach --}}
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row"></div>
            </div>
        </div>
    </div>
    <!-- form articles -->
    <div class="panel panel-bordered">
        <div class="panel-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="table-responsive">
                        <table id="tblArticles" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>N#</th>
                                    <th>Articulo (Descripción)</th>
                                    <th>Unidad</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unitario</th>
                                    <th>Precio referencial</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="article-container">
                                <tr data-id="1" class="fila">
                                    <td>
                                        <span class="num-fila">1</span>
                                    </td>
                                    <td>
                                        <select name="article_id[]" id="article_id" class="form-control select2" required>
                                            <option value="" selected disabled>--Seleccione una opción--</option>
                                            {{-- @foreach ($articles as $item)
                                                <option value="{{$item->id}}">{{$item->name}}</option>                                                    
                                            @endforeach --}}
                                        </select>
                                    </td>
                                    <td>
                                        <select name="unit_id[]" id="unit_id" class="form-control select2" required>
                                            <option value="" selected disabled>--Seleccione una opción--</option>
                                            {{-- @foreach ($units as $item)
                                                <option value="{{$item->id}}">{{$item->name}}</option>                                                    
                                            @endforeach --}}
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="quantity[]" id="quantity" class="form-control" step="1" required>
                                    </td>
                                    <td>
                                        <input type="number" name="price[]" id="price" class="form-control" min="0" step="0.01" required>
                                    </td>
                                    <td>
                                        <input type="number" name="price_ref[]" id="price_ref" class="form-control" min="0" step="0.01" required>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm btn-delete-row" data-id="1"><i class="voyager-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <button id="add-row" type="button" class="btn btn-success btn-sm btn-add-row"><i class="voyager-plus"></i>Más</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('javascript')
<Script>
    const btn_add_row = document.getElementById('add-row');
    let btn_delete_row = document.querySelectorAll('.btn-delete-row');

    //events
    btn_add_row.addEventListener('click', addRow);
    btn_delete_row.forEach(btn => {
        btn.addEventListener('click', deleteRow);
    });

    //funtions
    function addRow() {
        const table = document.getElementById('tblArticles');
        const tbody = document.getElementById('article-container');
        const numfila = document.querySelectorAll('.num-fila');
        const row = document.createElement('tr');
        const num = numfila.length + 1;
        row.dataset.id = num;
        row.classList.add('fila');
        row.innerHTML = `
            <td>
                <span class="num-fila">${num}</span>
            </td>
            <td>
                <select name="article_id[]" id="article_id" class="form-control select2" required>
                    <option value="" selected disabled>--Seleccione una opción--</option>
                    {{-- @foreach ($articles as $item)
                        <option value="{{$item->id}}">{{$item->name}}</option>                                                    
                    @endforeach --}}
                </select>
            </td>
            <td>
                <select name="unit_id[]" id="unit_id" class="form-control select2" required>
                    <option value="" selected disabled>--Seleccione una opción--</option>
                    {{-- @foreach ($units as $item)
                        <option value="{{$item->id}}">{{$item->name}}</option>                                                    
                    @endforeach --}}
                </select>
            </td>
            <td>
                <input type="number" name="quantity[]" id="quantity" class="form-control" step="1" required>
            </td>
            <td>
                <input type="number" name="price[]" id="price" class="form-control" min="0" step="0.01" required>
            </td>
            <td>
                <input type="number" name="price_ref[]" id="price_ref" class="form-control" min="0" step="0.01" required>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm btn-delete-row" data-id="${num}"><i class="voyager-trash"></i></button>
            </td>
        `;
        tbody.appendChild(row);
        btn_delete_row = document.querySelectorAll('.btn-delete-row');
        btn_delete_row.forEach(btn => {
            btn.addEventListener('click', deleteRow);
        });
    }
    function deleteRow(e) {
        console.log(e.target.tagName)
        let btn = null;
        if(e.target.tagName === 'I') {
            btn = e.target.parentElement;
        }
        else if(e.target.tagName === 'BUTTON') {
            btn = e.target;
        }

        const id = btn.dataset.id;
        const fila = document.querySelector(`tr[data-id="${id}"]`);

        fila.remove();
        //actuali
        const filas = document.querySelectorAll('.fila');
        filas.forEach((fila, index) => {
            fila.querySelector('.num-fila').textContent = index + 1;
            fila.querySelector('.btn-delete-row').dataset.id = index + 1;
            fila.dataset.id = index + 1;
        });
    }
</Script>
@endsection