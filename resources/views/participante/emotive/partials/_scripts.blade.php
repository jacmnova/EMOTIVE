<script>
    const pontuacoes = @json($pontuacoes);
    
    // Ordenar las dimensiones según la imagen (empezando desde arriba-derecha en sentido horario)
    const ordemDimensoes = ['EXEM', 'REPR', 'DECI', 'FAPS', 'EXTR', 'ASMO'];
    const pontuacoesOrdenadas = [];

    console.log(pontuacoes, 'puntos');
    
    ordemDimensoes.forEach(tag => {
        const ponto = pontuacoes.find(p => p.tag === tag);
        if (ponto) {
            pontuacoesOrdenadas.push(ponto);
        }
    });
    
    // Si faltan algunas, agregar las restantes
    pontuacoes.forEach(ponto => {
        if (!pontuacoesOrdenadas.find(p => p.tag === ponto.tag)) {
            pontuacoesOrdenadas.push(ponto);
        }
    });
    
    const labelsRadar = pontuacoesOrdenadas.map(p => p.tag);
    // Usar valores absolutos para el gráfico radar
    const dataRadar = pontuacoesOrdenadas.map(p => p.valor);
    
    // Calcular el máximo del gráfico: usar el máximo más alto de todas las dimensiones
    // Si algún máximo_grafico es 200, usar 200 para todo el gráfico; sino 100
    const maximosGrafico = pontuacoesOrdenadas.map(p => p.maximo_grafico || 100);
    const maximoGrafico = Math.max(...maximosGrafico);
    
    // Cores específicas para cada dimensão según la imagen
    const coresPorTag = {
        'EXEM': '#4F3F23',   // Dark Brown (marrón oscuro)
        'REPR': '#A4977F',   // Dark Brown (marrón oscuro)
        'DECI': '#D79648',   // Orange (naranja)
        'FAPS': '#62807C',   // Light Green (verde claro)
        'EXTR': '#636A99',   // Purple (púrpura)
        'ASMO': '#005882'    // Royal Blue (azul)
    };
    
    // Colores con transparencia para los segmentos
    const coresTransparentes = {
        'EXEM': 'rgba(139, 69, 19, 0.25)',   // Dark Brown con transparencia
        'REPR': 'rgba(139, 69, 19, 0.25)',   // Dark Brown con transparencia
        'DECI': 'rgba(255, 140, 0, 0.25)',   // Orange con transparencia
        'FAPS': 'rgba(144, 238, 144, 0.25)', // Light Green con transparencia
        'EXTR': 'rgba(147, 112, 219, 0.25)', // Purple con transparencia
        'ASMO': 'rgba(65, 105, 225, 0.25)'   // Royal Blue con transparencia
    };
    
    // Nombres completos para los labels
    const nomesCompletos = {
        'EXEM': 'Exaustão Emocional',
        'REPR': 'Realização Profissional',
        'DECI': 'Despersonalização / Cinismo',
        'FAPS': 'Fatores Psicossociais',
        'EXTR': 'Excesso de Trabalho',
        'ASMO': 'Assédio Moral'
    };

    // Criar gráfico Radar E.MO.TI.VE
    const ctxRadar = document.getElementById('graficoRadarEmotive');
    if (ctxRadar) {
        const datasets = [];
        
        // Dataset principal (línea azul claro con área rellena)
        datasets.push({
            label: 'Pontuação Geral',
            data: dataRadar,
            backgroundColor: 'rgba(173, 216, 230, 0.4)', // Light blue fill (más visible)
            borderColor: 'rgba(173, 216, 230, 1)', // Light blue line
            borderWidth: 2.5,
            pointBackgroundColor: 'rgba(173, 216, 230, 1)',
            pointBorderColor: '#fff',
            pointRadius: 0, // Sin puntos visibles en la línea principal
            pointHoverRadius: 5,
            fill: true,
            order: 2
        });
        
        // Dataset para cada dimensão (segmentos de colores)
        pontuacoesOrdenadas.forEach((ponto, index) => {
            const data = Array(pontuacoesOrdenadas.length).fill(0);
            data[index] = ponto.valor;
            
            const cor = coresPorTag[ponto.tag] || '#808080';
            const corTransparente = coresTransparentes[ponto.tag] || 'rgba(128, 128, 128, 0.25)';
            
            datasets.push({
                label: nomesCompletos[ponto.tag] || ponto.tag,
                data: data,
                backgroundColor: corTransparente, // Color con transparencia
                borderColor: 'transparent', // Sin borde para los segmentos
                borderWidth: 0,
                pointBackgroundColor: cor,
                pointBorderColor: '#fff',
                pointRadius: 0,
                pointHoverRadius: 0,
                fill: true,
                order: 1
            });
        });

        // Crear labels con nombres completos
        const labelsCompletos = labelsRadar.map(tag => nomesCompletos[tag] || tag);
        
        const chart = new Chart(ctxRadar, {
            type: 'radar',
            data: {
                labels: labelsCompletos,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false // Ocultar leyenda
                    },
                    tooltip: {
                        enabled: true,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.r.toFixed(0) + ' pontos';
                            }
                        }
                    }
                },
                scales: {
                    r: {
                        beginAtZero: true,
                        max: maximoGrafico, // Máximo dinámico (100 o 200)
                        min: 0,
                        ticks: {
                            stepSize: maximoGrafico === 200 ? 40 : 20, // Si máximo es 200, stepSize 40; si es 100, stepSize 20
                            font: {
                                size: 10,
                                family: 'Quicksand'
                            },
                            color: '#666',
                            backdropColor: 'transparent'
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.15)', // Gris claro para los anillos
                            lineWidth: 1
                        },
                        angleLines: {
                            color: 'rgba(0, 0, 0, 0.15)', // Gris claro para las líneas radiales
                            lineWidth: 1
                        },
                        pointLabels: {
                            font: {
                                size: 11,
                                weight: 'bold',
                                family: 'Quicksand'
                            },
                            color: '#000',
                            padding: 15
                        }
                    }
                }
            },
            plugins: [{
                id: 'badgesPlugin',
                afterDraw: function(chart) {
                    const ctx = chart.ctx;
                    const meta = chart.getDatasetMeta(0);
                    const scale = chart.scales.r;
                    const centerX = chart.chartArea.left + chart.chartArea.width / 2;
                    const centerY = chart.chartArea.top + chart.chartArea.height / 2;
                    
                    labelsRadar.forEach((tag, index) => {
                        const value = dataRadar[index];
                        const ponto = pontuacoesOrdenadas[index];
                        const maxGrafico = ponto?.maximo_grafico || maximoGrafico;
                        const color = coresPorTag[tag] || '#808080';
                        
                        if (meta.data[index]) {
                            const point = meta.data[index];
                            const angle = point.angle;
                            const distance = scale.getDistanceFromCenterForValue(value);
                            
                            // Calcular posición del badge (fuera del punto, en la dirección del eje)
                            const badgeDistance = distance + 25;
                            const x = Math.cos(angle) * badgeDistance;
                            const y = Math.sin(angle) * badgeDistance;
                            
                            // Dibujar badge circular
                            ctx.save();
                            ctx.translate(centerX + x, centerY + y);
                            
                            // Círculo de fondo
                            ctx.beginPath();
                            ctx.arc(0, 0, 18, 0, Math.PI * 2);
                            ctx.fillStyle = color;
                            ctx.fill();
                            
                            // Texto del valor
                            ctx.fillStyle = '#FFFFFF';
                            ctx.font = 'bold 12px Quicksand';
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'middle';
                            ctx.fillText(value.toString(), 0, 0);
                            
                            ctx.restore();
                        }
                    });
                }
            }]
        });
    }
</script>

