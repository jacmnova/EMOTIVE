<div class="card mt-4">
    <div class="card-header border-0">
        <h2 class="card-title">
            <label class="badge badge-dark"> Recomendações Personalizadas </label> | Por Dimensão
        </h2>
        <div class="card-tools">
            <a href="#" class="btn btn-tool btn-sm"><i class="fas fa-download"></i></a>
            <a href="#" class="btn btn-tool btn-sm"><i class="fas fa-bars"></i></a>
            <a href="#" class="btn btn-sm btn-tool"><i class="fa-solid fa-circle-info"></i></a>
        </div>
    </div>

    <div class="card-body">
        <div class="card mb-2 pl-4 pr-4 border-0" style="box-shadow: none;">
            <div class="card mb-2 pl-4 pr-4 border-0 text-center" style="box-shadow: none;">
                <h1>{{ $user->name }}</h1>
                <h4>{{ $user->email }}</h4>
            </div>
        </div>

        @foreach($pontuacoes as $ponto)
            <div class="mb-4 p-3 border rounded">
                <h4>
                    <label class="badge badge-{{ $ponto['badge'] }}">{{ $ponto['tag'] }}</label>
                    {{ $ponto['nome'] }}
                </h4>
                <p>
                    Sua pontuação foi de {{ $ponto['valor'] }} pontos, Sua faixa está {{ $ponto['faixa'] }}.
                </p>
                <p>{{ $ponto['recomendacao'] }}</p>
            </div>
        @endforeach
    </div>
</div>
