@extends('adminlte::page')

@section('title', 'Lista de Clientes')

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
                    <li class="breadcrumb-item active">Clientes</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fa-solid fa-building-user" style="margin-right: 5px;"></i> Lista de Clientes</h3>
        <div class="card-tools d-flex align-items-center">

            <button type="button" class="btn btn-block btn-default btn-sm" onclick="window.location.href='{{ route('clientes.create') }}'" style="margin-right: 10px;"> 
                <i class="fa-solid fa-plus" style="margin-right: 5px;"></i> Incluir Cliente 
            </button>

            <a href="#" class="btn btn-sm btn-tool d-sm-inline-block" title="Incuir Cliente" style="color: #008ca5">
                <i class="fa-solid fa-circle-plus"></i>
            </a>

            <a href="#" class="btn btn-sm btn-tool d-sm-inline-block" title="Filtro" style="color: green">
                <i class="fa-solid fa-filter"></i>
            </a>
    
            <a href="#" class="btn btn-sm btn-tool d-sm-inline-block" title="Mais Informações">
                <i class="fas fa-bars"></i>
            </a>

        </div>
    </div>

    @if (count($clientes) > 0)

        <div class="card-body mr-1">
            <table class="table datatable dtr-inline mr-1 ml-1">
                <thead>
                    <tr>
                        <th style="width: 30%">Nome Fantasia</th>
                        <th style="width: 20%">Razão Social</th>
                        <th style="width: 10%">CPF/CNPJ</th>
                        <th style="width: 10%">Contato</th>
                        <th style="width: 10%">Criado em</th>
                        <th style="width: 5%">Status</th>
                        <th style="width: 15%">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($clientes as $cliente)
                        <tr>
                            @if($cliente->ativo == 1)
                                <td>

                                    <div class="btn-group">
                                        <button type="button" class="btn btn-white btn-sm" data-toggle="dropdown" data-offset="-30" aria-expanded="false">
                                            <i class="fa-solid fa-ellipsis-vertical" style="width: 13px;margin-right: 4px;color: #008ca5;"></i>
                                        </button>
                                        <div class="dropdown-menu" role="menu">
                                            <a href="{{ route('clientes.show', $cliente->id) }}" class="dropdown-item"><i class="fa-solid fa-eye" style="margin-right: 4px;"></i> Visualizar</a>
                                            <a href="{{ route('clientes.edit', $cliente->id) }}" class="dropdown-item"><i class="fa-solid fa-pencil" style="margin-right: 4px;"></i> Editar</a>
                                        </div>
                                    </div>

                                    <img src="{{ Storage::url($cliente->logo_url) }}" class="img-fluid" alt="Avatar" style="border-radius: 50%; width: 35px; margin-right: 10px;" title="ID:{{$cliente->id}}">

                                    {{ $cliente->nome_fantasia }} 

                                </td>
                                <td> {{ $cliente->razao_social }} </td>
                                <td>
                                    @if($cliente->tipo === 'cpf')
                                        <p><i class="fa-solid fa-person" style="color: rgb(206, 206, 206); margin-right: 7px;" title="CPF"></i> {{ $cliente->formatted_cpf }} </p>
                                    @elseif($cliente->tipo === 'cnpj')
                                        <p><i class="fa-solid fa-building" style="color: rgb(206, 206, 206); margin-right: 7px;" title="CNPJ"></i> {{ $cliente->formatted_cnpj }} </p>
                                    @else
                                        <p><i class="fa-solid fa-globe" style="color: #008ca5; margin-right: 7px;" title="Internacional"></i> {{ $cliente->cpf_cnpj }} </p>
                                    @endif
                                </td>
                                <td> 
                                    <a href="" class="btn btn-sm btn-tool d-sm-inline-block" title="{{ $cliente->contato }} ">
                                        <i class="fa-solid fa-user" style="color: #008ca5"></i>
                                    </a>

                                    <a href="" class="btn btn-sm btn-tool d-sm-inline-block" title="{{ $cliente->telefone }} ">
                                        <i class="fa-solid fa-phone-flip" style="color: #008ca5"></i>
                                    </a>

                                    <a href="" class="btn btn-sm btn-tool d-sm-inline-block" title="{{ $cliente->email }} ">
                                        <i class="fa-solid fa-envelope" style="color: #008ca5"></i>
                                    </a>

                                        @if($cliente->gestor)
                                            <a href="{{ route('usuarios.show', $cliente->gestor->id)}}" class="btn btn-sm btn-tool d-sm-inline-block" title="{{ optional($cliente->gestor)->name ?? 'Gestor não atribuído' }}"> 
                                                <i class="fa-solid fa-user" style="color:rgb(165, 52, 0)"></i>
                                            </a>
                                        @else
                                            <a href="#" class="btn btn-sm btn-tool d-sm-inline-block" title="{{ optional($cliente->gestor)->name ?? 'Gestor não atribuído' }}"> 
                                                <i class="fa-solid fa-user" style="color:rgb(226, 226, 226)"></i>
                                            </a>
                                        @endif

                                </td>
                                
                                <td>{{ $cliente->created_at->format('d/m/Y') }}</td>

                            @else
                                <td style="color: gray"> 
                                    <img src="{{ Storage::url($cliente->logo_url) }}" class="img-fluid" alt="Avatar" style="border-radius: 50%; width:35px;" title="ID:{{$cliente->id}}">
                                    <a href="{{ route('clientes.show', $cliente->id) }}" class="btn btn-sm btn-tool d-sm-inline-block">
                                        <i class="fa-solid fa-ellipsis-vertical" style="margin-right: 12px;color: #008ca5;"></i>
                                    </a>
                                
                                    {{ $cliente->nome_fantasia }} 
                                </td>
                                <td style="color: gray"> {{ $cliente->razao_social }} </td>
                                <td style="color: gray">
                                    @if($cliente->tipo === 'cpf')
                                        <p><i class="fa-solid fa-person" style="color: rgb(206, 206, 206); margin-right: 7px;" ></i> {{ $cliente->formatted_cpf }} </p>
                                    @elseif($cliente->tipo === 'cnpj')
                                        <p><i class="fa-solid fa-building" style="color: rgb(206, 206, 206); margin-right: 7px;"></i> {{ $cliente->formatted_cnpj }} </p>
                                    @else
                                        <p><i class="fa-solid fa-globe" style="margin-right: 7px;"></i> {{ $cliente->cpf_cnpj }} </p>
                                    @endif
                                </td>
                                <td style="color: gray"> 
                                    <a href="" class="btn btn-sm btn-tool d-sm-inline-block" title="{{ $cliente->contato }} ">
                                        <i class="fa-solid fa-user" style="color: gray"></i>
                                    </a>

                                    <a href="" class="btn btn-sm btn-tool d-sm-inline-block" title="{{ $cliente->telefone }} ">
                                        <i class="fa-solid fa-phone-flip" style="color: gray"></i>
                                    </a>

                                    <a href="" class="btn btn-sm btn-tool d-sm-inline-block" title="{{ $cliente->email }} ">
                                        <i class="fa-solid fa-envelope" style="color: gray"></i>
                                    </a>
                                </td>
                                <td style="color: gray"> {{ $cliente->created_at->format('d/m/Y') }} </td>
                            @endif

                            <td>
                                @if($cliente->ativo)
                                    <small class="badge badge-info">Ativo</small>
                                @else
                                    <small class="badge badge-danger">Inativo</small>
                                @endif
                            </td>

                            <td>
                                <a href="{{ route('clientes.show', $cliente->id) }}" class="btn btn-sm btn-tool d-sm-inline-block" title="Descrição">
                                    <i class="fa-solid fa-eye" style="color: green"></i>
                                </a>

                                <a href="{{ route('clientes.edit', $cliente->id) }}" class="btn btn-sm btn-tool d-sm-inline-block" title="Editar">
                                    <i class="fa-solid fa-pencil" style="color: #008ca5"></i>
                                </a>

                                {{-- Exclusão de cliente --}}
                                <form action="{{ route('clientes.destroy', $cliente->id) }}" method="POST" class="d-inline" id="removerForm">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="destroy_id" id="deleteIdInput">
                                    <button type="button" class="btn btn-sm btn-tool d-sm-inline-block" title="Remover" onclick="confirmDeletar({{$cliente->id}})">
                                        <i class="fa-regular fa-trash-can" style="color: darkred"></i>
                                    </button>
                                </form>

                                {{-- Alteração de Status --}}
                                <form action="{{ route('clientes.status', $cliente->id) }}" method="POST" class="d-inline" id="statusForm">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status_id" id="statusIdInput">
                                    <button type="button" class="btn btn-sm btn-tool d-sm-inline-block" title="{{ $cliente->ativo ? 'Inativar' : 'Ativar' }}" onclick="confirmStatus({{$cliente->id}})">
                                        <i class="fa-solid fa-toggle-on" style="color: {{ $cliente->ativo ? '#233750' : '#5fc3b4' }}"></i>
                                    </button>
                                </form>
                            
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    @else
        <div class="row" style="margin: 20px;">
            <div class="callout callout-warning">
                <h5><i class="fa-solid fa-circle-info"></i> Nenhum Cliente foi encontrado.</h5>
                <p>Cadastre seu Cliente no botão <strong>"Incluir Cliente"</strong> no canto superior direito</p>
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

    {{-- Script Global --}}
    <script src="{{ asset('../js/utils.js') }}"></script>

    {{-- Exclusão de Idioma --}}
    <script>
        function confirmDeletar(id) {
            Swal.fire({
                title: 'Remover Cliente!',
                text: 'Esta ação vai remover o Cliente. Você tem certeza?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#008ca5',
                cancelButtonColor: '#5fc3b4',
                confirmButtonText: 'Sim, Remover',
                cancelButtonText: 'Cancelar',
                iconHtml: '<i class="fa-solid fa-exclamation-circle text-danger" style="font-size: 1.5em;"></i> ',
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('deleteIdInput').value = id;
                    document.getElementById('removerForm').submit();
                }
            });
        }
    </script>

    {{-- Alteração de Status --}}
    <script>
        function confirmStatus(id) {
            Swal.fire({
                title: 'Alteração do Status!',
                text: 'Esta ação vai alterar o status do Cliente. Você tem certeza?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#008ca5',
                cancelButtonColor: '#5fc3b4',
                confirmButtonText: 'Sim, Alterar',
                cancelButtonText: 'Cancelar',
                iconHtml: '<i class="fa-solid fa-exclamation-circle" style="font-size: 1.5em;"></i> ',  
                allowOutsideClick: false,
            allowEscapeKey: false       
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('statusIdInput').value = id;
                    document.getElementById('statusForm').submit();
                }
            });
        }
    </script>
@stop
