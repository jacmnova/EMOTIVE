<div class="row">
    @foreach($variaveis as $var)

        @php
            $tag = strtoupper($var->tag);
            $resposta = 0;
            foreach($pontuacoes as $pontos) {
                if($pontos['tag'] === $tag) {
                    $resposta = $pontos['valor'];
                    break;
                }
            }

            $b = (int) $var->B;
            $m = (int) $var->M;
            $a = (int) $var->A;

            $faixaBaixa = $b;
            $faixaModerada = $m - $b;
            $faixaAlta = $a - $m;

            $max = max($a + 5, $resposta + 5);
            $id = 'grafico_faixa_' . $tag;
        @endphp

        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <strong>{{ $tag }} - {{ $var->nome }}</strong>
                </div>
                <div class="card-body">
                    <canvas id="{{ $id }}" height="250"></canvas>
                    <div class="mt-3 text-center"></div>
                </div>
            </div>
        </div>

        @push('js')
        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@1.4.0"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const ctx = document.getElementById('{{ $id }}').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Faixas', 'Usuário'],
                        datasets: [
                            {
                                label: 'Baixa',
                                data: [{{ $b }}, 0],
                                backgroundColor: '#17a2b8',
                                stack: 'faixas'
                            },
                            {
                                label: 'Moderada',
                                data: [{{ $m - $b }}, 0],
                                backgroundColor: '#ffc107',
                                stack: 'faixas'
                            },
                            {
                                label: 'Alta',
                                data: [{{ $a - $m }}, 0],
                                backgroundColor: '#dc3545',
                                stack: 'faixas'
                            },
                            {
                                label: 'Pontuação',
                                data: [0, {{ $resposta }}],
                                backgroundColor: '#343a40',
                                stack: 'usuario'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            annotation: {
                                annotations: {
                                    linhaPontuacao: {
                                        type: 'line',
                                        yMin: {{ $resposta }},
                                        yMax: {{ $resposta }},
                                        borderColor: 'red',
                                        borderWidth: 2,
                                        borderDash: [6, 6],
                                        label: {
                                            content: 'Pontuação',
                                            enabled: true,
                                            position: 'start',
                                            backgroundColor: 'red',
                                            color: '#fff',
                                            font: {
                                                size: 10,
                                                style: 'normal'
                                            }
                                        }
                                    }
                                }
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                callbacks: {
                                    label: function(context) {
                                        const label = context.dataset.label;
                                        if (label === 'Baixa') {
                                            return 'Baixa: 0 – {{ $b }}';
                                        }
                                        if (label === 'Moderada') {
                                            return 'Moderada: {{ $b + 1 }} – {{ $m }}';
                                        }
                                        if (label === 'Alta') {
                                            return 'Alta: {{ $m + 1 }} – {{ $a }}';
                                        }
                                        if (label === 'Pontuação') {
                                            return 'Pontuação do Usuário: {{ $resposta }}';
                                        }
                                        return `${label}: ${context.raw}`;
                                    }
                                }
                            },
                            legend: {
                                position: 'top',
                                labels: {
                                    filter: function(item) {
                                        return item.text !== 'Pontuação';
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                stacked: true
                            },
                            y: {
                                beginAtZero: true,
                                stacked: true,
                                max: {{ $max }}
                            }
                        }
                    }
                });
            });
        </script>
        @endpush

    @endforeach
</div>
