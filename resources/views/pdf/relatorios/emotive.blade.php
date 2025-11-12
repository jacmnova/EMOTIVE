<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>RELATÓRIO E.MO.TI.VE® | {{ $user->name }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }
        
        @page :first {
            margin: 0;
        }
        
        @page :not(:first) {
            margin: 0;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif; /* Fallback para DomPDF */
            margin: 0;
            padding: 0;
            font-size: 10pt;
            color: #333;
            line-height: 1.4;
            background: white;
        }
        
        * {
            box-sizing: border-box;
        }
        
        .relatorio-emotive {
            font-family: 'DejaVu Sans', sans-serif;
            background: white;
            color: #333;
            max-width: 595.28pt;
            width: 100%;
            margin: 0 auto;
            box-sizing: border-box;
        }
        
        /* Estilos iguales a la web */
        .section-title {
            color: #A4977F;
            font-size: 18pt;
            font-style: normal;
            font-weight: 700;
            line-height: normal;
        }
        
        .section-subtitle {
            color: #2E9196;
            font-size: 12pt;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }
        
        .highlight-box {
            background-color: #e8f4f8;
            border-left: 4px solid #008ca5;
            padding: 12pt;
            margin: 15pt 0;
            border-radius: 4px;
        }
        
        .quote-box {
            background-color: #e8f4f8;
            border-radius: 8px;
            padding: 15pt;
            margin: 15pt 0;
            font-style: italic;
        }
        
        .faixa-badge {
            display: inline-block;
            padding: 4pt 12pt;
            border-radius: 20px;
            font-weight: bold;
            font-size: 9pt;
            margin: 4pt;
        }
        
        .faixa-baixa {
            background-color: #d4edda;
            color: #155724;
        }
        
        .faixa-moderada {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .faixa-alta {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        /* Permitir que el contenido fluya naturalmente - solo evitar cortes en elementos críticos */
        img, table {
            page-break-inside: avoid;
            page-break-after: avoid;
            max-width: 100%;
            height: auto;
            display: block;
        }
        
        /* Evitar cortes en títulos */
        h1, h2, h3 {
            page-break-after: avoid;
            page-break-inside: avoid;
        }
        
        /* Asegurar que los contenedores principales no se corten innecesariamente */
        .relatorio-emotive > div {
            page-break-inside: auto;
        }
        
        /* Evitar cortes en secciones de gráficos */
        .graficos-container {
            page-break-inside: avoid;
        }
        
        /* Evitar cortes en secciones completas */
        .section-container {
            page-break-inside: avoid;
        }
        
        /* Mejorar el espaciado entre secciones */
        .relatorio-emotive > div + div {
            margin-top: 0;
        }
    </style>
</head>
<body style="margin: 0; padding: 0; font-family: 'DejaVu Sans', sans-serif; font-size: 10pt; color: #333; background: white;">
<div class="relatorio-emotive" style="width: 100%; max-width: 595.28pt; margin: 0 auto; font-family: 'DejaVu Sans', sans-serif;">
    {{-- CAPA --}}
    @include('participante.emotive.partials._capa')

    {{-- INTRODUÇÃO --}}
    @include('participante.emotive.partials._introducao')

    {{-- ESTRUTURA DO MODELO --}}
    @include('participante.emotive.partials._estrutura_modelo')

    {{-- RESULTADO E.MO.TI.VE --}}
    @include('participante.emotive.partials._resultado_emotive')

    {{-- ESTADO EMOCIONAL E PSICOSSOCIAL --}}
    @include('participante.emotive.partials._estado_emocional')

    {{-- EIXOS ANALÍTICOS --}}
    @include('participante.emotive.partials._eixos_analiticos')

    {{-- RISCO DE DESCARRILAMENTO --}}
    @include('participante.emotive.partials._risco_descarrilamento')

    {{-- SAÚDE EMOCIONAL --}}
    @include('participante.emotive.partials._saude_emocional')

    {{-- PLANO DE DESENVOLVIMENTO --}}
    @include('participante.emotive.partials._plano_desenvolvimento')

    {{-- CONCLUSÃO --}}
    @include('participante.emotive.partials._conclusao')
</div>
</body>
</html>
