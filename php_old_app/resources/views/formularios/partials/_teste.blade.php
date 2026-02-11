
<div class="col-md-2">
    <div class="card mb-3">
        <div class="card-header border-0">
            <div class="d-flex justify-content-between align-items-center">
            <h3 class="card-title">Faixas</h3>
            <a href="javascript:void(0);">Ver Detalhes</a>
            </div>
        </div>
        <div class="card-body">
            <canvas id="stackedBarChart" style="height: 220px; min-height: 220px"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>

    var ctx = document.getElementById('stackedBarChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Baixo', 'Medio', 'Alto'],
            datasets: [
                {
                    label: 'de',
                    data: [0, 10, 30],
                    backgroundColor: '#eee'
                },
                {
                    label: 'Faixas',
                    data: [10, 20, 40],
                    backgroundColor: '#28a745'
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                tooltip: { mode: 'index', intersect: false }
            },
            scales: {
                x: {
                    stacked: true
                },
                y: {
                    stacked: true,
                    beginAtZero: true
                }
            }
        }
    });
</script>
