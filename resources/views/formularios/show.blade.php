@extends('adminlte::page')

@section('title', 'Visualizar Formulário')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Visualizar Formulário</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/home">Início</a></li>
                    <li class="breadcrumb-item active">Formulários</li>
                    <li class="breadcrumb-item active">Visualizar</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')


    <!-- <div class="row">
        @include('formularios.partials._variados')
    </div> -->

    <div class="row">
        @include('formularios.partials._variaveis')
    </div>

    <div class="row">
        @include('formularios.partials._graficos')
    </div>

    <!-- <div class="row">
        @include('formularios.partials._faixas')
    </div> -->

    <!-- <div class="row">
        @include('formularios.partials._teste')
    </div> -->

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fa-solid fa-eye" style="margin-right: 5px;"></i>
                    Detalhes do Formulário
                </h3>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label><strong>Nome:</strong></label>
                        <p>{{ $formulario->nome }}</p>
                    </div>

                    <div class="form-group col-md-6">
                        <label><strong>Tag:</strong></label>
                        <p>{{ $formulario->label }}</p>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-12">
                        <label><strong>Descrição:</strong></label>
                        <p>{!! $formulario->descricao !!}</p>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-4">
                        <label><strong>Calculo:</strong></label>
                        <p>{{ $formulario->tipoCalculo->nome ?? '---' }}</p>
                    </div>

                    <div class="form-group col-md-4">
                        <label><strong>Score inicial:</strong></label>
                        <p>{{ $formulario->score_ini }}</p>
                    </div>

                    <div class="form-group col-md-4">
                        <label><strong>Score final:</strong></label>
                        <p>{{ $formulario->score_fim }}</p>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-4">
                        <label><strong>Quantidade de Variaveis:</strong></label>
                        <p>{{$formulario->variaveisCount()}}</p>
                    </div>

                    <div class="form-group col-md-4">
                        <label><strong>Quantidade de Perguntas:</strong></label>
                        <p>{{$formulario->perguntaCount()}}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fa-solid fa-eye" style="margin-right: 5px;"></i>
                    Instruções {{ $formulario->label }}
                </h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <p>{!! $formulario->instrucoes !!}</p>
                </div>
            </div>
            <div class="card-footer">
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fa-solid fa-file-alt" style="margin-right: 5px;"></i>
                    Perguntas por Variavel
                </h3>
            </div>
            <div class="card-body mr-1">
                <table class="table datatable table-striped dtr-inline mr-1 ml-1">
                    <thead>
                        <tr>
                            <th style="width: 60%">Variável</th>
                            <th style="width: 40%">Perguntas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($formulario->perguntasPorVariavel() as $formularios)
                            <tr>
                                <td>{{ $formularios->nome }}</td>
                                <td>{{ $formularios->total_perguntas }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if(Auth::user()->sa === true)
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fa-solid fa-file-alt" style="margin-right: 5px;"></i>
                        Perguntas do Formulário
                    </h3>
                </div>

                <div class="card-body mr-1">
                    <table class="table datatable table-striped dtr-inline mr-1 ml-1">
                        <thead>
                            <tr>
                                <th style="width: 5%">Número</th>
                                <th style="width: 65%">Pergunta</th>
                                <th style="width: 30%">Variavel</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($formulario->perguntasComVariaveis() as $questionario)
                                <tr>
                                    <td>{{ $questionario->numero_da_pergunta }}</td>
                                    <td>{{ $questionario->pergunta }}</td>
                                    <td><label class="badge badge-dark"> {{ $questionario->tag }} </label> | {{ $questionario->nome }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif




@stop

@section('js')

@stop