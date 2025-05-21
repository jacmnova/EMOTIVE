@extends('adminlte::page')

@section('title', 'Lista de Formulários')

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
                    <li class="breadcrumb-item active">Formulários</li>
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
                Lista de Formulários
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
                            <th style="width: 10%">Questões</th>
                            <th style="width: 10%">Habilitado em</th>
                            <th style="width: 10%">Status</th>
                            <th style="width: 20%">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($questionarios as $formulario)
                            <tr>
                                <td> <label class="badge badge-dark"> {{ $formulario->formulario->label }} </label> | {{ $formulario->formulario->nome }}</td>
                                
                                <td>{{ $formulario->formulario->perguntaCount() }}</td>
                                <td>
                                    {{ $formulario->created_at->translatedFormat('d \d\e F \d\e Y \à\s H:i') }}
                                </td>
                                <td>
                                    @if($formulario->status == 'novo')
                                        <label class="badge badge-primary"> {{  strtoupper($formulario->status) }} </label>
                                    @elseif($formulario->status == 'pendente')
                                        <label class="badge badge-warning"> {{  strtoupper($formulario->status) }} </label>
                                    @else
                                        <label class="badge badge-success"> {{  strtoupper($formulario->status) }} </label>
                                    @endif
                                </td>
                                <td>
                                    @if($formulario->status !== 'completo')
                                        <a href="{{ route('questionarios.editar', $formulario->formulario->id) }}"
                                        class="btn btn-sm btn-tool" title="Iniciar">
                                            <i class="fa-regular fa-circle-play" style="color: #008ca5"></i>
                                        </a>
                                    @else
                                        <a href="#"
                                        class="btn btn-sm btn-tool" title="Formulário finalizado!">
                                            <i class="fa-solid fa-check-double" style="color:rgb(46, 134, 11)"></i>
                                        </a> Finalizado em: {{ $formulario->updated_at->translatedFormat('d \d\e F \d\e Y \à\s H:i') }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="row" style="margin: 20px;">
                <div class="callout callout-warning">
                    <h5><i class="fa-solid fa-circle-info"></i> Nenhum Formulário foi encontrado.</h5>
                    <p>Entre em contato com seu gestor para habilitar um novo formulário</p>
                </div>
            </div>
        @endif
        
    </div>
@stop

@section('js')
    <script src="{{ asset('../js/utils.js') }}"></script>

@stop
