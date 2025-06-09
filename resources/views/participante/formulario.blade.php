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
<div class="d-none d-md-block">
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
                    <h5>Instruções</h5>
                    <p>{!! $formulario->instrucoes !!}</p>
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
<div class="d-block d-md-none">
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
                    {!! $formulario->instrucoes !!}
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
                        config.confirmButtonText = 'Ir para o início';
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

                Swal.fire(config).then(() => {
                    if (data.status === 'formulario_concluido') {
                        window.location.href = "{{ route('questionarios.usuario') }}";
                    } else {
                        location.reload();
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
</script>

@stop
