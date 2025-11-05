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
    <div class="col-md-12">
        @include('participante.partials._radar')
        @include('participante.partials._dimensoes')
        @include('participante.partials._barras')
        @include('participante.partials._recomenda')
        @include('participante.partials._graficos-dimensionais')
        @include('participante.partials._analise')
    </div>

        @if(Auth::user()->sa === true)
            @include('participante.partials._gabarito')
        @endif
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
    const pontuacoes = @json($pontuacoes);
    const labels = pontuacoes.map(p => `${p.tag}`);
    const data = pontuacoes.map(p => p.valor);

    function gerarCores(qtd, opacidade = 1) {
        const cores = [
            'rgba(255, 99, 132, OP)',
            'rgba(54, 162, 235, OP)',
            'rgba(255, 206, 86, OP)',
            'rgba(75, 192, 192, OP)',
            'rgba(153, 102, 255, OP)',
            'rgba(255, 159, 64, OP)',
            'rgba(199, 199, 199, OP)',
            'rgba(83, 102, 255, OP)',
            'rgba(66, 135, 245, OP)',
            'rgba(245, 66, 212, OP)'
        ];
        return Array.from({ length: qtd }, (_, i) => cores[i % cores.length].replace('OP', opacidade));
    }

    const backgroundColors = gerarCores(data.length, 0.6);
    const borderColors = gerarCores(data.length, 1);

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

<script>

    const labelsRadar = pontuacoes.map(p => p.tag);
    const dataRadar = pontuacoes.map(p => p.valor);

    function gerarCoresRadar(qtd, opacidade = 1) {
        const cores = [
            'rgba(255, 99, 132, OP)',
            'rgba(54, 162, 235, OP)',
            'rgba(255, 206, 86, OP)',
            'rgba(75, 192, 192, OP)',
            'rgba(153, 102, 255, OP)',
            'rgba(255, 159, 64, OP)',
            'rgba(199, 199, 199, OP)',
            'rgba(83, 102, 255, OP)',
            'rgba(66, 135, 245, OP)',
            'rgba(245, 66, 212, OP)'
        ];
        return Array.from({ length: qtd }, (_, i) => cores[i % cores.length].replace('OP', opacidade));
    }

    const coresFundo = gerarCoresRadar(dataRadar.length, 0.2);
    const coresBorda = gerarCoresRadar(dataRadar.length, 1);

    // Dataset geral (linha cinza clara, fundo transparente)
    const datasetPrincipal = {
        label: 'Pontuação Geral',
        data: dataRadar,
        backgroundColor: 'rgba(192, 192, 192, 0)', // silver transparente
        borderColor: 'rgba(192, 192, 192, 1)',     // silver
        borderWidth: 2,
        pointBackgroundColor: 'rgba(192, 192, 192, 1)',
        pointBorderColor: '#fff',
        pointHoverBackgroundColor: '#fff',
        pointHoverBorderColor: 'rgba(192, 192, 192, 1)',
        order: 2 // menor prioridade → desenha por baixo
    };

    // Datasets coloridos (um para cada dimensão, com nome completo)
    const datasetsColoridos = pontuacoes.map((ponto, i) => {
        const data = Array(pontuacoes.length).fill(0);
        data[i] = ponto.valor;

        return {
            label: ponto.nome,
            data: data,
            backgroundColor: coresFundo[i],
            borderColor: coresBorda[i],
            pointBackgroundColor: coresBorda[i],
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: coresBorda[i],
            borderWidth: 1,
            fill: true,
            order: 1 // prioridade visual acima da linha cinza
        };
    });

    const ctxRadar = document.getElementById('graficoRadar').getContext('2d');

    new Chart(ctxRadar, {
        type: 'radar',
        data: {
            labels: labelsRadar,
            datasets: [datasetPrincipal, ...datasetsColoridos]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'right' },
                tooltip: { enabled: true }
            },
            scales: {
                r: {
                    angleLines: { display: true },
                    suggestedMin: 0,
                    suggestedMax: Math.max(...dataRadar) + 5
                }
            }
        }
    });
</script>



@stop