
<div class="col-md-2">
    <div class="card mb-3">
        <div class="card-header border-0">
            <div class="d-flex justify-content-between align-items-center">
            <h3 class="card-title">Variaveis do Formulário</h3>
            <a href="javascript:void(0);">Ver Detalhes</a>
            </div>
        </div>
        <div class="card-body">
            <canvas id="radarChart" style="height: 600px; width: 600px;"></canvas>
        </div>
    </div>
</div>
<div class="col-md-3">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fa-solid fa-file-alt" style="margin-right: 5px;"></i>
                Perguntas por Variavel
            </h3>
        </div>

        <div class="card-body mr-1">
            <table class="table datatable table-striped dtr-inline mr-1 ml-1">
                <thead>
                    <tr>
                        <th style="width: 60%">Variável</th>
                        <th style="width: 10%">Perguntas</th>
                        <th style="width: 10%">Score</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($formulario->perguntasPorVariavel() as $formularios)
                        <tr>
                            <td>{{ $formularios->nome }}</td>
                            <td>{{ $formularios->total_perguntas }}</td>
                            <td>{{ $formularios->total_perguntas * $formulario->score_fim }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    const ctx = document.getElementById('radarChart').getContext('2d');

    const myRadarChart = new Chart(ctx, {
    type: 'radar',
    data: {
        labels: [
        'Assédio Moral',
        'Baixa Realização',
        'Despersonalização',
        'Exaustão Emocional',
        'Excesso de Trabalho',
        'Fatores Psicossociais'
        ],
        datasets: [{
        label: 'Resultado',
        data: [9, 4, 3, 3, 11, 6],
        fill: true,
        backgroundColor: 'rgba(0, 123, 255, 0.2)',
        borderColor: '#007bff',
        pointBackgroundColor: '#007bff',
        pointBorderColor: '#fff',
        pointHoverBackgroundColor: '#fff',
        pointHoverBorderColor: '#007bff'
        }]
    },
    options: {
        responsive: true,
        plugins: {
        legend: {
            display: false
        }
        },
        scales: {
        r: {
            suggestedMin: 0,
            suggestedMax: 12,
            ticks: {
            stepSize: 2
            }
        }
        }
    }
    });
</script>