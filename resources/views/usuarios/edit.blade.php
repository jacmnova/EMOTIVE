@extends('adminlte::page')

@section('title', 'Editar Perfil')

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
                <h1>Editar Perfil</h1>
            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Início</a></li>
                    <li class="breadcrumb-item active">Perfil</li>
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
                    <h3 class="card-title">Editar Informações do Perfil</h3>
                    <div class="card-tools">
                        <a href="#" class="btn btn-sm btn-tool d-sm-inline-block" title="Filtro">
                            <i class="fa-solid fa-filter"></i>
                        </a>
                
                        <a href="#" class="btn btn-sm btn-tool d-sm-inline-block" title="Mais Informações">
                            <i class="fas fa-bars"></i>
                        </a>
                    </div>
                </div>

                <form action="{{ route('usuarios.update', $usuario->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        <div class="card card-widget widget-user">
                            <div class="widget-user-header text-white" style="background: url('{{ asset('img/panel_user.png') }}') center center;">
                                <h3 class="widget-user-username text-right">{{$usuario->name}}</h3>
                                <h5 class="widget-user-desc text-right">{{$usuario->email}}</h5>
                            </div>
                    
                            <div class="widget-user-image">
                                <img class="img-circle" src="{{ asset('storage/' . $usuario->avatar) }}" alt="User Avatar">
                            </div>
                    
                            <div class="card-footer">
                    
                                <div class="row">
                    
                                    <div class="col-sm-4 border-right">
                                        <div class="description-block">
                                            {{-- <h5 class="description-header">EMPRESA</h5>
                                            <span class="description-text">0</span> --}}
                                            <i class="fa-solid fa-trophy"></i>
                                        </div>
                                    </div>
                                
                                    <div class="col-sm-4 border-right">
                                        <div class="description-block">
                                            {{-- <h5 class="description-header">PROJETO</h5>
                                            <span class="description-text">0</span> --}}
                                            {{-- <i class="fa-solid fa-certificate"></i> --}}
                                            <i class="fa-solid fa-award"></i>
                                        </div>
                                    </div>
                                
                                    <div class="col-sm-4">
                                        <div class="description-block">
                                            {{-- <h5 class="description-header">INSTRUMENTOS</h5>
                                            <span class="description-text">0</span> --}}
                                            <i class="fa-solid fa-ranking-star"></i>
                                        </div>
                                    </div>
                                
                                </div>
                            
                            </div>
                        </div>

                        <input hidden type="text" name="id" class="form-control" value="{{ $usuario->id}}" required>
                
                        <div class="form-group">
                            <label for="name">Nome:</label>
                            <input type="text" name="name" class="form-control" value="{{ $usuario->name}}" required>
                        </div>
                
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" name="email" class="form-control" value="{{ $usuario->email }}" readonly required>
                        </div>

                        <div class="form-group">
                            <label for="cliente_id">Associar a cliente:</label>
                            <select name="cliente_id" id="cliente_id" class="form-control select2">
                                <option value="">-- Selecione --</option>
                                @foreach($clientes as $cliente)
                                    <option value="{{ $cliente->id }}"
                                        {{ old('cliente_id', $usuario->cliente_id ?? '') == $cliente->id ? 'selected' : '' }}>
                                        {{ $cliente->nome_fantasia }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
            

                        <div class="card card-widget widget-user border-0 p-4">
                            <p class="lead"><i class="fa-solid fa-house-lock"></i> Permissões</p>
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th style="width:50%">
                                            <div class="form-group">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" name="sa" id="saCheckbox" value="1" {{ $usuario->sa ? 'checked' : '' }} @disabled(true)>
                                                    <label class="custom-control-label" for="saCheckbox">Super Usuário</label>
                                                    <input type="hidden" name="sa_hidden" id="saHidden" value="{{ $usuario->sa ? '1' : '0' }}">
                                                </div>
                                            </div>                                        
                                        </th>
                                    </tr>
                                    <tr>
                                        <th style="width:50%">
                                            <div class="form-group">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" name="admin" id="adminCheckbox" value="1" {{ $usuario->admin ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="adminCheckbox">Administrador</label>
                                                    <input type="hidden" name="admin_hidden" id="adminHidden" value="{{ $usuario->admin ? '1' : '0' }}">
                                                </div>
                                            </div>                                        
                                        </th>
                                    </tr>

                                    <tr>
                                        <th style="width:50%">
                                            <div class="form-group">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" name="gestor" id="gestorCheckbox" value="1" {{ $usuario->gestor ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="gestorCheckbox">Gestor</label>
                                                    <input type="hidden" name="gestor_hidden" id="gestorHidden" value="{{ $usuario->gestor ? '1' : '0' }}">
                                                </div>
                                            </div>                                        
                                        </th>
                                    </tr>

                                    <tr>
                                        <th style="width:50%">
                                            <div class="form-group">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" name="usuario" id="usuarioCheckbox" value="1" {{ $usuario->usuario ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="usuarioCheckbox">Usuário</label>
                                                    <input type="hidden" name="usuario_hidden" id="usuarioHidden" value="{{ $usuario->usuario ? '1' : '0' }}">
                                                </div>
                                            </div>                                        
                                        </th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-default" style="width: 150px;">Salvar</button>
                        <a href="{{ route('usuarios.index') }}" class="btn btn-default" style="width: 150px;">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-6">
            @include('usuarios.partials._questionarios') 
        </div>
    </div>

@stop



@section('js')

{{-- Script Global --}}
<script src="{{ asset('js/utils.js') }}"></script>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('saCheckbox').addEventListener('change', function() {
            document.getElementById('saHidden').value = this.checked ? '1' : '0';
        });

        document.getElementById('adminCheckbox').addEventListener('change', function() {
            document.getElementById('adminHidden').value = this.checked ? '1' : '0';
        });

        document.getElementById('usuarioCheckbox').addEventListener('change', function() {
            document.getElementById('usuarioHidden').value = this.checked ? '1' : '0';
        });

        document.getElementById('gestorCheckbox').addEventListener('change', function() {
            document.getElementById('gestorHidden').value = this.checked ? '1' : '0';
        });

    });
</script>


@stop
