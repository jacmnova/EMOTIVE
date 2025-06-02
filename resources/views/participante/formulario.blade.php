@extends('adminlte::page')

@section('title', 'Questionário')

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
                    <li class="breadcrumb-item active">Questionário</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')

    <form action="{{ route('respostas.salvar') }}" method="POST">
        @csrf
        <input name="formulario_id" value="{{ $formulario->id }}" hidden>

        <div class="card">
            <div class="card-header border-0">
                <h2 class="card-title">
                    <label class="badge badge-dark">{{ $formulario->label }}</label> | {{ $formulario->nome }}
                </h2>
            </div>

            <div class="card-body">
                <div class="card mb-2 pl-4 pr-4 border-0">
                    <h5>Instruções</h5>
                    <p>{!! $formulario->instrucoes !!}</p>
                </div>

                <div class="card mb-2 p-2">
                    <table class="table table-striped">
                        <tbody>
                            @php
                                $perguntasEtapa = $formulario->perguntas->whereBetween('id', [$etapaAtual->de, $etapaAtual->ate]);
                            @endphp

                            @foreach($perguntasEtapa as $pergunta)
                                @php
                                    $resposta = $respostasUsuario->get($pergunta->id);
                                @endphp

                                <tr>
                                    <td style="width: 70%;">
                                        {{ $pergunta->numero_da_pergunta }} - {{ $pergunta->pergunta }}
                                    </td>

                                    @if($formulario->score_ini !== null && $formulario->score_fim !== null)
                                        @for($i = 0; $i <= ($formulario->score_fim - $formulario->score_ini); $i++)
                                            @php
                                                $valor = $formulario->score_ini + $i;
                                            @endphp
                                            <td style="text-align: center;">
                                                <div class="custom-control custom-radio">
                                                    <label class="d-flex align-items-center justify-content-center" for="radio_{{ $pergunta->id }}_{{ $valor }}" style="font-size: 12px;">
                                                        {{ $valor }}
                                                        <input class="custom-control-input" type="radio" id="radio_{{ $pergunta->id }}_{{ $valor }}" name="respostas[{{ $pergunta->id }}]" value="{{ $valor }}" @if($resposta && $resposta->valor_resposta == $valor) checked @endif>
                                                        <span class="custom-control-label"></span>
                                                    </label>
                                                </div>
                                            </td>
                                        @endfor
                                    @endif
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

@stop
