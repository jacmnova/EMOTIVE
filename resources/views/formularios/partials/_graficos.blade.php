
@foreach($variaveis as $index => $categorias)
    <div class="col-md-2">
        <div class="card mb-3">
        <div class="card-header border-0">
            <div class="d-flex justify-content-between align-items-center">
            <h3 class="card-title">{{ $categorias->nome }}</h3>
            <a href="javascript:void(0);">Ver Detalhes</a>
            </div>
        </div>
        <div class="card-body">
            <div class="d-flex flex-column">
            <div class="position-relative mb-4">
                <canvas id="grafico-{{ $index }}" height="220" style="display: block; height: 200px; width: 100%;"></canvas>
            </div>
            <div class="d-flex flex-row justify-content-end">
                <span class="mr-2"><i class="fas fa-square text-info"></i> Baixo</span>
                <span class="mr-2"><i class="fas fa-square text-warning"></i> Médio</span>
                <span class="mr-4"><i class="fas fa-square text-danger"></i> Alto</span>
                <span><i class="fas fa-square text-secondary"></i> Questões x Pontos</span>
            </div>
            </div>
        </div>
        </div>
    </div>
@endforeach

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    @foreach($variaveis as $index => $categorias)
        const ctx{{ $index }} = document.getElementById('grafico-{{ $index }}').getContext('2d');
        new Chart(ctx{{ $index }}, {
            type: 'bar',
            data: {
                labels: ['Baixo', 'Médio', 'Alto','QxP'],
                datasets: [{
                    label: 'Pontuação',
                    data: [{{ $categorias->baixa }}, {{ $categorias->media }}, {{ $categorias->alta }},{{ $categorias->max }}],
                    backgroundColor: ['#17a2b8', '#ffc107', '#dc3545','#6c757d'],
                    borderColor: ['#0069d9', '#e0a800', '#218838', '#091d33'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: { mode: 'index', intersect: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                }
            }
        });
    @endforeach
</script>