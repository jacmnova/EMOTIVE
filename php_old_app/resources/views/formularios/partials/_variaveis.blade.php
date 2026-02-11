
    <div class="card col-12">
        <div class="card-header border-0">
            <div class="d-flex justify-content-between align-items-center">
            <h3 class="card-title">Variaveis do Formulário</h3>
            <a href="javascript:void(0);">Ver Detalhes</a>
            </div>
        </div>
        <div class="card-body">
            <canvas id="radarCharty"></canvas>
        </div>
    </div>

    <div class="card col-12">
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


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('radarCharty').getContext('2d');

    const labels = [
        @foreach ($formulario->perguntasPorVariavel() as $item)
            '{{ $item->nome }}',
        @endforeach
    ];

    const data = [
        @foreach ($formulario->perguntasPorVariavel() as $item)
            {{ $item->total_perguntas }},
        @endforeach
    ];

    const myRadarCharty = new Chart(ctx, {
        type: 'radar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total de Perguntas',
                data: data,
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
                    display: true
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
