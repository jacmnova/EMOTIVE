@extends('adminlte::page')

@section('title', 'Importar Usuários')

@section('content_header')
    <h1>Importar Usuários</h1>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('gestor.importar') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="arquivo">Selecione o arquivo CSV:</label>
            <input type="file" name="arquivo" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary mt-2">Importar</button>
    </form>
@stop