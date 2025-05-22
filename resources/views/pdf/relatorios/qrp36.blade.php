<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>{{ $formulario->label }} | {{ $formulario->nome }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111;
            padding: 20px;
        }
        h1, h2, h3 {
            text-align: center;
            margin-bottom: 10px;
        }
        .titulo {
            font-size: 16px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11px;
        }
        th, td {
            border: 1px solid #333;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .section {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>

<h1>{{ $formulario->label }}</h1>
<h2>{{ $formulario->nome }}</h2>

<p><strong>Descrição:</strong> {!! $formulario->descricao !!}</p>
<p><strong>Nome:</strong> {{ $user->name }}</p>
<p><strong>E-mail:</strong> {{ $user->email }}</p>
<p><strong>Data:</strong> {{ $hoje }}</p>

<hr>

<div class="section">
    <h3>Instruções</h3>
    <p>{!! $formulario->instrucoes !!}</p>
</div>

<div class="section">
    <h3>Respostas ao Questionário</h3>
    <table>
        <thead>
            <tr>
                <th>Pergunta</th>
                <th>Variável</th>
                <th>Resposta</th>
            </tr>
        </thead>
        <tbody>
            @foreach($formulario->perguntas as $pergunta)
                @php
                    $resposta = $respostasUsuario->get($pergunta->id);
                @endphp
                <tr>
                    <td>{{ $pergunta->numero_da_pergunta }} - {{ $pergunta->pergunta }}</td>
                    <td>
                        @if($pergunta->variaveis->isNotEmpty())
                            {{ $pergunta->variaveis->pluck('nome')->join(', ') }}
                        @else
                            Nenhuma
                        @endif
                    </td>
                    <td>{{ $resposta->valor_resposta ?? 'Sem resposta' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="section">
    <h3>Pontuação por Dimensão</h3>
    <table>
        <thead>
            <tr>
                <th>Tag</th>
                <th>Dimensão</th>
                <th>Pontos</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pontuacoes as $ponto)
                <tr>
                    <td>{{ $ponto['tag'] }}</td>
                    <td>{{ $ponto['nome'] }}</td>
                    <td>{{ $ponto['pontuacao'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="section">
    <h3>Classificação por Variável</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 20%">Variável</th>
                <th style="width: 10%">Tag</th>
                <th style="width: 10%">Pontuação</th>
                <th style="width: 60%">Classificação</th>
            </tr>
        </thead>
        <tbody>
            @foreach($variaveis as $registro)
                @php
                    $pontuacao = null;
                    $classificacao = 'Sem dados';

                    foreach($pontuacoes as $pontos) {
                        if (mb_strtoupper($registro->tag, 'UTF-8') === $pontos['tag']) {
                            $pontuacao = $pontos['pontuacao'];
                            if ($pontuacao <= $registro->B) {
                                $classificacao = $registro->baixa;
                            } elseif ($pontuacao <= $registro->M) {
                                $classificacao = $registro->moderada;
                            } else {
                                $classificacao = $registro->alta;
                            }
                            break;
                        }
                    }
                @endphp
                <tr>
                    <td>{{ $registro->nome }}</td>
                    <td>{{ mb_strtoupper($registro->tag, 'UTF-8') }}</td>
                    <td>{{ $pontuacao ?? '–' }}</td>
                    <td>{{ $classificacao }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="section">
    <h3>Análise Interpretativa por Dimensão</h3>

</div>

</body>
</html>
