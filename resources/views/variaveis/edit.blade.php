@extends('adminlte::page')

@section('title', 'Editar Variável')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Editar Variável</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/home">Início</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('variaveis.index') }}">Variáveis</a></li>
                    <li class="breadcrumb-item active">Editar</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fa-solid fa-spell-check" style="margin-right: 5px;"></i>
                Editar Variável
            </h3>
        </div>

        <form action="{{ route('variaveis.update', $variavel->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="card-body">
                <div class="row">

                    <div class="form-group col-md-4">
                        <label for="formulario">Formulário:</label>
                        <input type="text" id="formulario" class="form-control" value="{{ $variavel->formulario->nome }}" readonly>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="tag">Tag:</label>
                        <input type="text" name="tag" id="tag" class="form-control" value="{{ old('tag', $variavel->tag ) }}" readonly >
                    </div>

                    <div class="form-group col-md-4">
                        <label for="nome">Nome:</label>
                        <input type="text" name="nome" id="nome" class="form-control" value="{{ old('nome', $variavel->nome ) }}" required>
                    </div>
                
                </div>

                <div class="row">
                    <div class="form-group col-md-12">
                        <label for="descricao">Descrição:</label>
                        <textarea name="descricao" id="descricao" class="form-control" rows="5" required>{{ old('descricao',$variavel->descricao) }}</textarea>
                    </div>
                </div>

                <div class="row">

                    <div class="form-group col-md-4">
                        <label for="B">Baixa de 0 até:</label>
                        <input type="text" name="B" id="B" class="form-control" value="{{ old('B', $variavel->B ) }}" required>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="M">Moderada até:</label>
                        <input type="text" name="M" id="M" class="form-control" value="{{ old('M', $variavel->M ) }}" required>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="A">Alta a partir de:</label>
                        <input type="text" name="A" id="A" class="form-control" value="{{ old('A', $variavel->A ) }}" required>
                    </div>

                </div>

                <div class="row">
                    <div class="form-group col-md-12">
                        <label for="baixa">Baixa:</label>
                        <textarea name="baixa" id="baixa" class="form-control" rows="5" required>{{ old('baixa', $variavel->baixa ) }}</textarea>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-12">
                        <label for="moderada">Moderada:</label>
                        <textarea name="moderada" id="moderada" class="form-control" rows="5" required>{{ old('moderada', $variavel->moderada ) }}</textarea>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-12">
                        <label for="alta">Alta:</label>
                        <textarea name="alta" id="alta" class="form-control" rows="5" required>{{ old('alta', $variavel->alta ) }}</textarea>
                    </div>
                </div>

            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-default" style="width: 150px;">Salvar</button>
                <a href="{{ route('variaveis.index') }}" class="btn btn-default" style="width: 150px;">Cancelar</a>
            </div>
        </form>
    </div>
@stop
