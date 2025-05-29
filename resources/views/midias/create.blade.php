@extends('adminlte::page')

@section('title', 'Cadastrar Mídia')

@section('content_header')
    <h1>Cadastrar Nova Mídia</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Nova Mídia</h3>
    </div>

    <form action="{{ route('midias.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card-body">

            <div class="form-group">
                <label for="titulo">Título</label>
                <input type="text" name="titulo" id="titulo" class="form-control @error('titulo') is-invalid @enderror" value="{{ old('titulo') }}" required>
                @error('titulo')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="tipo">Tipo</label>
                <select name="tipo" id="tipo" class="form-control @error('tipo') is-invalid @enderror" required onchange="toggleVideoInput()">
                    <option value="">Selecione o tipo</option>
                    <option value="video" {{ old('tipo') == 'video' ? 'selected' : '' }}>Vídeo (arquivo)</option>
                    <option value="url" {{ old('tipo') == 'url' ? 'selected' : '' }}>URL do Vídeo</option>
                </select>
                @error('tipo')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            {{-- Combo para escolher o formulário --}}
            <div class="form-group">
                <label for="formulario_id">Formulário</label>
                <select name="formulario_id" id="formulario_id" class="form-control @error('formulario_id') is-invalid @enderror" required>
                    <option value="">Selecione o formulário</option>
                    @foreach($formularios as $formulario)
                        <option value="{{ $formulario->id }}" {{ old('formulario_id') == $formulario->id ? 'selected' : '' }}>
                            {{ $formulario->nome }}
                        </option>
                    @endforeach
                </select>
                @error('formulario_id')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            {{-- Campo para URL do vídeo --}}
            <div class="form-group" id="video_url" style="display: none;">
                <label for="url">URL do Vídeo:</label>
                <input type="url" name="url" id="url" class="form-control @error('url') is-invalid @enderror" placeholder="Insira a URL do vídeo" value="{{ old('url') }}">
                @error('url')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            {{-- Campo para upload de vídeo --}}
            <div class="form-group" id="video_file" style="display: none;">
                <label for="arquivo">Anexar Vídeo:</label>
                <input type="file" name="arquivo" id="arquivo" class="form-control-file @error('arquivo') is-invalid @enderror" accept="video/*">
                @error('arquivo')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                @enderror
            </div>

        </div>

        <div class="card-footer d-flex justify-content-end">
            <a href="{{ route('midias.index') }}" class="btn btn-secondary mr-2">
                <i class="fa-solid fa-arrow-left"></i> Voltar
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-save"></i> Salvar
            </button>
        </div>
    </form>
</div>
@stop

@section('js')
<script>
    function toggleVideoInput() {
        const tipo = document.getElementById('tipo').value;
        document.getElementById('video_url').style.display = (tipo === 'url') ? 'block' : 'none';
        document.getElementById('video_file').style.display = (tipo === 'video') ? 'block' : 'none';
    }

    // Executa ao carregar a página para restaurar estado correto
    document.addEventListener('DOMContentLoaded', function () {
        toggleVideoInput();
    });
</script>
@stop
