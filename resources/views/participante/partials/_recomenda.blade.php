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

        <div class="card mb-2 pl-4 pr-4 border-0" style="box-shadow: none;">
            <div class="card mb-2 pl-4 pr-4 border-0 text-center" style="box-shadow: none;">
                <h1>{{ $user->name }}</h1>
                <h4>{{ $user->email }}</h4>
            </div>
        </div>

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
