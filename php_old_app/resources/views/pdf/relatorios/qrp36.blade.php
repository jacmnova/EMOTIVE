<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>{{ $formulario->label }} | {{ $formulario->nome }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }

        @page :not(:first) {
            size: A4 portrait;
            margin: 100px 80px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111;
            width: 100%;
            max-width: 100%;
        }

        .conteudo {
            padding: 20px;
            max-width: 595.28pt; /* Ancho exacto de A4 en portrait */
            width: 100%;
            margin: 0 auto;
            box-sizing: border-box;
        }

        .page-break {
            page-break-after: always;
        }

        .capa-imagem {
            width: 100%;
            height: 842pt; /* Altura exacta de A4 en portrait */
            max-height: 842pt;
            position: relative;
            box-sizing: border-box;
        }

        .capa-imagem img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        h1, h2, h3, h4 {
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

        .grafico {
            text-align: center;
            margin: 30px 0;
            padding: 10px;
            border: 1px dashed #ccc;
            border-radius: 6px;
            background-color: #f9f9f9;
        }

        .grafico h4 {
            font-size: 14px;
            margin-bottom: 10px;
            color: #333;
        }

        .grafico img {
            max-width: 90%;
            height: auto;
            margin: 0 auto;
            display: block;
        }
    </style>
</head>
<body>

{{-- Primeira página: capa sem margem/padding --}}
<div class="capa-imagem">
    <img src="{{ public_path('img/capa-fundo.png') }}" alt="Capa">
</div>

<div class="page-break"></div>

{{-- Conteúdo com margens e paddings padrão --}}
<div class="conteudo">

<h1>{{ $formulario->label }}</h1>
<h2>{{ $formulario->nome }}</h2>
<p><strong>Descrição:</strong> {!! $formulario->descricao !!}</p>

<hr>

<div class="section">
    <h2>Sumário do Relatório</h2>

    <p><strong>Formulário:</strong> {{ $formulario->label }} — {{ $formulario->nome }}</p>
    <p><strong>Participante:</strong> {{ $user->name }} ({{ $user->email }})</p>
    <p><strong>Data:</strong> {{ $hoje }}</p>

    <p><strong>Respostas registradas:</strong> {{ $respostasUsuario->count() }} de {{ $formulario->perguntas->count() }}</p>

    <p><strong>Dimensões avaliadas:</strong> {{ $variaveis->pluck('nome')->join(', ') }}.</p>

    <p><strong>Conteúdo do Relatório:</strong></p>
    <ul>
        <li>Pontuação por Dimensão</li>
        <li>Gráficos Visuais</li>
        <li>Classificação por Variável</li>
        <li>Análise Interpretativa</li>
        <li>Recomendações Personalizadas</li>
        <li>Resumo por Faixa de Pontuação</li>
    </ul>
</div>

<hr>

<div class="section">
    <h3>Resumo por Faixa de Pontuação</h3>

    @php
        $grupoAlta = [];
        $grupoModerada = [];
        $grupoBaixa = [];

        foreach ($variaveis as $registro) {
            foreach ($pontuacoes as $pontos) {
                if (mb_strtoupper($registro->tag, 'UTF-8') === $pontos['tag']) {
                    $pontuacao = $pontos['pontuacao'];
                    if ($pontuacao <= $registro->B) {
                        $grupoBaixa[] = $registro->nome . ' (' . $registro->tag . ')';
                    } elseif ($pontuacao <= $registro->M) {
                        $grupoModerada[] = $registro->nome . ' (' . $registro->tag . ')';
                    } else {
                        $grupoAlta[] = $registro->nome . ' (' . $registro->tag . ')';
                    }
                    break;
                }
            }
        }
    @endphp

    @if(count($grupoAlta))
        <div style="background-color:#fdecea; border-left: 4px solid #d93025; padding: 10px; margin-bottom: 10px;">
            <strong>Faixa Alta</strong>
            <ul>
                @foreach($grupoAlta as $dim)
                    <li>{{ $dim }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(count($grupoModerada))
        <div style="background-color:#fff9e5; border-left: 4px solid #f4b400; padding: 10px; margin-bottom: 10px;">
            <strong>Faixa Moderada</strong>
            <ul>
                @foreach($grupoModerada as $dim)
                    <li>{{ $dim }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(count($grupoBaixa))
        <div style="background-color:#e8f1fa; border-left: 4px solid #1a73e8; padding: 10px; margin-bottom: 10px;">
            <strong>Faixa Baixa</strong>
            <ul>
                @foreach($grupoBaixa as $dim)
                    <li>{{ $dim }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>


<hr>

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

    <div class="grafico">
        <h4>Visualização Gráfica — Barras</h4>
        <img src="{{ public_path($imagemGrafico) }}" alt="Gráfico de Barras">
    </div>

    <div class="grafico">
        <h4>Visualização Gráfica — Radar</h4>
        <img src="{{ public_path($imagemRadar) }}" alt="Gráfico de Radar">
    </div>
</div>

<div class="section">
    <h3>Recomendações Personalizadas</h3>
    @foreach ($variaveis as $registro)
        @php
            $pontuacao = null;
            $faixa = null;
            $recomendacao = 'Sem recomendação disponível.';

            foreach ($pontuacoes as $pontos) {
                if (mb_strtoupper($registro->tag, 'UTF-8') === $pontos['tag']) {
                    $pontuacao = $pontos['pontuacao'];

                    if ($pontuacao <= $registro->B) {
                        $faixa = 'Baixa';
                        $recomendacao = $registro->r_baixa;
                    } elseif ($pontuacao <= $registro->M) {
                        $faixa = 'Moderada';
                        $recomendacao = $registro->r_moderada;
                    } else {
                        $faixa = 'Alta';
                        $recomendacao = $registro->r_alta;
                    }
                    break;
                }
            }
        @endphp

        <div style="margin-bottom: 20px;">
            <strong>{{ $registro->nome }} ({{ mb_strtoupper($registro->tag, 'UTF-8') }})</strong><br>
            <em>Faixa: {{ $faixa ?? '–' }}</em>
            <p style="margin-top: 5px;">{!! nl2br(e($recomendacao)) !!}</p>
        </div>
    @endforeach
</div>

<div class="section">
    <h3>Análise Interpretativa por Dimensão</h3>
    <p style="white-space: pre-wrap;">
        {{ $analiseTexto }}
    </p>
</div>

@if (isset($pdf))
    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->get_font("DejaVu Sans", "normal");
            $size = 9;
            $text = "Página {PAGE_NUM} de {PAGE_COUNT} — {{ $user->name }} — {{ $hoje }}";
            $width = $fontMetrics->get_text_width($text, $font, $size);
            $x = (595.28 - $width) / 2;
            $y = 820;
            $pdf->text($x, $y, $text, $font, $size);
        }
    </script>
@endif

</div> {{-- fim .conteudo --}}

</body>
</html>
