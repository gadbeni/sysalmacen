@extends('voyager::master')
@section('page_title', 'Solicitud de Inexistencia')
@section('page_header')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <h1 id="subtitle" class="page-title">
                <i class="fa fa-cart-arrow-down"></i> Solicitud de Inexistencia
            </h1>
        </div>
        <div class="col-md-8 text-right" style="padding-top: 10px">
            <a href="{{ route('nonstock.index') }}" class="btn btn-warning btn-add-new">
                <i class="fa fa-arrow-circle-left"></i> <span>Volver</span>
            </a>
        </div>
    </div>
</div>
@endsection
@section('content')
<form id="form-registrar-pedido" action="{{ route('nonstock.store') }}" method="POST">
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
                            <label for="" class="panel-title">Solicitante</label>
                            <p class="panel-body" style="padding-top:0;">
                                <small style="text-transform: uppercase">
                                    {{auth()->user()->name}}
                                    @if ($funcionario)
                                    - {{$funcionario->cargo}}
                                    @endif     
                                </small>
                            </p>
                            <hr style="margin:0;">
                        </div>
                        <div class="col-lg-5">
                            <label for="" class="panel-title">Fecha de solicitud</label>
                            <p class="panel-body" style="padding-top:0;">
                                <small>{{\Carbon\Carbon::now()->format('d/m/Y h:i:s')}}</small>
                            </p>
                            <hr style="margin:0;">
                        </div>
                    </div>
                    <div class="row">
                        @if (auth()->user()->direction)
                            <div class="col-lg-6">
                                <label for="" class="panel-title">Dirección</label>
                                <p class="panel-body" style="padding-top:0;">
                                    <small>{{auth()->user()->direction->nombre}}</small>
                                </p>
                                <hr style="margin:0;">
                            </div>
                        @endif
                        @if (auth()->user()->unit)
                            <div class="col-lg-6">
                                <label for="" class="panel-title">Unidad</label>
                                <p class="panel-body" style="padding-top:0;">
                                    <small>{{auth()->user()->unit->nombre}}</small>
                                </p>
                                <hr style="margin:0;">
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!-- form articles -->
        <div class="container-fluid">
            
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-bordered">
                        <div class="panel-body">
                            <h1 class="page-title">
                                <i class="fa fa-archive"></i>
                                Articulos registrados en el sistema
                            </h1>
                            <div class="alert alert-info">
                                <strong>Información: </strong>
                                <p>En esta sección puede buscar el articulo a solicitar, Se listaran todos los articulos registrados en almacen que no esten disponibles en la "solicitud de pedidos".</p>
                                <p>En caso de no encontrar lo que busca aqui, baje a la siguiente sección donde podra ingresar el producto manualmente.</p>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="product_id">Buscar producto</label>
                                    <select class="form-control" id="select_producto"></select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table id="dataTable" class="tables table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th style="width: 30px">N&deg;</th>
                                                <th style="text-align: center">Detalle</th>  
                                                <th style="text-align: center; width: 120px">Cantidad</th>  
                                                <th style="text-align: center; width: 80px"></th>  
                                            </tr>
                                        </thead>
                                        <tbody id="table-body">
                                            <tr id="tr-empty">
                                                <td colspan="6" style="height: 290px">
                                                    <h4 class="text-center text-muted" style="margin-top: 50px">
                                                        <i class="voyager-basket" style="font-size: 50px"></i> <br><br>
                                                        Lista de pedido vacía
                                                    </h4>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>       
                            <div class="form-group col-md-12 text-center">
                                {{-- Tiene que tener una gestion activa y tenes una unidad agregada como funcionario --}}
                                {{-- @if ($gestion && $user->unidadAdministrativa_id)
                                    <button type="submit" id="btn-register" class="btn btn-success btn-block">Registrar Pedido <i class="voyager-basket"></i></button>
                                @endif --}}
                                {{-- <button id="btn-volver" class="btn btn-block" href="{{ route('outbox.index') }}" >Volver a la lista</button> --}}
                            </div>                        
                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-bordered">
                <div class="panel-body">
                    <div class="row">
                        <h1 class="page-title">
                            <i class="fa fa-archive"></i>
                            Registrar Manualmente
                        </h1>
                        <div class="col-lg-12">
                            <div class="alert alert-info">
                                <strong>Información: </strong>
                                <p>En esta sección puede introducir el articulo manualmente. Click en el boton "+" para añadir una nueva fila.</p>
                            </div>
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
                                        {{-- <tr data-id="1" class="fila">
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

                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm btn-delete-row" data-id="1"><i class="voyager-trash"></i></button>
                                            </td>
                                        </tr> --}}
                                            {{-- <td>
                                                <input type="number" name="price[]" id="price" class="form-control" min="0" step="0.01" required>
                                            </td>
                                            <td>
                                                <input type="number" name="price_ref[]" id="price_ref" class="form-control" min="0" step="0.01" required>
                                            </td> --}}
                                    </tbody>
                                </table>
                                
                            </div>
                            <button id="add-row" type="button" class="btn btn-success btn-sm btn-add-row"><i class="voyager-plus" style="font-size: 1.5rem"></i></button>
                        </div>
                    </div>
                    <button id="submit-button" type="submit" class="btn btn-success btn-block">
                        Registrar Inexistencia
                        <i class="fa fa-cart-arrow-down"></i>
                    </button>
                </div>
            </div>
        </div>
        <!-- end form articles -->
    </div>
</form>
@endsection


@section('javascript')
{{-- scripts copy --}}
<script>

    $(document).ready(function(){
        
        $('#form-registrar-pedido').submit(function(e){

            $("#submit-button").text('Registrando...');
            $("#submit-button").attr('disabled','disabled');


            // $('#btn-volver').attr('disabled','disabled');
        });
        

    })
    $(document).ready(function(){
        

        var productSelected;
        subid=null;
        $('#subSucursal_id').on('change', function()
        {
            subid = $("#subSucursal_id").val();
            $('#select_producto').val("").trigger("change");
            $(`.classRemove`).remove();
            $('#tr-empty').fadeIn('fast');
        });
        // ruta = `{{ url('admin/outbox/article/stock/ajax/${subid}') }}`
        // alert(subid)
        // alert(ruta)


        $('#select_producto').select2({
            placeholder: '<i class="fa fa-search"></i> Buscar...',
            escapeMarkup : function(markup) {
                return markup;
            },
            language: {
                inputTooShort: function (data) {
                    return `Por favor ingrese ${data.minimum - data.input.length} o más caracteres`;
                },
                noResults: function () {
                    return `<i class="far fa-frown"></i> No hay resultados encontrados`;
                }
            },
            quietMillis: 250,
            minimumInputLength: 2,
            ajax: {
                // http://127.0.0.1:8000/admin/outbox/article/stock/ajax?search=papel&externo=25
                url: `{{ url('admin/nonstock/article/nostock/ajax') }}`,     
                data: function (params) {
                    return {
                                search: params.term, // search term
                                externo: subid,
                            };
                        },   
                processResults: function (data) {                    
                    let results = [];
                    data.map(data =>{
                        results.push({
                            ...data,
                            disabled: false
                        });
                    });
                    return {
                        results
                    };
                },
                cache: true
            },
            templateResult: formatResultCustomers,
            templateSelection: (opt) => {
                productSelected = opt;

                
                return opt.id?opt.nombre:'<i class="fa fa-search"></i> Buscar... ';
            }
        }).change(function(){
            // alert(2)
            // alert($('#select_producto option:selected').val())
            if($('#select_producto option:selected').val()){
                let product = productSelected;
                // toastr.info('EL detalle ya está agregado', 'Información');

                // alert(product.article_id);
                if($('.tables').find(`#tr-item-${product.id}`).val() === undefined){
                // alert(product.name);

                    $('#table-body').append(`
                        <tr class="tr-item classRemove" id="tr-item-${product.id}">
                            <td class="td-item"></td>
                            <td>
                                <b class="label-description" id="description-${product.id}"><small>${product.nombre}</small><br>
                                <b class="label-description"><small>${product.presentacion}</small>
                                <input type="hidden" name="article_id[]" value="${product.id}" />
                            </td>
                            <td>
                                <input type="number" name="cantidad[]" min="0.1" step="0.01" id="select-cant-${product.id}" style="text-align: right" class="form-control text" required>
                            </td>
                            <td class="text-right"><button type="button" onclick="removeTr(${product.id})" class="btn btn-link"><i class="voyager-trash text-danger"></i></button></td>
                        </tr>
                    `);
                    toastr.success('Producto agregado..', 'Información');

                }else{
                    // alert(1)
                    toastr.warning('El detalle ya está agregado..', 'Información');
                }
                setNumber();
                // getSubtotal(product.article_id);
            }
        });

        
        

    })

    function formatResultCustomers(option){
    // Si está cargando mostrar texto de carga
    // alert(option.article.name)
        if (option.loading) {
            return '<span class="text-center"><i class="fas fa-spinner fa-spin"></i> Buscando...</span>';
        }
        let image = "{{ asset('images/default.jpg') }}";
        if(option.image){
            image = "{{ asset('storage') }}/"+option.image.replace('.', '-cropped.');
            // alert(image)
        }
        
        // Mostrar las opciones encontradas
        return $(`  <div style="display: flex">
                        <div style="margin: 0px 10px">
                            <img src="${image}" width="50px" />
                        </div>
                        <div>
                            <b style="font-size: 16px"> ${option.nombre} </b> <br>
                            <small style="font-size: 16px">${option.presentacion} </small>
                         
                        </div>
                    </div>`);
    }



    function setNumber(){
        var length = 0;
        $(".td-item").each(function(index) {
            $(this).text(index +1);
            length++;
        });

        if(length > 0){
            $('#tr-empty').css('display', 'none');
        }else{
            $('#tr-empty').fadeIn('fast');
        }
    }

    function removeTr(id){
        $(`#tr-item-${id}`).remove();
        $('#select_producto').val("").trigger("change");
        setNumber();
        // getTotal();
    }







</script>

{{-- my scripts --}}
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