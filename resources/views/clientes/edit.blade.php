@extends('adminlte::page')

@section('title', 'Incluir Cliente')

@section('css')

    <style>

        .select2-selection--multiple {
            background-color: #f2f2f2 !important;
            border: 1px solid #ccc !important;
            color: #555 !important;
        }

        .select2-selection__choice {
            background-color: #008ca5 !important;
            border: 1px solid #bcbabd !important;
        }

        .select2-selection__choice__remove {
            color: #bab5ec !important;
        }

        .select2-search__field {
            color: #555 !important;
            background-color: #f2f2f2 !important;
        }

    </style>

{{-- CSS Global --}}
<link href="{{ asset('css/utils.css') }}" rel="stylesheet">

@stop

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/home">Início</a></li>
                    <li class="breadcrumb-item active">Clientes</li>
                    <li class="breadcrumb-item active">Editar</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fa-solid fa-building-user" style="margin-right: 5px;"></i> Editar Cliente</h3>
                <div class="card-tools">
                    <a href="#" class="btn btn-sm btn-tool d-sm-inline-block" title="Audio">
                        <i class="fa-solid fa-circle-play"></i>
                    </a>
                </div>
            </div>

            <form action="{{ route('clientes.update', $clientes->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-body">

                    <div class="form-group" hidden>
                        <label for="id">Id:</label>
                        <input type="text" name="id" id="id" class="form-control" value="{{ $clientes->id }}" readonly required>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-3">
                            <label for="tipo">Tipo:</label>
                            <input type="text" id="tipo" class="form-control" value="@if($clientes->tipo == 'cpf') CPF @elseif($clientes->tipo == 'cnpj') CNPJ @else Internacional @endif" readonly>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="cpf_cnpj">cpf_cnpj:</label>
                            <input type="text" name="cpf_cnpj" id="cpf_cnpj" class="form-control" value="@if($clientes->tipo == 'cpf') {{ $clientes->formatted_cpf}} @elseif($clientes->tipo == 'cnpj') {{ $clientes->formatted_cnpj}} @else {{ $clientes->cpf_cnpj }} @endif" readonly>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="nome_fantasia">Nome Fantasia:</label>
                            <input type="text" name="nome_fantasia" id="nome_fantasia" class="form-control" value="{{ $clientes->nome_fantasia }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="razao_social">Razão Social:</label>
                        <input type="text" name="razao_social" id="razao_social" class="form-control" value="{{ $clientes->razao_social }}" required>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="contato">Contato:</label>
                            <input type="text" name="contato" id="contato" class="form-control" value="{{ $clientes->contato }}" required>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="email">E-mail:</label>
                            <input type="text" name="email" id="email" class="form-control" value="{{ $clientes->email }}" required>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="telefone">Telefone:</label>
                            <input type="text" name="telefone" id="telefone" class="form-control" value="{{ $clientes->telefone }}" required>
                        </div>
                    </div>

                    <div class="row">

                        <div class="form-group col-md-8">
                            <label for="usuario_id">Usuário Associado:</label>
                            <select name="usuario_id" id="usuario_id" class="form-control select2">
                                <option value="">-- Selecione --</option>
                                @foreach($usuarios as $usuario)
                                    <option value="{{ $usuario->id }}"
                                        {{ old('usuario_id', $clientes->usuario_id ?? '') == $usuario->id ? 'selected' : '' }}>
                                        {{ $usuario->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>


                        <div class="form-group col-md-4">
                            <label for="ativo">Ativo:</label>
                            <select name="ativo" id="ativo" class="form-control" required>
                                <option value="1" {{ $clientes->ativo ? 'selected' : '' }}>Sim</option>
                                <option value="0" {{ !$clientes->ativo ? 'selected' : '' }}>Não</option>
                            </select>
                        </div>                        
                    </div>


                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-default" style="width: 150px;">Salvar</button>
                    <a href="{{ route('clientes.index') }}" class="btn btn-default" style="width: 150px;">Cancelar</a>
                </div>

            </form>

        </div>

        <form action="{{ route('questionarios.incluir') }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fa-solid fa-building-user" style="margin-right: 5px;"></i> Incluir Questionários</h3>
                    <div class="card-tools">
                        <a href="#" class="btn btn-sm btn-tool d-sm-inline-block" title="Audio">
                            <i class="fa-solid fa-circle-play"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">

                        <input name="cliente_id" value="{{ $clientes->id }}" hidden>

                        <div class="form-group col-md-8">
                            <label for="formulario_id">Questionários:</label>
                            <select name="formulario_id" id="formulario_id" class="form-control select2" required>
                                <option value="">-- Selecione --</option>
                                @foreach($formularios as $formulario)
                                    <option value="{{ $formulario->id }}">
                                        {{ $formulario->label }} | {{ $formulario->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-2">
                            <label for="quantidade">Quantidade:</label>
                            <input type="number" name="quantidade" id="quantidade" class="form-control" required min="1">
                        </div>

                        <div class="form-group col-md-2" style="margin-top: 32px;">
                            <button type="submit" class="btn btn-default btn-block">Incluir</button>
                        </div>

                    </div>
                </div>
            </div>
        </form>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fa-solid fa-building-user" style="margin-right: 5px;"></i>Questionários</h3>
                <div class="card-tools">
                    <a href="#" class="btn btn-sm btn-tool d-sm-inline-block" title="Audio">
                        <i class="fa-solid fa-circle-play"></i>
                    </a>
                </div>
            </div>

            <div class="card-body mr-1">
                <table class="table datatable dtr-inline mr-1 ml-1">
                    <thead>
                        <tr>
                            <th style="width: 75%">Formulário</th>
                            <th style="width: 15%;text-align: center;">Quantidade</th>
                            <th style="width: 10%">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($questionarios as $questionario)
                            <tr>
                                <td> <label class="badge badge-dark"> {{ $questionario->formulario->label }} </label> | {{ $questionario->formulario->nome }} </td>
                                <td style="text-align: center;"> {{ $questionario->quantidade }} </td>
                                <td style="text-align: center;">
                                        <button type="button" class="btn btn-sm btn-tool" style="color: darkred" title="Remover" onclick="confirmarRemocao({{ $questionario->id }})">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div> 

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fa-solid fa-building-user" style="margin-right: 5px;"></i> Editar Cliente</h3>
                <div class="card-tools">
                    <a href="#" class="btn btn-sm btn-tool d-sm-inline-block" title="Audio">
                        <i class="fa-solid fa-circle-play"></i>
                    </a>
                </div>
            </div>

            <div class="text-center m-3 p-3">
                @if(isset($clientes) && !empty($clientes->logo_url))
                    <img class="profile-user-img img-fluid" src="{{ Storage::url($clientes->logo_url) }}" alt="Cliente Logo" style="width: 400px; height: 400px;">
                @endif
            </div>


            <div class="card collapsed-card bg-light" style="margin-left: 20px;margin-right: 20px;">
                
                <div class="card-header">
                    <h3 class="card-title"> <i class="fa-solid fa-camera-retro" style="margin-right: 8px; color:silver;"></i> Alterar Logo</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('upload.image.cliente') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" value="{{ $clientes->id }}">
                        <div class="form-group">
                            <label for="avatar">Nova Imagem do Cliente:</label>
                            <div class="input-group mb-3">
                                <input type="file" name="image" id="image" class="form-control rounded-0" required>
                                <span class="input-group-append">
                                    <button type="submit" class="btn btn-info btn-flat">Enviar!</button>
                                </span>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>       
    </div>
</div>

@stop

@section('js')

{{-- Script Global --}}
<script src="{{ asset('js/utils.js') }}"></script>

@stop