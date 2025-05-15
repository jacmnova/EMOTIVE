@extends('adminlte::page')

@section('title', 'Editar Cálculo')

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
                <h1>Editar Cálculo</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/home">Início</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('calculos.index') }}">Cálculos</a></li>
                    <li class="breadcrumb-item active">Editar</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <form action="{{ route('calculos.update', $calculo->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fa-solid fa-pen" style="margin-right: 5px;"></i>
                    Atualizar Cálculo
                </h3>
            </div>

            <div class="card-body">

                <div class="form-group">
                    <label for="nome">Nome <span class="text-danger">*</span></label>
                    <input type="text" name="nome" class="form-control" maxlength="50" required value="{{ old('nome', $calculo->nome) }}">
                    @error('nome')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="descricao">Descrição <span class="text-danger">*</span></label>
                    <input type="text" name="descricao" class="form-control" maxlength="200" required value="{{ old('descricao', $calculo->descricao) }}">
                    @error('descricao')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="operador">Operador <span class="text-danger">*</span></label>
                    <input type="text" name="operador" class="form-control" maxlength="20" required value="{{ old('operador', $calculo->operador) }}">
                    @error('operador')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="formula">Fórmula <span class="text-danger">*</span></label>
                    <textarea name="formula" class="form-control" rows="4" required>{{ old('formula', $calculo->formula) }}</textarea>
                    @error('formula')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

            </div>

            <div class="card-footer text-right">
                <a href="{{ route('calculos.index') }}" class="btn btn-secondary" style="width: 150px;">Cancelar</a>
                <button type="submit" class="btn btn-primary" style="width: 150px;">Salvar</button>
            </div>
        </div>
    </form>
@stop