@extends('adminlte::page')

@section('title', 'Editar Mídia')

@section('content_header')
    <h1>Editar Mídia</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Editar Mídia: {{ $midia->titulo }}</h3>
    </div>

    <form action="{{ route('midias.update', $midia->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="card-body">

            <div class="form-group">
                <label for="titulo">Título</label>
                <input type="text" name="titulo" id="titulo" class="form-control @error('titulo') is-invalid @enderror" value="{{ old('titulo', $midia->titulo) }}" required>
                @error('titulo')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="tipo">Tipo</label>
                <select name="tipo" id="tipo" class="form-control @error('tipo') is-invalid @enderror" required onchange="toggleVideoInput()">
                    <option value="">Selecione o tipo</option>
                    <option value="video" {{ old('tipo', $midia->tipo) == 'video' ? 'selected' : '' }}>Vídeo (arquivo)</option>
                    <option value="url" {{ old('tipo', $midia->tipo) == 'url' ? 'selected' : '' }}>URL do Vídeo</option>
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
                        <option value="{{ $formulario->id }}" {{ old('formulario_id', $midia->formulario_id) == $formulario->id ? 'selected' : '' }}>
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
                <input type="url" name="url" id="url" class="form-control @error('url') is-invalid @enderror" placeholder="Insira a URL do vídeo" value="{{ old('url', $midia->url) }}">
                @error('url')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            {{-- Campo para upload de vídeo --}}
            <div class="form-group" id="video_file" style="display: none;">
                <label for="arquivo">Substituir Vídeo:</label>
                <input type="file" name="arquivo" id="arquivo" class="form-control-file @error('arquivo') is-invalid @enderror" accept="video/*">
                @error('arquivo')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                @enderror

                @if($midia->arquivo)
                    <small class="form-text text-muted mt-2">
                        <i class="fa-solid fa-circle-info"></i>
                        Vídeo atual: 
                        <button type="button" class="btn btn-link p-0 align-baseline" onclick="abrirVideoAtualModal('{{ asset('storage/' . $midia->arquivo) }}')">
                            Ver vídeo
                        </button>
                    </small>
                @endif
            </div>

        </div>

        <div class="card-footer d-flex justify-content-end">
            <a href="{{ route('midias.index') }}" class="btn btn-secondary mr-2">
                <i class="fa-solid fa-arrow-left"></i> Voltar
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-save"></i> Salvar Alterações
            </button>
        </div>
    </form>
</div>


<!-- Modal para vídeo atual -->
<div class="modal fade" id="videoAtualModal" tabindex="-1" role="dialog" aria-labelledby="videoAtualModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="videoAtualModalLabel">Visualizar Vídeo Atual</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center" id="videoAtualModalBody">
                <!-- Conteúdo dinâmico -->
            </div>
        </div>
    </div>
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

    // Abre o modal e injeta o vídeo atual
    function abrirVideoAtualModal(url) {
        const conteudo = `<video src="${url}" controls autoplay style="width: 100%; height: auto;"></video>`;
        document.getElementById('videoAtualModalBody').innerHTML = conteudo;
        $('#videoAtualModal').modal('show');
    }
</script>
@stop

