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
        @include('participante.partials._eixos_analiticos')
        @include('participante.partials._risco_descarrilamento')
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
    <style>
        .json-display {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 15px;
            max-height: 500px;
            overflow-y: auto;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
    </style>
@stop

@section('js')
    <script src="{{ asset('../js/utils.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@if(Session::has('pythonApiError'))
<!-- Modal para mostrar error de API Python -->
<div class="modal fade" id="modalErrorPython" tabindex="-1" role="dialog" aria-labelledby="modalErrorPythonLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title" id="modalErrorPythonLabel">
                    <i class="fa-solid fa-triangle-exclamation"></i> Error al Enviar Datos a la API de Python
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <strong><i class="fa-solid fa-info-circle"></i> Información:</strong>
                    <p class="mb-0">No se pudo enviar los datos a la API de Python. A continuación se muestra la estructura JSON que se intentó enviar:</p>
                </div>
                
                @if(Session::has('pythonApiErrorMessage'))
                <div class="alert alert-danger">
                    <strong>Error:</strong> {{ Session::get('pythonApiErrorMessage') }}
                </div>
                @endif
                
                <h6 class="mb-2"><strong>Estructura JSON:</strong></h6>
                <div class="json-display">
{{ Session::get('pythonApiErrorData', 'No hay datos disponibles') }}
                </div>
                
                <div class="mt-3">
                    <small class="text-muted">
                        <i class="fa-solid fa-lightbulb"></i> 
                        <strong>Nota:</strong> Puedes copiar este JSON y enviarlo manualmente a tu API de Python, o verificar la configuración de <code>PYTHON_RELATORIO_API_URL</code> en el archivo .env
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="copiarJSON()">
                    <i class="fa-regular fa-copy"></i> Copiar JSON
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Mostrar modal automáticamente cuando hay error
    $(document).ready(function() {
        $('#modalErrorPython').modal('show');
    });
    
    // Función para copiar JSON al portapapeles
    function copiarJSON() {
        const jsonText = document.querySelector('.json-display').textContent;
        navigator.clipboard.writeText(jsonText).then(function() {
            alert('JSON copiado al portapapeles');
        }, function(err) {
            console.error('Error al copiar:', err);
            alert('Error al copiar. Por favor, selecciona y copia manualmente.');
        });
    }
</script>
@endif

<script>
    const pontuacoes = @json($pontuacoes);
    const labels = pontuacoes.map(p => `${p.tag}`);
    const data = pontuacoes.map(p => p.normalizada || p.valor);

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
    const dataRadar = pontuacoes.map(p => p.normalizada || p.valor);

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
                    min: 0,
                    max: 100,
                    ticks: {
                        stepSize: 20,
                        min: 0,
                        max: 100
                    }
                }
            }
        }
    });
</script>



@stop