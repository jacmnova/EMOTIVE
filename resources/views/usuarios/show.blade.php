@extends('adminlte::page')

@section('title', 'Visualizar Usuário')

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
            <div class="col-sm-6">
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Início</a></li>
                    <li class="breadcrumb-item"><a href="#">Usuários</a></li>
                    <li class="breadcrumb-item active">Visualizar</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')

    <div class="row">
        <div class="col-md-6">
            <div class="card card-widget widget-user shadow">

                <div class="widget-user-header bg-info">
                    <h3 class="widget-user-username">{{$usuario->name}}</h3>
                    <h5 class="widget-user-desc">{{$usuario->email}}</h5>
                </div>

                <div class="widget-user-image">
                    <img class="img-circle" src="{{ asset('storage/' . $usuario->avatar) }}" alt="User Avatar">
                </div>

                <div class="card-footer">
                    <div class="row">

                        <div class="col-sm-4 border-right">
                            <div class="description-block">
                                <h5 class="description-header">{{ $quantidadeFormularios }}</h5>
                                <span class="description-text">Liberados</span>
                            </div>
                        </div>

                        <div class="col-sm-4 border-right">
                            <div class="description-block">
                                <h5 class="description-header">{{ $quantidadePendente }}</h5>
                                <span class="description-text">Pendentes</span>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="description-block">
                                <h5 class="description-header">{{ $quantidadeFinalizado }}</h5>
                                <span class="description-text">Finalizados</span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        @if($cliente)
            <div class="col-md-6">
                <div class="card card-widget widget-user shadow">
                    <div class="widget-user-header bg-dark">
                        <h3 class="widget-user-username">{{ strtoupper($cliente->razao_social) }}</h3>
                        <h5 class="widget-user-desc">{{ $cliente->email }}</h5>
                        @if($cliente->tipo === 'cpf')
                            <p><i class="fa-solid fa-person" style="color: rgb(206, 206, 206); margin-right: 7px;" title="CPF"></i> {{ $cliente->formatted_cpf }} </p>
                        @elseif($cliente->tipo === 'cnpj')
                            <p><i class="fa-solid fa-building" style="color: rgb(206, 206, 206); margin-right: 7px;" title="CNPJ"></i> {{ $cliente->formatted_cnpj }} </p>
                        @else
                            <p><i class="fa-solid fa-globe" style="color: #008ca5; margin-right: 7px;" title="Internacional"></i> {{ $cliente->cpf_cnpj }} </p>
                        @endif
                    </div>
                    <div class="card-footer">
                    </div>
                </div>
            </div>
        @endif

    </div>

    @include('usuarios.partials._formularios')

@stop