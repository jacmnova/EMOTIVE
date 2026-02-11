@extends('adminlte::page')

@section('title', 'Cadastrar Usuário')

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
    <link href="{{ asset('css/utils.css') }}" rel="stylesheet">
@stop

@section('content_header')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Cadastrar Novo Usuário</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Início</a></li>
                <li class="breadcrumb-item active">Usuários</li>
                <li class="breadcrumb-item active">Cadastrar</li>
            </ol>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Informações do Novo Usuário</h3>
    </div>
    <form action="{{ route('gestor.cliente.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">

                    <div class="form-group">
                        <label for="name">Nome:</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <input name="cliente_id" type="text" value="{{ Auth::user()->cliente_id}}" hidden>

                </div>
                <div class="col-md-6">
                    <p class="lead"><i class="fa-solid fa-house-lock"></i> Permissões</p>
                    <table class="table">
                        <tbody>
                            <tr>
                                <th style="width:50%">
                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="gestor" id="gestorCheckbox" value="1">
                                            <label class="custom-control-label" for="gestorCheckbox">Gestor</label>
                                            <input type="hidden" name="gestor_hidden" id="gestorHidden" value="0">
                                        </div>
                                    </div>
                                </th>
                            </tr>
                            <tr>
                                <th style="width:50%">
                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="usuario" id="usuarioCheckbox" value="1" checked>
                                            <label class="custom-control-label" for="usuarioCheckbox">Usuário</label>
                                            <input type="hidden" name="usuario_hidden" id="usuarioHidden" value="1">
                                        </div>
                                    </div>
                                </th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-default" style="width: 150px;">Salvar</button>
            <a href="{{ route('usuarios.index') }}" class="btn btn-default" style="width: 150px;">Cancelar</a>
        </div>
    </form>
</div>
@stop

@section('js')
<script src="{{ asset('js/utils.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        document.getElementById('gestorCheckbox').addEventListener('change', function() {
            document.getElementById('gestorHidden').value = this.checked ? '1' : '0';
        });

        document.getElementById('usuarioCheckbox').addEventListener('change', function() {
            document.getElementById('usuarioHidden').value = this.checked ? '1' : '0';
        });
    });
</script>
@stop
