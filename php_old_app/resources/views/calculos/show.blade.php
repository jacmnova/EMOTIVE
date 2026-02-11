@extends('adminlte::page')

@section('title', 'Detalhes do Cálculo')

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
            <i class="fa-solid fa-triangle-exclamation"></i> {{ Session::get('msgError') }}
        </div>
    @endif

    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Detalhes do Cálculo</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/home">Início</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('calculos.index') }}">Cálculos</a></li>
                    <li class="breadcrumb-item active">Visualizar</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fa-solid fa-circle-info" style="margin-right: 5px;"></i>
                Informações do Cálculo
            </h3>
        </div>

        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Nome</dt>
                <dd class="col-sm-9">{{ $calculo->nome }}</dd>

                <dt class="col-sm-3">Descrição</dt>
                <dd class="col-sm-9">{{ $calculo->descricao }}</dd>

                <dt class="col-sm-3">Operador</dt>
                <dd class="col-sm-9">{{ $calculo->operador }}</dd>

                <dt class="col-sm-3">Fórmula</dt>
                <dd class="col-sm-9"><pre class="bg-light p-2">{{ $calculo->formula }}</pre></dd>

                <dt class="col-sm-3">Status</dt>
                <dd class="col-sm-9">
                    @if ($calculo->ativo)
                        <span class="badge badge-info">Ativo</span>
                    @else
                        <span class="badge badge-secondary">Inativo</span>
                    @endif
                </dd>

                <dt class="col-sm-3">Criado em</dt>
                <dd class="col-sm-9">{{ $calculo->created_at ? $calculo->created_at->format('d/m/Y H:i') : 'N/A' }}</dd>

                <dt class="col-sm-3">Última atualização</dt>
                <dd class="col-sm-9">{{ $calculo->updated_at ? $calculo->updated_at->format('d/m/Y H:i') : 'N/A' }}</dd>
            </dl>
        </div>

        <div class="card-footer text-right">
            <a href="{{ route('calculos.index') }}" class="btn btn-secondary" style="width: 150px;">Voltar</a>
        </div>
    </div>

@stop