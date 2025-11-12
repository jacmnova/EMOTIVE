@extends('adminlte::page')

@section('title', 'Lista de Questionários')

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
            <i class="fa-solid fa-triangle-exclamation"></i> {!! Session::get('msgError') !!}
        </div>
    @endif

    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Início</a></li>
                    <li class="breadcrumb-item active">Questionários</li>
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
            Lista de Questionários
        </h3>
    </div>

    @if ($questionarios->count() > 0)
        {{-- Tabela (Desktop e Tablet) --}}
        <div class="card-body d-none d-md-block">
            <div class="table-responsive">
                <table class="table datatable table-striped dtr-inline">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Questões</th>
                            <th>Habilitado em</th>
                            <th>Etapa</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($questionarios as $formulario)
                            <tr>
                                <td>
                                    <label class="badge badge-dark">{{ $formulario->formulario->label }}</label> |
                                    {{ $formulario->formulario->nome }}
                                </td>
                                <td>{{ $formulario->formulario->perguntaCount() }}</td>
                                <td>{{ optional($formulario->created_at)->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if ($formulario->etapa_atual_numero)
                                        <span class="badge badge-info">{{ $formulario->etapa_atual_nome }}</span>
                                    @else
                                        <span class="badge badge-secondary">Sem Etapa</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusClass = [
                                            'novo' => 'primary',
                                            'pendente' => 'warning',
                                            'completo' => 'success'
                                        ][$formulario->status];
                                    @endphp
                                    <span class="badge badge-{{ $statusClass }}">{{ strtoupper($formulario->status) }}</span>
                                </td>
                                <td>
                                    @if($formulario->status !== 'completo')
                                        <a href="{{ route('questionarios.editar', $formulario->formulario->id) }}" class="btn btn-sm text-secondary">
                                            <i class="fa-regular fa-circle-play mr-1"></i> Responder
                                        </a>
                                    @else
                                        @php
                                            $temMidia = $formulario->midia && (($formulario->midia->tipo == 'url') || ($formulario->midia->tipo == 'video' && $formulario->midia->arquivo));
                                        @endphp

                                        @if($temMidia)
                                            @if($formulario->midia->tipo == 'url')
                                                <button class="btn btn-sm text-primary" onclick="abrirMidiaModal('url', '{{ $formulario->midia->url }}', {{ $formulario->id }})">
                                                    <i class="fa-solid fa-link"></i>
                                                </button>
                                            @elseif($formulario->midia->tipo == 'video' && $formulario->midia->arquivo)
                                                <button class="btn btn-sm text-success" title="Assista Vídeo" onclick="abrirMidiaModal('video', '{{ asset('storage/' . $formulario->midia->arquivo) }}', {{ $formulario->id }})">
                                                    <i class="fa-solid fa-video"></i>
                                                </button>
                                            @endif

                                            @if($formulario->video_assistido)
                                                <a href="{{ route('relatorio.show', ['formulario_id' => $formulario->formulario->id, 'usuario_id' => $user->id]) }}"  title="Visualizar Relatório" class="btn btn-sm text-info">
                                                    <i class="fa-regular fa-rectangle-list"></i>
                                                </a>
                                                <button onclick="gerarPDF({{ $user->id }}, {{ $formulario->formulario->id }})" title="Imprimir Relatório" class="btn btn-sm text-danger">
                                                    <i class="fas fa-file-pdf"></i>
                                                </button>
                                            @else
                                                <span class="text-muted"><i class="fa-solid fa-spinner"></i> Pendente</span>
                                            @endif
                                        @else
                                            {{-- Se não há mídia, mostra o relatório diretamente --}}
                                            <a href="{{ route('relatorio.show', ['formulario_id' => $formulario->formulario->id, 'usuario_id' => $user->id]) }}"  title="Visualizar Relatório" class="btn btn-sm text-info">
                                                <i class="fa-regular fa-rectangle-list"></i>
                                            </a>
                                            <button onclick="gerarPDF({{ $user->id }}, {{ $formulario->formulario->id }})" title="Imprimir Relatório" class="btn btn-sm text-danger">
                                                <i class="fas fa-file-pdf"></i>
                                            </button>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Cards (Mobile) --}}
        <div class="card-body d-block d-md-none">
            <div class="row">
                @foreach ($questionarios as $formulario)
                    <div class="col-12 mb-3">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="mb-2">
                                    <i class="fa-solid fa-file-alt text-primary mr-1"></i>
                                    {{ $formulario->formulario->nome }}
                                </h5>
                                <span class="badge badge-dark mb-3">{{ $formulario->formulario->label }}</span>

                                <p class="mb-1"><i class="fa-solid fa-circle-question mr-1 text-muted"></i> <strong>Questões:</strong> {{ $formulario->formulario->perguntaCount() }}</p>
                                <p class="mb-1"><i class="fa-solid fa-calendar-plus mr-1 text-muted"></i> <strong>Habilitado:</strong> {{ optional($formulario->created_at)->format('d/m/Y H:i') }}</p>
                                <p class="mb-1"><i class="fa-solid fa-layer-group mr-1 text-muted"></i> <strong>Etapa:</strong>
                                    @if ($formulario->etapa_atual_numero)
                                        <span class="badge badge-info">{{ $formulario->etapa_atual_nome }}</span>
                                    @else
                                        <span class="badge badge-secondary">Sem Etapa</span>
                                    @endif
                                </p>
                                <p class="mb-1"><i class="fa-solid fa-flag-checkered mr-1 text-muted"></i> <strong>Status:</strong>
                                    @php
                                        $statusClass = [
                                            'novo' => 'primary',
                                            'pendente' => 'warning',
                                            'completo' => 'success'
                                        ][$formulario->status];
                                    @endphp
                                    <span class="badge badge-{{ $statusClass }}">{{ strtoupper($formulario->status) }}</span>
                                </p>
                            </div>
                            <div class="card-footer bg-light border-top">
                                <div class="d-flex flex-wrap justify-content-start gap-2">
                                    @if($formulario->status !== 'completo')
                                        <a href="{{ route('questionarios.editar', $formulario->formulario->id) }}" class="btn btn-sm text-secondary">
                                            <i class="fa-regular fa-circle-play mr-1"></i> Responder
                                        </a>
                                    @else
                                        @php
                                            $temMidia = $formulario->midia && (($formulario->midia->tipo == 'url') || ($formulario->midia->tipo == 'video' && $formulario->midia->arquivo));
                                        @endphp

                                        @if($temMidia)
                                            @if($formulario->midia->tipo == 'url')
                                                <button class="btn btn-sm text-primary" onclick="abrirMidiaModal('url', '{{ $formulario->midia->url }}', {{ $formulario->id }})">
                                                    <i class="fa-solid fa-link"></i>
                                                </button>
                                            @elseif($formulario->midia->tipo == 'video' && $formulario->midia->arquivo)
                                                <button class="btn btn-sm text-success" title="Assista Vídeo" onclick="abrirMidiaModal('video', '{{ asset('storage/' . $formulario->midia->arquivo) }}', {{ $formulario->id }})">
                                                    <i class="fa-solid fa-video"></i>
                                                </button>
                                            @endif

                                            @if($formulario->video_assistido)
                                                <a href="{{ route('relatorio.show', ['formulario_id' => $formulario->formulario->id, 'usuario_id' => $user->id]) }}" title="Visualizar Relatório" class="btn btn-sm text-info">
                                                    <i class="fa-regular fa-rectangle-list"></i>
                                                </a>
                                                <button onclick="gerarPDF({{ $user->id }}, {{ $formulario->formulario->id }})" title="Imprimir Relatório" class="btn btn-sm text-danger">
                                                    <i class="fas fa-file-pdf"></i>
                                                </button>
                                            @else
                                                <span class="text-muted"><i class="fa-solid fa-spinner"></i> Pendente</span>
                                            @endif
                                        @else
                                            {{-- Se não há mídia, mostra o relatório diretamente --}}
                                            <a href="{{ route('relatorio.show', ['formulario_id' => $formulario->formulario->id, 'usuario_id' => $user->id]) }}" title="Visualizar Relatório" class="btn btn-sm text-info">
                                                <i class="fa-regular fa-rectangle-list"></i>
                                            </a>
                                            <button onclick="gerarPDF({{ $user->id }}, {{ $formulario->formulario->id }})" title="Imprimir Relatório" class="btn btn-sm text-danger">
                                                <i class="fas fa-file-pdf"></i>
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="row m-3">
            <div class="callout callout-warning w-100">
                <h5><i class="fa-solid fa-circle-info"></i> Nenhum Questionário foi encontrado.</h5>
                <p>Entre em contato com seu gestor para habilitar um novo questionário.</p>
            </div>
        </div>
    @endif
</div>


<!-- Modal para exibir mídia -->
<div class="modal fade" id="midiaModal" tabindex="-1" role="dialog" aria-labelledby="midiaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-fullscreen-sm-down" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary flex-wrap">
                <h5 class="modal-title w-100 mb-2" id="midiaModalLabel">
                    <label class="badge badge-dark">
                        {{ optional($formulario->formulario ?? null)->label }}
                    </label> |
                    {{ optional($formulario->formulario ?? null)->nome }}
                </h5>
                <p class="text-white small w-100 mb-0">
                    Você precisa assistir o vídeo na sua totalidade para a liberação do relatório!
                </p>
                <button type="button" class="close text-white position-absolute" style="top: 10px; right: 15px;" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body text-center p-2 p-md-4" id="midiaModalBody">
                <!-- Conteúdo dinâmico (vídeo ou imagem) -->
            </div>
        </div>
    </div>
</div>


@stop




@section('js')
<script src="{{ asset('../js/utils.js') }}"></script>
<script src="https://www.youtube.com/iframe_api"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Verificar si el elemento existe antes de agregar el event listener
    const btnGerarRelatorio = document.getElementById('btnGerarRelatorio');
    if (btnGerarRelatorio) {
        btnGerarRelatorio.addEventListener('click', function(event) {
            event.preventDefault(); // impede redirecionamento imediato

            const url = this.href;

            Swal.fire({
                title: 'Gerando análise...',
                text: 'Por favor, aguarde enquanto processamos seu relatório.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Agora redireciona manualmente (deixa o Swal aparecer)
            window.location.href = url;
        });
    }
</script>


<script>
    let player;
    let usuarioFormularioIdGlobal = null;

    function abrirMidiaModal(tipo, url, usuarioFormularioId = null) {
        usuarioFormularioIdGlobal = usuarioFormularioId;
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

        if (tipo === 'video' && usuarioFormularioId) {
            const video = document.getElementById('videoPlayer');
            video.addEventListener('ended', function() {
                registrarVideoAssistido(usuarioFormularioId);
            });
        }
    }

    function criarYouTubePlayer(videoId) {
        player = new YT.Player('youtubePlayer', {
            videoId: videoId,
            events: {
                'onReady': function(event) {
                    event.target.playVideo();
                },
                'onStateChange': function(event) {
                    if (event.data === 0 && usuarioFormularioIdGlobal) {
                        registrarVideoAssistido(usuarioFormularioIdGlobal);
                    }
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

    function registrarVideoAssistido(usuarioFormularioId) {
        fetch(`/usuario-formulario/${usuarioFormularioId}/assistido`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                assistido: true
            })
        })
        .then(response => {
            if (!response.ok) throw new Error('Erro ao registrar vídeo assistido!');
            console.log('Vídeo assistido registrado com sucesso!');
            location.reload();
        })
        .catch(error => {
            console.error(error);
        });
    }

    function gerarPDF(userId, formularioId) {
        // 1. Mostrar loading y deshabilitar botón
        const btn = event.target.closest('button');
        const originalHTML = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        // 2. Generar la URL del informe que será convertida
        const informeUrl = '{{ config("app.url") }}/meurelatorio/pdf?formulario_id=' + formularioId + '&usuario_id=' + userId;

        // 3. Configuración de la petición POST al servicio de conversión de Railway
        fetch('https://api-convet-pdf-g3nia.up.railway.app/convert-url', {
            method: 'POST',
            mode: 'cors', // Permitir CORS explícitamente
            credentials: 'omit', // No enviar cookies
            headers: {
                'Accept': 'application/pdf', // Indicamos que esperamos un PDF
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                "url": informeUrl
            })
        })
        .then(response => {
            if (response.ok) {
                // El servicio respondió exitosamente.
                // Verificamos si realmente recibimos un PDF.
                const contentType = response.headers.get('Content-Type');

                if (contentType && contentType.includes('application/json')) {
                    // Si el servicio responde OK pero devuelve JSON (ej. un link de descarga), 
                    // maneja esa lógica aquí.
                    return response.json().then(data => {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Conversión OK, pero no es un PDF',
                            text: 'El servicio externo devolvió JSON. Si esperabas el archivo, revisa el endpoint.',
                            confirmButtonText: 'OK'
                        });
                        throw new Error('Respuesta JSON en lugar de PDF');
                    });
                }

                // 4. Si la respuesta es un archivo, la procesamos como BLOB.
                return response.blob(); 
            } else {
                // Manejar errores de respuesta HTTP (4xx, 5xx)
                return response.text().then(text => {
                    let errorMsg = `Error ${response.status}: Error en el servicio de conversión.`;
                    try {
                        const jsonError = JSON.parse(text);
                        errorMsg = jsonError.detail || jsonError.message || errorMsg;
                    } catch (e) {
                        // Si no es JSON, usar el texto tal cual
                        if (text) errorMsg += ' ' + text.substring(0, 200);
                    }
                    throw new Error(errorMsg);
                });
            }
        })
        .then(pdfBlob => {
            // 5. Crear el enlace de descarga y simular el clic.
            if (pdfBlob) {
                // Creamos una URL temporal para el Blob
                const url = window.URL.createObjectURL(pdfBlob);
                
                // Creamos un elemento <a> oculto para forzar la descarga
                const a = document.createElement('a');
                a.href = url;
                a.download = `Relatorio_${userId}_${formularioId}.pdf`; // Nombre del archivo
                document.body.appendChild(a);
                
                // Simulamos el clic
                a.click();
                
                // Limpiamos la URL temporal y el elemento <a>
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);

                Swal.fire({
                    icon: 'success',
                    title: 'PDF Descargado',
                    text: 'El informe se ha descargado correctamente.',
                    timer: 3000,
                    showConfirmButton: false
                });
            }
        })
        .catch(error => {
            // 6. Manejo de errores de red o del throw anterior
            let errorMessage = error.message || 'Verifica la conexión o el servicio.';
            
            // Detectar errores de CORS específicamente
            if (error.message.includes('CORS') || error.message.includes('Failed to fetch') || error.name === 'TypeError') {
                errorMessage = 'Error de CORS: El servidor de Railway no permite peticiones desde este dominio. Contacta al administrador para configurar CORS en el servidor.';
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Error de Descarga',
                text: `No fue posible descargar el PDF: ${errorMessage}`,
                confirmButtonText: 'OK'
            });
            console.error('Error al generar y descargar PDF:', error);
        })
        .finally(() => {
            // Restaurar el botón siempre
            btn.disabled = false;
            btn.innerHTML = originalHTML;
        });
    }
</script>
@stop
