@extends('adminlte::page')

@section('title', 'Detalhes da Variável')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Detalhes da Variável</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/home">Início</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('variaveis.index') }}">Variáveis</a></li>
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
                {{ $variavel->nome}}
            </h3>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="form-group col-md-4">
                    <label for="formulario">Formulario:</label>
                    <p>{{ $variavel->formulario->nome}}</p>
                </div>

                <div class="form-group col-md-4">
                    <label for="nome">Nome:</label>
                    <p>{{ $variavel->nome}}</p>
                </div>

                <div class="form-group col-md-4">
                    <label for="tag">Tag:</label>
                    <p><label class="badge badge-info"> {{ strtoupper($variavel->tag) }} </label></p>
                </div>
            </div>

            <div class="row">
                <div class="form-group col-md-12">
                    <label for="descricao">Descrição:</label>
                    <p>{!! $variavel->descricao !!}</p>
                </div>
            </div>

            <div class="row">

                <div class="info-box mb-3 bg-success">
                    <span class="info-box-icon"><i class="fas fa-tag"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-number">BAIXA</span>
                        <span class="info-box-text">{{ $variavel->baixa }}</span>
                        <span class="info-box-number">Baixa de: ( <strong>0</strong> até <strong>{{ $variavel->B }}</strong> )</span>
                    </div>
                </div>

                <div class="info-box mb-3 bg-warning">
                    <span class="info-box-icon"><i class="fas fa-tag"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-number">MODERADA</span>
                        <span class="info-box-text">{{ $variavel->moderada }}</span>
                        <span class="info-box-number">Moderada de:( <strong>{{ $variavel->B + 1 }}</strong> até <strong>{{ $variavel->M }}</strong> )</span>
                    </div>
                </div>

                <div class="info-box mb-3 bg-danger">
                    <span class="info-box-icon"><i class="fas fa-tag"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-number">ALTA</span>
                        <span class="info-box-text">{{ $variavel->alta }}</span>
                        <span class="info-box-number">Alta acima de: ( <strong>{{ $variavel->A }}</strong> )</span>
                    </div>
                </div>

            </div>

        </div>

        <div class="card-footer">
            <a href="{{ route('variaveis.index') }}" class="btn btn-default" style="width: 150px;">
                 Voltar
            </a>
            </form>
        </div>
    </div>
@stop

