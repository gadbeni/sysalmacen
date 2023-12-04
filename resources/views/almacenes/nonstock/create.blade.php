@extends('voyager::master')
@section('page_title', 'Solicitud de Inexistencia')
@section('page_header')
<div class="container-fluid">
    <div class="row">
        <h1 id="subtitle" class="page-title">
            <i class="fa fa-file-text"></i> Solicitud de Inexistencia
        </h1>
        <a href="{{ route('nonstock.index') }}" class="btn btn-warning btn-add-new">
            <i class="fa-solid fa-file"></i> <span>Volver</span>
        </a>
    </div>
</div>
@endsection
@section('content')
<form action="{{ route('nonstock.store') }}" method="POST">
    @csrf
    <div>
        <div class="container-fluid">
            <div class="panel panel-bordered">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-7">
                            <label for="customer_id">Almacen:</label>          
                            <div class="form-group">
                                <div class="form-line">
                                    <select name="sucursal_id" class="form-control select2" required>
                                        {{-- <option value="">-- Seleccione --</option> --}}
                                        @if ($sucursal)
                                            <option value="{{$sucursal->id}}">{{$sucursal->nombre}}</option>                                                    
                                        @endif
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
                                        @foreach ($subalmacen as $item)
                                            <option value="{{$item->id}}">{{$item->name}}</option>                                                    
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-7">
                            <label for="">Solicitante</label>
                            <p>
                                <small>
                                    {{auth()->user()->name}}
                                    @if ($funcionario)
                                    - {{$funcionario->cargo}}
                                    @endif     
                                </small>
                            </p>
                        </div>
                        <div class="col-lg-5">
                            <label for="">Fecha de solicitud</label>
                            <p><small>{{\Carbon\Carbon::now()->format('d/m/Y h:i:s')}}</small></p>
                        </div>
                    </div>
                    <div class="row">
                        @if (auth()->user()->direction)
                            <div class="col-lg-6">
                                <label for="">Dirección</label>
                                <p><small>{{auth()->user()->direction->nombre}}</small></p>
                            </div>
                        @endif
                        @if (auth()->user()->unit)
                            <div class="col-lg-6">
                                <label for="">Unidad</label>
                                <p><small>{{auth()->user()->unit->nombre}}</small></p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!-- form articles -->
        <div class="container-fluid">
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
                                            {{-- <th>Precio Unitario</th>
                                            <th>Precio referencial</th> --}}
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="article-container">
                                        <datalist id="articleList">
                                            {{-- <option value="Articulo 1"> --}}
                                        </datalist>
                                        <datalist id="presentationList">
        
                                        </datalist>
                                        <tr data-id="1" class="fila">
                                            <td>
                                                <span class="num-fila">1</span>
                                            </td>
                                            <td>
                                                <input id="article_name" list="articleList" type="text" name="article_name[]" class="article_input form-control" required>
                                            </td>
                                            <td>
                                                <input id="presentation" type="text" list="presentationList" name="unit_presentation[]" class="presentation-input form-control" required>
                                            </td>
                                            <td>
                                                <input type="number" name="quantity[]" id="quantity" class="form-control" step="1" required>
                                            </td>
                                            {{-- <td>
                                                <input type="number" name="price[]" id="price" class="form-control" min="0" step="0.01" required>
                                            </td>
                                            <td>
                                                <input type="number" name="price_ref[]" id="price_ref" class="form-control" min="0" step="0.01" required>
                                            </td> --}}
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm btn-delete-row" data-id="1"><i class="voyager-trash"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                
                            </div>
                            <button id="add-row" type="button" class="btn btn-success btn-sm btn-add-row"><i class="voyager-plus" style="font-size: 1.5rem"></i></button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success btn-block">Guardar</button>
                </div>
            </div>
        </div>
        <!-- end form articles -->
    </div>
</form>
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
                <input list="articleList" type="text" name="article_name[]" class="article_input form-control" required>
            </td>
            <td>
                <input type="text" list="presentationList" name="unit_presentation[]" class="presentation-input form-control" required>
            </td>
            <td>
                <input type="number" name="quantity[]" id="quantity" class="form-control" step="1" required>
            </td>
            <td>
            <button type="button" class="btn btn-danger btn-sm btn-delete-row" data-id="${num}"><i class="voyager-trash"></i></button>
            </td>
            
        `;
        // <td>
        //     <input type="number" name="price[]" id="price" class="form-control" min="0" step="0.01" required>
        // </td>
        // <td>
        //     <input type="number" name="price_ref[]" id="price_ref" class="form-control" min="0" step="0.01" required>
        // </td>
        

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


<script>
    // $(document).ready(function () {
    //     $('.article_input').each(function() {
    //         $(this).on('input', function(){
    //             var query = $(this).val();
    //             if(query.length > 3) {
    //                 $.ajax({
    //                     url:"{{ route('get-articlesnames-nonstock.list') }}",
    //                     type:"GET",
    //                     data:{'query':query},
    //                     success:function (data) {
    //                         var datalist = $('#articleList');
    //                         datalist.empty();
    //                         data.forEach(article => {
    //                             datalist.append(`<option data_id="${article.id}" value="${article.name}">`);
    //                         });
    //                     }
    //                 })
    //             }
    //         });
    //     })
        
    // });
    $(document).ready(function () {
        $(document).on('input', '.article_input', function(){
            var query = $(this).val();
            if(query.length > 3) {
                $.ajax({
                    url:"{{ route('get-articlesnames-nonstock.list') }}",
                    type:"GET",
                    data:{'query':query},
                    success:function (data) {
                        var datalist = $('#articleList');
                        datalist.empty();
                        data.forEach(article => {
                            datalist.append(`<option data_id="${article.id}" value="${article.name}">`);
                        });
                    }
                })
            }
        });
    });


    $(document).ready(function(){
        $(document).on('input','.presentation-input', function(){
            var query = $(this).val();
            if(query.length > 3) {
                $.ajax({
                    url:"{{ route('get-presentations-nonstock.list') }}",
                    type:"GET",
                    data:{'query':query},
                    success:function (data) {
                        var datalist = $('#presentationList');
                        datalist.empty();
                        data.forEach(presentation => {
                            datalist.append(`<option data_id="${presentation.id}" value="${presentation.name}">`);
                        });
                    }
                })
            }
        });
    })
    // $(document).ready(function(){
    //     $('#presentation').on('input', function(){
    //         var query = $(this).val();
    //         if(query.length > 3) {
    //             $.ajax({
    //                 url:"{{ route('get-presentations-nonstock.list') }}",
    //                 type:"GET",
    //                 data:{'query':query},
    //                 success:function (data) {
    //                     var datalist = $('#presentationList');
    //                     datalist.empty();
    //                     data.forEach(presentation => {
    //                         datalist.append(`<option data_id="${presentation.id}" value="${presentation.name}">`);
    //                     });
    //                 }
    //             })
    //         }
    //     });
    // })
</script>
@endsection