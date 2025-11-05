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
<style>
    .relatorio-emotive {
        font-family: 'Montserrat', 'DejaVu Sans', sans-serif;
        background: white;
        color: #333;
    }
    
    .page-break {
        page-break-after: always;
        margin-bottom: 50px;
        padding-bottom: 50px;
        border-bottom: 2px dashed #ddd;
    }
    
    .section-title {
        color: #008ca5;
        font-weight: bold;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .section-subtitle {
        color: #00a8b5;
        font-weight: 600;
        font-size: 1.2rem;
        margin-bottom: 0.8rem;
    }
    
    .highlight-box {
        background-color: #e8f4f8;
        border-left: 4px solid #008ca5;
        padding: 15px;
        margin: 20px 0;
        border-radius: 4px;
    }
    
    .quote-box {
        background-color: #e8f4f8;
        border-radius: 8px;
        padding: 20px;
        margin: 20px 0;
        font-style: italic;
    }
    
    .faixa-badge {
        display: inline-block;
        padding: 5px 15px;
        border-radius: 20px;
        font-weight: bold;
        font-size: 0.9rem;
        margin: 5px;
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

