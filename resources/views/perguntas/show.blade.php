@extends('adminlte::page')

@section('title', 'Detalhes da Pergunta')

@section('css')
    {{-- <link rel="stylesheet" href="../css/utils.css"> --}}
@stop

@section('content_header')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Detalhes da Pergunta</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="/home">Início</a></li>
                <li class="breadcrumb-item"><a href="{{ route('perguntas.index') }}">Perguntas</a></li>
                <li class="breadcrumb-item active">Detalhes</li>
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
            Informações da Pergunta
        </h3>
    </div>

    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <strong>Formulário:</strong>
                <p>{{ $pergunta->formulario->nome ?? 'N/D' }}</p>
            </div>
            <div class="col-md-6">
                <strong>Categoria:</strong>
                <p>{{ $pergunta->categoria->nome ?? 'N/D' }}</p>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-2">
                <strong>Nº da Pergunta:</strong>
                <p>{{ $pergunta->numero_da_pergunta }}</p>
            </div>
            <div class="col-md-10">
                <strong>Pergunta:</strong>
                <p>{{ $pergunta->pergunta }}</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <strong>Variáveis:</strong>
                @if($pergunta->variaveis->count())
                    <ul>
                        @foreach($pergunta->variaveis as $variavel)
                            <li>{{ $variavel->nome }} ({{ $variavel->tag }})</li>
                        @endforeach
                    </ul>
                @else
                    <p>Nenhuma variável associada.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="card-footer">
        <a href="{{ route('perguntas.index') }}" class="btn btn-default" style="width: 150px;">Voltar</a>
    </div>
</div>
@stop
