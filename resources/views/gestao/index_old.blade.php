@extends('adminlte::page')

@section('title', 'Usuários')

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
                    <li class="breadcrumb-item active">Usuários</li>
                </ol>
            </div>
        </div>
    </div>

@stop

@section('content')

<div class="card">

    <div class="card-header">
        <h3 class="card-title ">Lista de Usuários</h3>
        <div class="card-tools d-flex align-items-center">


            <button type="button" class="btn btn-default btn-sm mr-2" onclick="window.location.href='{{ route('gestor.create.cliente') }}'">
                <i class="fa-solid fa-plus mr-1"></i>
                Adicionar Usuário
            </button>

            <button type="button" class="btn btn-default btn-sm mr-2" onclick="window.location.href='{{ route('gestor.importar.form') }}'">
                <i class="fa-solid fa-file-import mr-1"></i>
                Importar em Lote
            </button>





        </div>
    </div>

    @if(count($usuarios) > 0)

        <div class="card-body mr-1">
            <table class="table table-striped datatable dtr-inline mr-1 ml-1">
                <thead>
                    <tr>
                        <th style="width: 20%">Nome</th>
                        <th style="width: 25%">Email</th>
                        <th style="width: 10%">Perfis</th>
                        <th style="width: 10%; text-align: center;">Formulários</th>
                        <th style="width: 15%"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usuarios as $perfil)
                        <tr>
                            <td>
                                <img src="{{ asset('storage/' . $perfil->avatar )}}" class="img-fluid" alt="Avatar" style="border-radius: 50%; width:35px; margin-right:10px;" title="ID:{{$perfil->id}}">
                                {{ $perfil->name }}
                            </td>
                            <td>{{ $perfil->email }}</td>
                            <td>
                                @if($perfil->gestor == 1)
                                    <i class="fa-brands fa-black-tie" style="margin-right: 8px;color: violet;font-size: 22px;" title="Gestor"></i>
                                @endif

                                @if($perfil->usuario == 1)
                                    <i class="fa-solid fa-user"" style="margin-right: 8px;color: green;font-size: 22px;" title="Usuário"></i>
                                @endif
                            </td>
                        
                            <td style="text-align: center;">
                                {{ $perfil->quantidade_formularios }}
                            </td>
                            
                            <td>
                                <a href="{{ route('usuarios.show', $perfil->id) }}" class="btn btn-sm btn-tool" title="Ver Detalhes">
                                    <i class="fa-solid fa-eye" style="color: green"></i>
                                </a>

                                <a href="{{ route('usuarios.editar', $perfil->id) }}" class="btn btn-sm btn-tool d-sm-inline-block" title="Editar">
                                    <i class="fa-solid fa-pencil" style="color: #008ca5"></i>
                                </a>                            
                                @if($perfil['sa'] !== true)
                                    <form action="{{ route('usuarios.status', $perfil->id) }}" method="POST" class="d-inline" id="statusForm">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status_id" id="statusIdInput">
                                        <button type="button" class="btn btn-sm btn-tool d-sm-inline-block" title=" @if($perfil->email_verified_at !== null) Inativar @else Ativar @endif" onclick="confirmStatus({{$perfil->id}})">
                                            <i class="fa-solid fa-toggle-on" style="color: {{ $perfil->email_verified_at ? '#233750' : '#5fc3b4' }}"></i>
                                        </button>
                                    </form>

                                    <form action="{{ route('impersonate.start', ['id' => $perfil->id]) }}" method="POST" class="d-inline" id="startForm_{{ $perfil->id }}">
                                        @csrf
                                        <a class="btn btn-sm btn-tool d-sm-inline-block" href="#" title="Impersonate" onclick="document.getElementById('startForm_{{ $perfil->id }}').submit()">
                                            <i class="fa-solid fa-user-gear" style="color: red"></i>
                                        </a>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="row" style="margin: 20px;">
            <div class="callout callout-warning">
                <h5><i class="fa-solid fa-circle-info"></i> Nenhum Perfil foi encontrado.</h5>
                <p>Cadastre seu perfil no botão <strong>"Incluir Perfil"</strong> no canto superior direito</p>
            </div>
        </div>
    @endif

    <div class="card-footer text-right">
        <a href="#" class="btn btn-sm btn-tool">
            <i class="fa-solid fa-circle-info"></i>
        </a>
    </div>

</div>
@stop

@section('js')
    <script src="{{ asset('../js/utils.js') }}"></script>
@stop
