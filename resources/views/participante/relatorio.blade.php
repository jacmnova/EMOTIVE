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
            <div class="col-sm-6">
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Início</a></li>
                    <li class="breadcrumb-item active">Relatório</li>
                </ol>
            </div>
        </div>
    </div>

@stop

@section('content')

<div class="row">
    <div class="col-md-6">

        <div class="card">
            <div class="card-header border-0">
                <h2 class="card-title"><label class="badge badge-dark"> {{ $formulario->label }} </label> | {{ $formulario->nome }}</h2>
                <div class="card-tools">
                    <a href="#" class="btn btn-tool btn-sm">
                        <i class="fas fa-download"></i>
                    </a>
                    <a href="#" class="btn btn-tool btn-sm">
                        <i class="fas fa-bars"></i>
                    </a>
                    <a href="#" class="btn btn-sm btn-tool">
                        <i class="fa-solid fa-circle-info"></i>
                    </a>
                </div>
            </div>
            <div class="card-body" style="box-shadow: none;">
                <div class="card mb-2 pl-4 pr-4 border-0" style="box-shadow: none;">
                    <h5>Instruções</h5>
                    <p>{!! $formulario->instrucoes !!}</p>
                </div>
                <div class="card mb-2 p-2" style="box-shadow: none;">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Pergunta</th>
                                <th>Variável</th>
                                <th>Resposta</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($formulario->perguntas as $pergunta)
                                @php
                                    $resposta = $respostasUsuario->get($pergunta->id);
                                @endphp
                                <tr>
                                    <td>
                                        {{ $pergunta->numero_da_pergunta }} - {{ $pergunta->pergunta }}
                                    </td>
                                    <td>
                                        @if($pergunta->variaveis->isNotEmpty())
                                            <label class="badge badge-dark">{{ $pergunta->variaveis->pluck('nome')->join(', ') }}</label>
                                        @else
                                            <span class="text-muted">Nenhuma</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $resposta->valor_resposta ?? 'Sem resposta' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-right">
                    teste
                </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header border-0">
                <h2 class="card-title"><label class="badge badge-dark"> Pontuação</label> | Dimensão</h2>
                <div class="card-tools">
                    <a href="#" class="btn btn-tool btn-sm">
                        <i class="fas fa-download"></i>
                    </a>
                    <a href="#" class="btn btn-tool btn-sm">
                        <i class="fas fa-bars"></i>
                    </a>
                    <a href="#" class="btn btn-sm btn-tool">
                        <i class="fa-solid fa-circle-info"></i>
                    </a>
                </div>
            </div>
            <div class="card-body" style="box-shadow: none;">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Dimensões</th>
                            <th>Pontos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <ul class="list-group">
                        @foreach ($pontuacoes as $p)
                            <tr>
                                <td>
                                    <label class="badge badge-secondary">{{ $p['tag'] }}</label> | {{ $p['nome'] }}
                                </td>
                                <td>
                                    <span class="badge badge-dark badge-pill">{{ $p['valor'] }} pontos</span>
                                </td>
                            </tr>
                        @endforeach
                        </ul>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header border-0">
                <h2 class="card-title"><label class="badge badge-dark"> {{ $formulario->label }} </label> | {{ $formulario->nome }}</h2>
                <div class="card-tools">
                    <a href="#" class="btn btn-tool btn-sm">
                        <i class="fas fa-download"></i>
                    </a>
                    <a href="#" class="btn btn-tool btn-sm">
                        <i class="fas fa-bars"></i>
                    </a>
                    <a href="#" class="btn btn-sm btn-tool">
                        <i class="fa-solid fa-circle-info"></i>
                    </a>
                </div>
            </div>
            <div class="card-body" style="box-shadow: none;">
                <div class="card mt-4">
                    <div class="card-body">
                        <h4 class="card-title">Pontuação por Variável</h4>
                        <canvas id="graficoVariaveis" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header border-0">
                <h2 class="card-title"><label class="badge badge-dark"> Pontuação</label> | Dimensão</h2>
                <div class="card-tools">
                    <a href="#" class="btn btn-tool btn-sm">
                        <i class="fas fa-download"></i>
                    </a>
                    <a href="#" class="btn btn-tool btn-sm">
                        <i class="fas fa-bars"></i>
                    </a>
                    <a href="#" class="btn btn-sm btn-tool">
                        <i class="fa-solid fa-circle-info"></i>
                    </a>
                </div>
            </div>

            <div class="card-body">
                <h4 class="mb-3">Classificação por Variável</h4>

                <table class="table table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Variável</th>
                            <th>Tag</th>
                            <th>Pontuação</th>
                            <th>Classificação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($variaveis as $registro)
                            @php
                                $pontuacao = null;
                                foreach($pontuacoes as $pontos) {
                                    if (mb_strtoupper($registro->tag, 'UTF-8') === $pontos['tag']) {
                                        $pontuacao = $pontos['valor'];
                                        break;
                                    }
                                }

                                if ($pontuacao !== null) {
                                    if ($pontuacao <= $registro->B) {
                                        $classificacao = $registro->baixa;
                                        $badge = 'success';
                                    } elseif ($pontuacao <= $registro->M) {
                                        $classificacao = $registro->moderada;
                                        $badge = 'warning';
                                    } else {
                                        $classificacao = $registro->alta;
                                        $badge = 'danger';
                                    }
                                } else {
                                    $classificacao = 'Sem dados';
                                    $badge = 'secondary';
                                }
                            @endphp
                            <tr>
                                <td>{{ $registro->nome }}</td>
                                <td><strong>{{ mb_strtoupper($registro->tag, 'UTF-8') }}</strong></td>
                                <td>{{ $pontuacao ?? '–' }}</td>
                                <td><span class="badge badge-{{ $badge }}">{{ $classificacao }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>


@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}

@stop

@section('js')
    <script src="{{ asset('../js/utils.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Dados vindos do backend (agora um array de objetos)
    const pontuacoes = @json($pontuacoes);

    // Extrai nomes + tags para os labels
    const labels = pontuacoes.map(p => `${p.tag} | ${p.nome}`);
    const data = pontuacoes.map(p => p.valor);

    // Função para gerar cores aleatórias
    function corAleatoria(opacidade = 1) {
        const r = Math.floor(Math.random() * 200);
        const g = Math.floor(Math.random() * 200);
        const b = Math.floor(Math.random() * 200);
        return `rgba(${r}, ${g}, ${b}, ${opacidade})`;
    }

    // Gera cores aleatórias para cada barra
    const backgroundColors = data.map(() => corAleatoria(0.6));
    const borderColors = backgroundColors.map(cor => cor.replace('0.6', '1'));

    // Monta o gráfico
    const ctx = document.getElementById('graficoVariaveis').getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Pontuação',
                data: data,
                backgroundColor: backgroundColors,
                borderColor: borderColors,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: { enabled: true }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Pontuação'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Dimensões'
                    }
                }
            }
        }
    });
</script>



@stop