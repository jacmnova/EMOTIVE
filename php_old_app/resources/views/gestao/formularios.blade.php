@extends('adminlte::page')

@section('title', 'Lista de Formulários')

@section('content_header')
    @if(Session::has('msgSuccess'))
        <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span>&times;</span></button>
            <i class="fa-regular fa-bell mr-1"></i> {!! Session::get('msgSuccess') !!}
        </div>
    @elseif(Session::has('msgError'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span>&times;</span></button>
            <i class="fa-solid fa-triangle-exclamation"></i> {!! Session::get('msgError') !!}
        </div>
    @endif

    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Início</a></li>
                    <li class="breadcrumb-item active">Formulários</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fa-solid fa-file-alt mr-2"></i> Lista de Formulários
        </h3>
        <div class="card-tools d-flex align-items-center">
            <a href="#" class="btn btn-sm btn-tool d-sm-inline-block" title="Mais Informações">
                <i class="fas fa-bars"></i>
            </a>
        </div>
    </div>

    @if($formularios->count() > 0)

        {{-- VISUALIZAÇÃO PARA DESKTOP --}}
        <div class="card-body d-none d-md-block">
            <table class="table datatable table-striped dtr-inline">
                <thead>
                    <tr>
                        <th style="width: 30%">Nome</th>
                        <th style="width: 70%">Descrição</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($formularios as $formulario)
                        <tr>
                            <td>
                                <a href="{{ route('formularios.show', $formulario->formulario->id) }}" class="btn btn-sm btn-tool" title="Ver Detalhes">
                                    <i class="fa-solid fa-eye text-success"></i>
                                </a> 
                                <label class="badge badge-dark">{{ $formulario->formulario->label }}</label> |
                                {{ $formulario->formulario->nome }}
                                <label class="badge badge-secondary">{{ $formulario->quantidade }}</label>
                            </td>
                            <td>{!! $formulario->formulario->descricao !!}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- VISUALIZAÇÃO PARA CELULAR --}}
        <div class="card-body d-block d-md-none">
            <div class="row">
                @foreach($formularios as $formulario)
                    <div class="col-12 mb-3">
                        <div class="card shadow-sm p-3">
                            <h5 class="mb-2">
                                <label class="badge badge-dark">{{ $formulario->formulario->label }}</label> |
                                {{ $formulario->formulario->nome }}
                            </h5>
                            <p class="mb-2">{!! $formulario->formulario->descricao !!}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge badge-secondary">Qtd: {{ $formulario->quantidade }}</span>
                                <a href="{{ route('formularios.show', $formulario->formulario->id) }}" class="btn btn-sm btn-tool" title="Ver Detalhes">
                                    <i class="fa-solid fa-eye text-success"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    @else
        <div class="row" style="margin: 20px;">
            <div class="callout callout-warning w-100">
                <h5><i class="fa-solid fa-circle-info"></i> Nenhum Formulário foi encontrado.</h5>
                <p>Entre em contato com o <strong>Administrador</strong> e peça liberação de um novo formulário.</p>
            </div>
        </div>
    @endif
</div>
@stop
