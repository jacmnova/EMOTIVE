
<div class="col-md-2">
    <div class="card mb-3">
        <div class="card-header border-0">
            <div class="d-flex justify-content-between align-items-center">
            <h3 class="card-title">ENTJ</h3>
            <a href="javascript:void(0);">Ver Detalhes</a>
            </div>
        </div>
        <div class="card-body">
        <canvas id="radarChart"></canvas>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('radarChart').getContext('2d');

    const radarChart = new Chart(ctx, {
        type: 'radar',
        data: {
            labels: [
                'Extrovertido(E)', 
                'Sensitivo(S)', 
                'Sentimental(F)', 
                'Julgador(J)', 
                'Introvertido(I)', 
                'Pensativo(T)', 
                'Intuitivo(N)', 
                'Perceptivo(P)'
            ],
            datasets: [{
                label: 'Resultado',
                data: [80, 70, 60, 75, 85, 65, 55, 90],
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
            scales: {
                r: {
                    angleLines: {
                        display: true
                    },
                    suggestedMin: 0,
                    suggestedMax: 100
                }
            }
        }
    });
</script>