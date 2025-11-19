@extends('adminlte::page')

@section('title', 'Questionário')

@section('content_header')
    @if(Session::has('msgSuccess'))
        <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <i class="fa-regular fa-bell mr-1"></i> {!! Session::get('msgSuccess') !!}
        </div>
    @elseif(Session::has('msgError'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <i class="fa-solid fa-triangle-exclamation mr-1"></i> {!! Session::get('msgError') !!}
        </div>
    @endif
@stop

@section('content')

{{-- FORMULÁRIO DESKTOP/TABLET --}}
<div class="d-none d-md-block mt-2">
    <form id="formulario-perguntas">
        @csrf
        <input name="formulario_id" value="{{ $formulario->id }}" hidden>
        <input name="etapa_atual" value="{{ $etapaAtual->numero ?? $etapaAtual->id }}" hidden>
        <input name="etapa_de" value="{{ $etapaAtual->de }}" hidden>
        <input name="etapa_ate" value="{{ $etapaAtual->ate }}" hidden>

        <div class="card">
            <div class="card-header border-0">
                <h2 class="card-title">
                    <label class="badge badge-dark">{{ $formulario->label }}</label> | {{ $formulario->nome }}
                </h2>
            </div>

            <div class="card-body">
                <div class="card m-2 p-4">
                    <h5>Instruções:</h5>
                    <p style="text-align: justify;">{!! $formulario->instrucoes !!}</p>
                </div>

                <div class="card m-2 p-4">
                    <table class="table table-striped">
                        <tbody>
                            @php
                                $perguntasEtapa = $formulario->perguntas->whereBetween('id', [$etapaAtual->de, $etapaAtual->ate]);
                            @endphp

                            @foreach($perguntasEtapa as $pergunta)
                                @php $resposta = $respostasUsuario->get($pergunta->id); @endphp

                                <tr>
                                    <td style="width: 70%;">
                                        {{ $pergunta->numero_da_pergunta }} - {{ $pergunta->pergunta }}
                                    </td>

                                    @for($i = $formulario->score_ini; $i <= $formulario->score_fim; $i++)
                                        <td style="text-align: center;">
                                            <div class="custom-control custom-radio">
                                                <label class="d-flex align-items-center justify-content-center" for="radio_{{ $pergunta->id }}_{{ $i }}" style="font-size: 12px;">
                                                    {{ $i }}
                                                    <input class="custom-control-input" type="radio" id="radio_{{ $pergunta->id }}_{{ $i }}" name="respostas[{{ $pergunta->id }}]" value="{{ $i }}" @if($resposta && $resposta->valor_resposta == $i) checked @endif>
                                                    <span class="custom-control-label"></span>
                                                </label>
                                            </div>
                                        </td>
                                    @endfor
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer text-right">
                <button type="submit" class="btn btn-dark" style="width: 150px;">Salvar Respostas</button>
            </div>
        </div>
    </form>
</div>

{{-- FORMULÁRIO MOBILE --}}
<div class="d-block d-md-none mt-2">
    <form id="formulario-perguntas-mobile">
        @csrf
        <input type="hidden" name="formulario_id" value="{{ $formulario->id }}">
        <input type="hidden" name="etapa_atual" value="{{ $etapaAtual->numero ?? $etapaAtual->id }}">
        <input type="hidden" name="etapa_de" value="{{ $etapaAtual->de }}">
        <input type="hidden" name="etapa_ate" value="{{ $etapaAtual->ate }}">

        <div class="card shadow-sm">
            <div class="card-header">
                <h2 class="h5 mb-0">
                    <span class="badge badge-dark">{{ $formulario->label }}</span> {{ $formulario->nome }}
                </h2>
            </div>

            <div class="card-body p-2">
                <div class="alert alert-info small">
                    <strong>Instruções:</strong><br>
                    <p style="text-align: justify;">{!! $formulario->instrucoes !!}<p>
                </div>

                @php $perguntasEtapa = $formulario->perguntas->whereBetween('id', [$etapaAtual->de, $etapaAtual->ate]); @endphp

                @foreach($perguntasEtapa as $pergunta)
                    @php $resposta = $respostasUsuario->get($pergunta->id); @endphp

                    <div class="card mb-3">
                        <div class="card-body p-3">
                            <p class="font-weight-bold small mb-2">
                                {{ $pergunta->numero_da_pergunta }} - {{ $pergunta->pergunta }}
                            </p>

                            <div class="d-flex flex-wrap justify-content-start">
                                @for($i = $formulario->score_ini; $i <= $formulario->score_fim; $i++)
                                    <div class="custom-control custom-radio mr-3 mb-2" style="min-width: 45px;">
                                        <input type="radio"
                                               id="radio_mobile_{{ $pergunta->id }}_{{ $i }}"
                                               name="respostas[{{ $pergunta->id }}]"
                                               class="custom-control-input"
                                               value="{{ $i }}"
                                               @if($resposta && $resposta->valor_resposta == $i) checked @endif>
                                        <label class="custom-control-label small" for="radio_mobile_{{ $pergunta->id }}_{{ $i }}">{{ $i }}</label>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="card-footer text-center">
                <button type="submit" class="btn btn-dark w-100">Salvar Respostas</button>
            </div>
        </div>
    </form>
</div>

{{-- JS UNIFICADO PARA AMBOS --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function enviarFormulario(formId) {
        const form = document.getElementById(formId);
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(form);

            fetch("{{ route('respostas.salvar') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                let config = { icon: 'success', confirmButtonText: 'Continuar' };
                switch (data.status) {
                    case 'etapa_concluida':
                        config.title = `Etapa ${data.etapa} concluída!`;
                        config.text = `Você completou ${data.percentual}% do questionário.`;
                        break;
                    case 'formulario_concluido':
                        config.title = 'Formulário finalizado!';
                        config.text = 'Você respondeu 100% das perguntas.';
                        config.showDenyButton = true;
                        config.showCancelButton = true;
                        config.confirmButtonText = 'Ir para o início';
                        config.denyButtonText = 'Ver relatório';
                        config.cancelButtonText = 'Baixar o relatório (.pdf)';
                        config.confirmButtonColor = '#3085d6';
                        config.denyButtonColor = '#28a745';
                        config.cancelButtonColor = '#dc3545';
                        config.preDeny = () => {
                            // Ver relatório - ejecutar antes de cerrar el modal
                            const relatorioUrl = "{!! route('relatorio.show', ['formulario_id' => $formulario->id, 'usuario_id' => $user->id]) !!}";
                            window.location.href = relatorioUrl;
                            return true; // Permitir el cierre del modal (la redirección cambiará la página)
                        };
                        break;
                    case 'etapa_incompleta':
                        config.icon = 'info';
                        config.title = `Etapa incompleta`;
                        config.text = `Você respondeu ${data.percentual}% das perguntas.`;
                        break;
                    default:
                        config.title = 'Respostas salvas com sucesso.';
                        config.timer = 1500;
                        config.showConfirmButton = false;
                }

                Swal.fire(config).then((result) => {
                    if (data.status === 'formulario_concluido') {
                        if (result.isConfirmed) {
                            // Ir para o início
                            window.location.href = "{{ route('questionarios.usuario') }}";
                        } else if (result.isDismissed && result.dismiss === Swal.DismissReason.cancel) {
                            // Baixar o relatório (.pdf)
                            gerarPDFRelatorio({{ $user->id }}, {{ $formulario->id }});
                        }
                        // Nota: El botón "Ver relatório" se maneja en preDeny
                    } else {
                        const url = new URL(window.location.href);
                        url.searchParams.set('scroll', 'top');
                        window.location.href = url.toString();
                    }
                });
            })
            .catch(() => {
                Swal.fire({ icon: 'error', title: 'Erro ao salvar respostas.' });
            });
        });
    }

    enviarFormulario('formulario-perguntas');
    enviarFormulario('formulario-perguntas-mobile');

    // Função para gerar PDF do relatório (similar à função gerarPDF do index)
    function gerarPDFRelatorio(userId, formularioId) {
        // Mostrar loading
        Swal.fire({
            title: 'Gerando PDF...',
            text: 'Por favor, aguarde enquanto processamos seu relatório.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Gerar a URL do relatório que será convertida
        const informeUrl = '{{ config("app.url") }}/meurelatorio/pdf?formulario_id=' + formularioId + '&usuario_id=' + userId;

        // Configuração da requisição POST ao serviço de conversão
        fetch('https://api-convet-pdf-g3nia.up.railway.app/convert-url-paginated', {
            method: 'POST',
            mode: 'cors',
            credentials: 'omit',
            headers: {
                'Accept': 'application/pdf',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                "url": informeUrl
            })
        })
        .then(response => {
            if (response.ok) {
                const contentType = response.headers.get('Content-Type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json().then(data => {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Aviso',
                            text: 'O serviço retornou JSON em vez de PDF.',
                            confirmButtonText: 'OK'
                        });
                        throw new Error('Resposta JSON em vez de PDF');
                    });
                }
                return response.blob();
            } else {
                return response.text().then(text => {
                    let errorMsg = `Erro ${response.status}: Erro no serviço de conversão.`;
                    try {
                        const jsonError = JSON.parse(text);
                        errorMsg = jsonError.detail || jsonError.message || errorMsg;
                    } catch (e) {
                        if (text) errorMsg += ' ' + text.substring(0, 200);
                    }
                    throw new Error(errorMsg);
                });
            }
        })
        .then(pdfBlob => {
            if (pdfBlob) {
                const url = window.URL.createObjectURL(pdfBlob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `Relatorio_${userId}_${formularioId}.pdf`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);

                Swal.fire({
                    icon: 'success',
                    title: 'PDF Baixado',
                    text: 'O relatório foi baixado com sucesso.',
                    timer: 3000,
                    showConfirmButton: false
                });
            }
        })
        .catch(error => {
            let errorMessage = error.message || 'Verifique a conexão ou o serviço.';
            if (error.message.includes('CORS') || error.message.includes('Failed to fetch') || error.name === 'TypeError') {
                errorMessage = 'Erro de CORS: O servidor não permite requisições deste domínio.';
            }
            Swal.fire({
                icon: 'error',
                title: 'Erro ao Baixar',
                text: `Não foi possível baixar o PDF: ${errorMessage}`,
                confirmButtonText: 'OK'
            });
        });
    }
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('scroll') === 'top') {
            window.scrollTo({ top: 0, behavior: 'smooth' });

            // Remove o parâmetro da URL sem recarregar
            history.replaceState(null, '', window.location.pathname);
        }
    });
</script>
@stop
