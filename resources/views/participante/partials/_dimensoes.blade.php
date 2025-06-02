<div class="card">
    <div class="card-header border-0">
        <h2 class="card-title"><label class="badge badge-dark"> Pontuação</label> | Dimensão</h2>
        <div class="card-tools">
            <a href="#" class="btn btn-tool btn-sm">
                <i class="fas fa-download"></i>
            </a>
            <a href="#" class="btn btn-tool btn-sm">
                <i class="fas fa-bars"></i>
            </a>
            <a href="#" class="btn btn-sm btn-tool">
                <i class="fa-solid fa-circle-info"></i>
            </a>
        </div>
    </div>

    <div class="card-body">
        <table class="table table-striped">
                <tr>
                </tr>
            <tbody>
                @foreach($variaveis as $registro)
                    @php
                        $pontuacao = null;
                        foreach($pontuacoes as $pontos) {
                            if (mb_strtoupper($registro->tag, 'UTF-8') === $pontos['tag']) {
                                $pontuacao = $pontos['valor'];
                                break;
                            }
                        }

                        if ($pontuacao !== null) {
                            if ($pontuacao <= $registro->B) {
                                $tipo = 'Baixa';
                                $classificacao = $registro->baixa;
                                $badge = 'success';
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
                            $classificacao = 'Sem dados';
                            $badge = 'secondary';
                        }
                    @endphp
                    <tr>
                        <td style="width: 20%"><label class="badge badge-secondary">{{ mb_strtoupper($registro->tag, 'UTF-8') }}</label> | {{ $registro->nome }}</td>
                        <td style="width: 5%; text-align: center;"><label class="badge badge-dark">{{ $pontuacao ?? '–' }} pontos </label></td>
                        <td>
                            @if($tipo == 'Baixa')
                                <label class="badge badge-info"> Faixa {{ $tipo }} </label>
                            @elseif($tipo == 'Moderada')
                                <label class="badge badge-warning"> Faixa {{ $tipo }} </label>
                            @else
                                <label class="badge badge-danger"> Faixa {{ $tipo }} </label>
                            @endif
                    <br> {{ $classificacao }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer text-right">
        
    </div>
</div>



<div class="card mt-4">
    <div class="card-header border-0">
        <h2 class="card-title">
            <label class="badge badge-dark"> Recomendações Personalizadas </label> | Por Dimensão
        </h2>
        <div class="card-tools">
            <a href="#" class="btn btn-tool btn-sm">
                <i class="fas fa-download"></i>
            </a>
            <a href="#" class="btn btn-tool btn-sm">
                <i class="fas fa-bars"></i>
            </a>
            <a href="#" class="btn btn-sm btn-tool">
                <i class="fa-solid fa-circle-info"></i>
            </a>
        </div>
    </div>

    <div class="card-body">
        @foreach($variaveis as $registro)
            @php
                $pontuacao = null;
                foreach($pontuacoes as $pontos) {
                    if (mb_strtoupper($registro->tag, 'UTF-8') === $pontos['tag']) {
                        $pontuacao = $pontos['valor'];
                        break;
                    }
                }

                if ($pontuacao !== null) {
                    if ($pontuacao <= $registro->B) {
                        $tipo = 'Baixa';
                        $recomendacao = $registro->r_baixa;
                        $badge = 'info';
                    } elseif ($pontuacao <= $registro->M) {
                        $tipo = 'Moderada';
                        $recomendacao = $registro->r_moderada;
                        $badge = 'warning';
                    } else {
                        $tipo = 'Alta';
                        $recomendacao = $registro->r_alta;
                        $badge = 'danger';
                    }
                } else {
                    $tipo = 'Sem Dados';
                    $recomendacao = 'Sem dados disponíveis para esta dimensão.';
                    $badge = 'secondary';
                }
            @endphp

            <div class="mb-4 p-3 border rounded">
                <h4>
                    <label class="badge badge-secondary">{{ mb_strtoupper($registro->tag, 'UTF-8') }}</label>
                    {{ $registro->nome }}
                </h4>
                <p>
                    Sua pontuação foi de {{ $pontuacao ?? '–' }} pontos, Sua faixa está  
                    @if($tipo == 'Baixa')
                        Baixa
                    @elseif($tipo == 'Moderada')
                        Moderada
                    @elseif($tipo == 'Alta')
                        Alta
                    @endif
                </p>
                <p>
                    {{ $recomendacao }}
                </p>
            </div>
        @endforeach
    </div>
</div>
