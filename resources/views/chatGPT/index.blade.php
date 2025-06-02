@extends('adminlte::page')

@section('title', 'ChatGPT')

@section('content_header')
    <h1>Assistente IA</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <!-- Formulário de Pergunta -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Faça sua pergunta</h3>
                </div>
                <form action="{{ route('chat.ask') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="question">Pergunta</label>
                            <input type="text" name="question" id="question" class="form-control" placeholder="Digite sua pergunta..." value="{{ old('question') }}" required>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Enviar</button>
                    </div>
                </form>
            </div>

            <!-- Resposta do ChatGPT -->
            @if(isset($answer))
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">Resposta da IA</h3>
                    </div>
                    <div class="card-body">
                        <p><strong>Pergunta:</strong> {{ $question }}</p>
                        <hr>
                        <textarea readonly class="form-control" rows="10">{{ $answer }}</textarea>
                    </div>
                </div>
            @endif
        </div>
    </div>
@stop

@section('css')
    {{-- Estilos adicionais --}}
@stop

@section('js')
    <script>
        console.log("ChatGPT carregado");
    </script>
@stop
