@extends('adminlte::page')

@section('title', 'Mídias')

@section('content_header')
    @if(Session::has('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Fechar">
                <span aria-hidden="true">&times;</span>
            </button>
            <i class="fa-regular fa-bell mr-1"></i> {!! Session::get('success') !!}
        </div>
    @elseif(Session::has('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Fechar">
                <span aria-hidden="true">&times;</span>
            </button>
            <i class="fa-solid fa-triangle-exclamation"></i> {!! Session::get('error') !!}
        </div>
    @endif

    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Mídias</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Início</a></li>
                    <li class="breadcrumb-item active">Mídias</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Lista de Mídias</h3>
            <div class="card-tools d-flex align-items-center">
                <a href="{{ route('midias.create') }}" class="btn btn-default btn-sm mr-2">
                    <i class="fa-solid fa-plus mr-1"></i>
                    Adicionar Mídia
                </a>
            </div>
        </div>

        @if($midias->count() > 0)
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped datatable">
                        <thead>
                            <tr>
                                <th style="width: 30%">Apresentação</th>
                                <th style="width: 10%">Tipo</th>
                                <th style="width: 30%">Formulário</th>
                                <th style="width: 30%">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($midias as $midia)
                                <tr>
                                    <td>{{ $midia->titulo }}</td>
                                    <td>{{ $midia->tipo == 'url' ? 'URL' : 'VÍDEO' }}</td>
                                    <td>{{ $midia->formulario->label }} | {{ $midia->formulario->nome }}</td>
                                    <td>
                                        @if($midia->tipo == 'url')
                                            <button class="btn btn-sm btn-tool" title="Abrir URL" onclick="abrirMidiaModal('url', '{{ $midia->url }}')">
                                                <i class="fa-solid fa-link text-primary"></i>
                                            </button>
                                        @elseif($midia->tipo == 'video' && $midia->arquivo)
                                            <button class="btn btn-sm btn-tool" title="Abrir Vídeo" onclick="abrirMidiaModal('video', '{{ asset('storage/' . $midia->arquivo) }}')">
                                                <i class="fa-solid fa-video text-success"></i>
                                            </button>
                                        @endif

                                        <a href="{{ route('midias.edit', $midia->id) }}" class="btn btn-sm btn-tool" title="Editar">
                                            <i class="fa-solid fa-pencil text-info"></i>
                                        </a>
                                        <form action="{{ route('midias.destroy', $midia->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-tool" title="Remover" onclick="confirmDeletar({{ $midia->id }})">
                                                <i class="fa-regular fa-trash-can text-danger"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="card-body">
                <div class="callout callout-warning">
                    <h5><i class="fa-solid fa-circle-info"></i> Nenhuma Mídia foi cadastrada ainda.</h5>
                    <p>Cadastre sua mídia no botão <strong>"Adicionar Mídia"</strong> no canto superior direito.</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Modal para exibir mídia -->
    <div class="modal fade" id="midiaModal" tabindex="-1" role="dialog" aria-labelledby="midiaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
        <div class="modal-header bg-primary">
            <h5 class="modal-title" id="midiaModalLabel">Visualizar Mídia</h5>
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fechar">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body text-center" id="midiaModalBody">
            <!-- Conteúdo dinâmico -->
        </div>
        </div>
    </div>
    </div>
@stop

@section('js')
    <script src="{{ asset('../js/utils.js') }}"></script>
    <script src="https://www.youtube.com/iframe_api"></script>

    <script>
        let player;

        function abrirMidiaModal(tipo, url) {
            let conteudo = '';
            let videoId = '';

            if (tipo === 'url') {
                videoId = extrairVideoId(url);
                if (videoId) {
                    conteudo = `<div id="youtubePlayer" style="width: 100%; height: 500px;"></div>`;
                } else {
                    conteudo = `<p class="text-danger">URL inválida ou vídeo não suportado para exibição em modal.</p>`;
                }
            } else if (tipo === 'video') {
                conteudo = `<video id="videoPlayer" src="${url}" controls autoplay style="width: 100%; height: auto;"></video>`;
            }

            document.getElementById('midiaModalBody').innerHTML = conteudo;
            $('#midiaModal').modal('show');

            if (tipo === 'url' && videoId) {
                if (typeof YT !== 'undefined' && YT.Player) {
                    criarYouTubePlayer(videoId);
                }
            }
        }

        function criarYouTubePlayer(videoId) {
            player = new YT.Player('youtubePlayer', {
                videoId: videoId,
                events: {
                    'onReady': function(event) {
                        event.target.playVideo();
                    }
                },
                playerVars: {
                    'autoplay': 1,
                    'controls': 1
                }
            });
        }

        function extrairVideoId(url) {
            const regExp = /(?:youtube\.com.*(?:\?|&)v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/;
            const match = url.match(regExp);
            return match ? match[1] : null;
        }

        $('#midiaModal').on('hidden.bs.modal', function () {
            if (player && typeof player.destroy === 'function') {
                player.destroy();
                player = null;
            }
        });

        function confirmDeletar(id) {
            Swal.fire({
                title: 'Remover Mídia!',
                text: 'Esta ação vai remover a mídia. Você tem certeza?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#008ca5',
                cancelButtonColor: '#5fc3b4',
                confirmButtonText: 'Sim, Remover',
                cancelButtonText: 'Cancelar',
                iconHtml: '<i class="fa-solid fa-exclamation-circle text-danger" style="font-size: 1.5em;"></i> ',
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('removerForm');
                    form.action = `/midias/${id}`;
                    form.submit();
                }
            });
        }
    </script>
@stop





