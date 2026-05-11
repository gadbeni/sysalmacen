@extends('voyager::master')

@section('page_title', 'Reporte de Usuarios por Dirección Administrativa')
@if(auth()->user()->hasRole('admin'))

@section('page_header')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body" style="padding: 0px">
                        <div class="col-md-7" style="padding: 0px">
                            <h1 class="page-title">
                                <i class="voyager-people"></i> Reporte de Usuarios por Dirección Administrativa
                            </h1>
                        </div>
                        <div class="col-md-5" style="margin-top: 30px">
                            <form name="form_search" id="form-search"
                                  action="{{ route('almacen-usuarios-direccion.list') }}" method="POST">
                                @csrf
                                <input type="hidden" name="print">

                                <div class="form-group">
                                    <div class="form-line">
                                        <select name="sucursal_id" id="sucursal_id"
                                                class="form-control select2" required>
                                            <option value="" disabled selected>-- Seleccione un almacén --</option>
                                            @foreach ($sucursal as $item)
                                                <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                                            @endforeach
                                        </select>
                                        <small>Almacén / Sucursal</small>
                                    </div>
                                </div>

                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary" style="padding: 5px 10px">
                                        <i class="voyager-settings"></i> Generar
                                    </button>
                                </div>
                                <br>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')
        <div class="row">
            <div id="div-results" style="min-height: 100px"></div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .loader {
            width: 100px; height: 100px;
            border-radius: 100%;
            position: relative;
            margin: 0 auto;
        }
        #loader-3:before, #loader-3:after {
            content: "";
            width: 20px; height: 20px;
            position: absolute;
            top: 0;
            left: calc(50% - 10px);
            background-color: #3498db;
            animation: squaremove 1s ease-in-out infinite;
        }
        #loader-3:after { bottom: 0; animation-delay: 0.5s; }
        @keyframes squaremove {
            0%,100% { transform: translate(0,0) rotate(0); }
            25%      { transform: translate(40px,40px) rotate(45deg); }
            50%      { transform: translate(0,80px) rotate(0deg); }
            75%      { transform: translate(-40px,40px) rotate(45deg); }
        }
    </style>
@stop

@section('javascript')
    <script src="{{ url('js/main.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('#form-search').on('submit', function (e) {
                e.preventDefault();
                $('#div-results').empty();
                var loader = '<div class="col-md-12 bg"><div class="loader" id="loader-3"></div></div>';
                $('#div-results').html(loader);
                $.post($('#form-search').attr('action'), $('#form-search').serialize(), function (res) {
                    $('#div-results').html(res);
                })
                .fail(function () {
                    toastr.error('Ocurrió un error!', 'Oops!');
                })
                .always(function () {
                    $('html, body').animate({ scrollTop: $("#div-results").offset().top - 70 }, 500);
                });
            });
        });

        function report_print() {
            $('#form-search').attr('target', '_blank');
            $('#form-search input[name="print"]').val(1);
            window.form_search.submit();
            $('#form-search').removeAttr('target');
            $('#form-search input[name="print"]').val('');
        }
    </script>
@stop

@else
    @section('content')
        @include('errors.403')
    @stop
@endif
