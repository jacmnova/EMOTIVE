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
        @include('participante.partials._radar')
        @include('participante.partials._gabarito')
    </div>

    <div class="col-md-6">
        @include('participante.partials._dimensoes')
        @include('participante.partials._barras')
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
    const labels = pontuacoes.map(p => `${p.tag}`);
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

<script>
    // Radar usa os mesmos dados
    const ctxRadar = document.getElementById('graficoRadar').getContext('2d');

    new Chart(ctxRadar, {
        type: 'radar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Pontuação',
                data: data,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgba(54, 162, 235, 1)'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    enabled: true
                },
                title: {
                    display: false
                }
            },
            scales: {
                r: {
                    angleLines: {
                        display: true
                    },
                    suggestedMin: 0,
                    suggestedMax: Math.max(...data) + 5
                }
            }
        }
    });
</script>


@stop