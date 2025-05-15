@extends('adminlte::page')

@section('title', 'Questionário')

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
                    <li class="breadcrumb-item active">Relatório</li>
                </ol>
            </div>
        </div>
    </div>

@stop

@section('content')


<div class="row">
    <div class="col-md-6">

        <div class="card">
            <div class="card-header border-0">
                <h2 class="card-title"><label class="badge badge-dark"> {{ $formulario->label }} </label> | {{ $formulario->nome }}</h2>
                <div class="card-tools">
                    <a href="#" class="btn btn-tool btn-sm">
                        <i class="fas fa-download"></i>
                    </a>
                    <a href="#" class="btn btn-tool btn-sm">
                        <i class="fas fa-bars"></i>
                    </a>
                    <a href="#" class="btn btn-sm btn-tool">
                        <i class="fa-solid fa-circle-info"></i>
                    </a>
                </div>
            </div>
            <div class="card-body" style="box-shadow: none;">
                <div class="card mb-2 pl-4 pr-4 border-0" style="box-shadow: none;">
                    <h5>Instruções</h5>
                    <p>{!! $formulario->instrucoes !!}</p>
                </div>
                <div class="card mb-2 p-2" style="box-shadow: none;">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Pergunta</th>
                                <th>Resposta</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($formulario->perguntas as $pergunta)
                                @php
                                    $resposta = $respostasUsuario->get($pergunta->id);
                                @endphp
                                <tr>
                                    <td>
                                        {{ $pergunta->numero_da_pergunta }} - {{ $pergunta->pergunta }}
                                    </td>
                                    <td>
                                        {{ $resposta->valor_resposta }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-right">
                    teste
                </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header border-0">
                <h2 class="card-title"><label class="badge badge-dark"> {{ $formulario->label }} </label> | {{ $formulario->nome }}</h2>
                <div class="card-tools">
                    <a href="#" class="btn btn-tool btn-sm">
                        <i class="fas fa-download"></i>
                    </a>
                    <a href="#" class="btn btn-tool btn-sm">
                        <i class="fas fa-bars"></i>
                    </a>
                    <a href="#" class="btn btn-sm btn-tool">
                        <i class="fa-solid fa-circle-info"></i>
                    </a>
                </div>
            </div>
            <div class="card-body" style="box-shadow: none;">
            </div>
        </div>
    </div>

</div>


@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}

@stop

@section('js')
    <script src="{{ asset('../js/utils.js') }}"></script>
@stop