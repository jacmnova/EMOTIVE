<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>RELATÓRIO E.MO.TI.VE® | {{ $user->name }}</title>
</head>
<body>

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

</body>
</html>
