<script>
    const pontuacoes = @json($pontuacoes);
    const labelsRadar = pontuacoes.map(p => p.tag);
    const dataRadar = pontuacoes.map(p => p.valor);

    // Cores para cada dimensão
    const coresPorTag = {
        'EXEM': 'rgba(139, 69, 19, 0.8)',   // Brown
        'REPR': 'rgba(210, 105, 30, 0.8)',  // Chocolate
        'DECI': 'rgba(255, 140, 0, 0.8)',   // Orange
        'FAPS': 'rgba(144, 238, 144, 0.8)', // Light Green
        'EXTR': 'rgba(34, 139, 34, 0.8)',   // Forest Green
        'ASMO': 'rgba(65, 105, 225, 0.8)'   // Royal Blue
    };

    // Criar gráfico Radar E.MO.TI.VE
    const ctxRadar = document.getElementById('graficoRadarEmotive');
    if (ctxRadar) {
        const datasets = [];
        
        // Dataset principal (linha geral)
        datasets.push({
            label: 'Pontuação Geral',
            data: dataRadar,
            backgroundColor: 'rgba(192, 192, 192, 0.1)',
            borderColor: 'rgba(192, 192, 192, 1)',
            borderWidth: 2,
            pointBackgroundColor: 'rgba(192, 192, 192, 1)',
            pointBorderColor: '#fff',
            pointRadius: 4,
            order: 2
        });
        
        // Dataset para cada dimensão (com cores específicas)
        pontuacoes.forEach((ponto, index) => {
            const data = Array(pontuacoes.length).fill(0);
            data[index] = ponto.valor;
            
            datasets.push({
                label: ponto.nome,
                data: data,
                backgroundColor: coresPorTag[ponto.tag] || 'rgba(128, 128, 128, 0.3)',
                borderColor: coresPorTag[ponto.tag] || 'rgba(128, 128, 128, 1)',
                borderWidth: 2,
                pointBackgroundColor: coresPorTag[ponto.tag] || 'rgba(128, 128, 128, 1)',
                pointBorderColor: '#fff',
                pointRadius: 5,
                pointHoverRadius: 7,
                fill: true,
                order: 1
            });
        });

        new Chart(ctxRadar, {
            type: 'radar',
            data: {
                labels: labelsRadar,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            font: {
                                size: 11
                            },
                            padding: 15
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.r + ' pontos';
                            }
                        }
                    }
                },
                scales: {
                    r: {
                        beginAtZero: true,
                        max: Math.max(...dataRadar) + 20,
                        ticks: {
                            stepSize: 20,
                            font: {
                                size: 10
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        pointLabels: {
                            font: {
                                size: 11,
                                weight: 'bold'
                            }
                        }
                    }
                }
            }
        });
    }
</script>

