@php
    $labels = $formulario->perguntasPorVariavel()->pluck('nome');
    $valores = $formulario->perguntasPorVariavel()->pluck('total_perguntas');
@endphp

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Variaveis do Formulário</h3>
                    <a href="javascript:void(0);">Ver Detalhes</a>
                </div>
            </div>
            <div class="card-body">
                <canvas id="barChartx" style="height: 400px; width: 600px;"></canvas>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Variaveis do Formulário</h3>
                    <a href="javascript:void(0);">Ver Detalhes</a>
                </div>
            </div>
            <div class="card-body">
                <canvas id="radarChartx" style="height: 400px; width: 400px;"></canvas>
            </div>
        </div>
    </div>



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const labels = {!! json_encode($labels) !!};
    const data = {!! json_encode($valores) !!};

    const chartConfigs = [
        {
            id: 'barChartx',
            type: 'bar',
            options: {
                responsive: true,
                plugins: { legend: { display: true } },
                scales: { y: { beginAtZero: true } }
            }
        },
        {
            id: 'radarChartx',
            type: 'radar',
            options: {
                responsive: true,
                plugins: { legend: { display: true } },
                scales: { r: { suggestedMin: 0, suggestedMax: 12 } }
            }
        },
    ];

    chartConfigs.forEach(cfg => {
        const ctx = document.getElementById(cfg.id).getContext('2d');
        new Chart(ctx, {
            type: cfg.type,
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total de Perguntas',
                    data: data,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.5)',
                        'rgba(54, 162, 235, 0.5)',
                        'rgba(255, 206, 86, 0.5)',
                        'rgba(75, 192, 192, 0.5)',
                        'rgba(153, 102, 255, 0.5)',
                        'rgba(255, 159, 64, 0.5)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1,
                    fill: cfg.type === 'radar' // só preenche no radar
                }]
            },
            options: cfg.options
        });
    });
</script>
