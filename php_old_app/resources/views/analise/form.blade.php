@extends('adminlte::page')

@section('title', 'Gerar Análise')

@section('content_header')
    <h1>Escolher Tom da Análise</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('analise.gerar', $idUsuario) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="tone">Escolha o tom desejado:</label>
                    <select name="tone" id="tone" class="form-control" required>
                        <option value="formal">Formal</option>
                        <option value="técnico">Técnico</option>
                        <option value="empático">Empático</option>
                        <option value="motivacional">Motivacional</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Gerar Análise</button>
            </form>
        </div>
    </div>
@stop
