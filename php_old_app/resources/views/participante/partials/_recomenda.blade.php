<div class="card mt-4">
    <div class="card-header border-0">
        <h2 class="card-title">
            <label class="badge badge-dark"> Recomendações Personalizadas </label> | Por Dimensão
        </h2>
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
                    <label class="badge badge-secondary">{{ $ponto['tag'] }}</label>
                    {{ $ponto['nome'] }}  <label class="badge badge-{{ $ponto['badge'] }}">{{ $ponto['faixa'] }}</label>
                </h4>
                <p>
                    Sua pontuação foi de <strong>{{ $ponto['valor'] }}</strong> pontos. <br>Sua faixa está <strong>{{ $ponto['faixa'] }}</strong>.
                </p>
                <p style="text-align: justify;">{{ $ponto['recomendacao'] }}</p>
            </div>
        @endforeach
    </div>
</div>
