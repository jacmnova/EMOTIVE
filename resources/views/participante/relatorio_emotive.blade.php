@extends('adminlte::page')

@section('title', 'Relatório E.MO.TI.VE')

@section('content_header')
    @if(Session::has('msgSuccess'))
        <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <i class="fa-regular fa-bell" style="margin-right: 5px"></i> {!! Session::get('msgSuccess') !!}
        </div>
    @elseif(Session::has('msgError'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <i class="fa-solid fa-triangle-exclamation"></i> {!! Session::get('msgError') !!}
        </div>
    @endif

    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Início</a></li>
                    <li class="breadcrumb-item active">Relatório</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
<div class="relatorio-emotive">
    @include('participante.emotive.partials._capa')
    @include('participante.emotive.partials._introducao')
    @include('participante.emotive.partials._estrutura_modelo')
    @include('participante.emotive.partials._resultado_emotive')
    @include('participante.emotive.partials._estado_emocional')
    @include('participante.emotive.partials._eixos_analiticos')
    @include('participante.emotive.partials._risco_descarrilamento')
    @include('participante.emotive.partials._saude_emocional')
    @include('participante.emotive.partials._plano_desenvolvimento')
    @include('participante.emotive.partials._conclusao')
</div>
@stop

@section('css')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
<style>
    @media print {
        @page {
            size: A4 portrait;
            margin: 0;
        }
        
        @page :not(:first) {
            size: A4 portrait;
            margin: 100px 80px;
        }
        
        body {
            width: 595.28pt; /* Ancho exacto de A4 en portrait */
            max-width: 595.28pt;
        }
    }
    
    .relatorio-emotive {
        font-family: 'Quicksand', sans-serif;
        background: white;
        color: #333;
        max-width: 595.28pt; /* Ancho exacto de A4 en portrait */
        width: 100%;
        margin: 0 auto;
        box-sizing: border-box;
    }
    
    .relatorio-emotive,
    .relatorio-emotive *,
    .relatorio-emotive h1,
    .relatorio-emotive h2,
    .relatorio-emotive h3,
    .relatorio-emotive h4,
    .relatorio-emotive p,
    .relatorio-emotive span,
    .relatorio-emotive div,
    .relatorio-emotive li,
    .relatorio-emotive td,
    .relatorio-emotive th {
        font-family: 'Quicksand', sans-serif !important;
    }
    
    .page-break {
        page-break-after: always;
        margin-bottom: 50px;
        padding-bottom: 50px;
        border-bottom: 2px dashed #ddd;
        max-width: 595.28pt; /* Ancho exacto de A4 en portrait */
        width: 100%;
        box-sizing: border-box;
        font-family: 'Quicksand', sans-serif !important;
    }
    
    .section-title {
        color: #A4977F;
        font-size: 24px;
        font-style: normal;
        font-weight: 700;
        line-height: normal;
        font-family: 'Quicksand', sans-serif !important;
    }
    
    .section-subtitle {
        color: #2E9196;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        font-family: 'Quicksand', sans-serif !important;
    }
    
    .highlight-box {
        background-color: #e8f4f8;
        border-left: 4px solid #008ca5;
        padding: 15px;
        margin: 20px 0;
        border-radius: 4px;
        font-family: 'Quicksand', sans-serif !important;
    }
    
    .quote-box {
        background-color: #e8f4f8;
        border-radius: 8px;
        padding: 20px;
        margin: 20px 0;
        font-style: italic;
        font-family: 'Quicksand', sans-serif !important;
    }
    
    .faixa-badge {
        display: inline-block;
        padding: 5px 15px;
        border-radius: 20px;
        font-weight: bold;
        font-size: 0.9rem;
        margin: 5px;
        font-family: 'Quicksand', sans-serif !important;
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
</style>
@stop

@section('js')
<script src="{{ asset('../js/utils.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@include('participante.emotive.partials._scripts')
@stop

