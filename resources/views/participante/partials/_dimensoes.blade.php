<div class="card">
    <div class="card-header border-0 d-flex justify-content-between align-items-center flex-wrap">
        <h2 class="card-title mb-2 mb-sm-0">
            <label class="badge badge-dark">Pontuação</label> | Dimensão
        </h2>
    </div>

    <div class="card-body p-2">

        {{-- TABELA PARA DESKTOP --}}
        <div class="table-responsive d-none d-md-block">
            <table class="table table-striped table-bordered">
                <tbody>
                    @foreach($variaveis as $registro)
                        @php
                            $pontuacao = null;
                            $pontuacaoNormalizada = null;
                            foreach($pontuacoes as $pontos) {
                                if (mb_strtoupper($registro->tag, 'UTF-8') === $pontos['tag']) {
                                    $pontuacao = $pontos['valor'];
                                    $pontuacaoNormalizada = $pontos['normalizada'] ?? null;
                                    break;
                                }
                            }

                            if ($pontuacao !== null) {
                                if ($pontuacao <= $registro->B) {
                                    $tipo = 'Baixa';
                                    $classificacao = $registro->baixa;
                                    $badge = 'info';
                                } elseif ($pontuacao <= $registro->M) {
                                    $tipo = 'Moderada';
                                    $classificacao = $registro->moderada;
                                    $badge = 'warning';
                                } else {
                                    $tipo = 'Alta';
                                    $classificacao = $registro->alta;
                                    $badge = 'danger';
                                }
                            } else {
                                $tipo = null;
                                $classificacao = 'Sem dados';
                                $badge = 'secondary';
                            }
                        @endphp

                        <tr>
                            <td style="min-width: 200px">
                                <label class="badge badge-secondary">{{ mb_strtoupper($registro->tag, 'UTF-8') }}</label>
                                <div class="small">{{ $registro->nome }}</div>
                            </td>
                            <td style="min-width: 100px; text-align: center;">
                                <label class="badge badge-dark">{{ $pontuacao ?? '–' }} pts</label>
                                @if($pontuacaoNormalizada)
                                    <br><small class="text-muted">({{ round($pontuacaoNormalizada) }}/100)</small>
                                @endif
                            </td>
                            <td>
                                @if($tipo)
                                    <label class="badge badge-{{ $badge }}">Faixa {{ $tipo }}</label>
                                @else
                                    <label class="badge badge-secondary">Indefinida</label>
                                @endif
                                <br>
                                <span style="text-align: justify; display: block;">{{ $classificacao }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- CARDS VERTICAIS PARA CELULAR --}}
        <div class="d-block d-md-none">
            @foreach($variaveis as $registro)
                @php
                    $pontuacao = null;
                    $pontuacaoNormalizada = null;
                    foreach($pontuacoes as $pontos) {
                        if (mb_strtoupper($registro->tag, 'UTF-8') === $pontos['tag']) {
                            $pontuacao = $pontos['valor'];
                            $pontuacaoNormalizada = $pontos['normalizada'] ?? null;
                            break;
                        }
                    }

                    if ($pontuacao !== null) {
                        if ($pontuacao <= $registro->B) {
                            $tipo = 'Baixa';
                            $classificacao = $registro->baixa;
                            $badge = 'info';
                        } elseif ($pontuacao <= $registro->M) {
                            $tipo = 'Moderada';
                            $classificacao = $registro->moderada;
                            $badge = 'warning';
                        } else {
                            $tipo = 'Alta';
                            $classificacao = $registro->alta;
                            $badge = 'danger';
                        }
                    } else {
                        $tipo = null;
                        $classificacao = 'Sem dados';
                        $badge = 'secondary';
                    }
                @endphp

                <div class="mb-4 p-3 border rounded shadow-sm bg-light">
                    <h5>
                        <label class="badge badge-secondary">{{ mb_strtoupper($registro->tag, 'UTF-8') }}</label>
                        {{ $registro->nome }}
                    </h5>
                    <p class="mb-1">
                        <label class="badge badge-dark">{{ $pontuacao ?? '–' }} pts</label>
                        @if($pontuacaoNormalizada)
                            <br><small class="text-muted">({{ round($pontuacaoNormalizada) }}/100)</small>
                        @endif
                    </p>
                    <p class="mb-1">
                        @if($tipo)
                            <label class="badge badge-{{ $badge }}">Faixa {{ $tipo }}</label>
                        @else
                            <label class="badge badge-secondary">Indefinida</label>
                        @endif
                    </p>
                    <p style="text-align: justify;">{{ $classificacao }}</p>
                </div>
            @endforeach
        </div>

    </div>
</div>
