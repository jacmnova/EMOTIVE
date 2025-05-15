@extends('adminlte::page')

@section('title', 'Questionarios')

@section('content_header')

    @if(Session::has('msgSuccess'))
        <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <i class="fa-regular fa-bell" style="margin-right: 5px"></i> {!! Session::get('msgSuccess') !!}
        </div>
    @elseif(Session::has('msgError'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <i class="fa-solid fa-triangle-exclamation"></i> {!! Session::get('msgError') !!}
        </div>
    @endif

    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Início</a></li>
                    <li class="breadcrumb-item active">Questionarios</li>
                </ol>
            </div>
        </div>
    </div>

@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fa-solid fa-file-alt" style="margin-right: 5px;"></i>
                Lista de Questionarios
            </h3>
            <div class="card-tools d-flex align-items-center">

                <a href="#" class="btn btn-sm btn-tool d-sm-inline-block" title="Mais Informações">
                    <i class="fas fa-bars"></i>
                </a>

            </div>
        </div>

        @if ($questionarios->count() > 0)
            <div class="card-body mr-1">
                <table class="table datatable table-striped dtr-inline mr-1 ml-1">
                    <thead>
                        <tr>
                            <th style="width: 20%">Nome</th>
                            <th style="width: 20%">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($questionarios as $questionario)
                            <tr>
                                <td> {{ $questionario->nome }}</td>
                                <td>Responder</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="row" style="margin: 20px;">
                <div class="callout callout-warning">
                    <h5><i class="fa-solid fa-circle-info"></i> Nenhum Formulário foi encontrado.</h5>
                    <p>Cadastre um novo formulário usando o botão <strong>"Adicionar Formulário"</strong> no canto superior direito.</p>
                </div>
            </div>
        @endif
        
    </div>
@stop

@section('js')
    <script src="{{ asset('../js/utils.js') }}"></script>
@stop
