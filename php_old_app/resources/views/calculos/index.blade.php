@extends('adminlte::page')

@section('title', 'Lista de Cálculos')

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
            <i class="fa-solid fa-triangle-exclamation"></i> {{ Session::get('msgError') }}
        </div>
    @endif

    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Início</a></li>
                    <li class="breadcrumb-item active">Cálculos</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fa-solid fa-calculator" style="margin-right: 5px;"></i> Lista de Cálculos</h3>
        <div class="card-tools d-flex align-items-center">

            <button type="button" class="btn btn-block btn-default btn-sm" onclick="window.location.href='{{ route('calculos.create') }}'" style="margin-right: 10px;"> 
                <i class="fa-solid fa-plus" style="margin-right: 5px;"></i> Incluir Cálculo 
            </button>

            <a href="#" class="btn btn-sm btn-tool d-sm-inline-block" title="Filtro">
                <i class="fa-solid fa-filter"></i>
            </a>
    
            <a href="#" class="btn btn-sm btn-tool d-sm-inline-block" title="Mais Informações">
                <i class="fas fa-bars"></i>
            </a>

        </div>
    </div>

    @if (count($calculos) > 0)
        <div class="card-body mr-1">
            <table class="table table-striped datatable dtr-inline mr-1 ml-1">
                <thead>
                    <tr>
                        <th style="width: 30%">Nome</th>
                        <th style="width: 30%">Descrição</th>
                        <th style="width: 15%">Criado em</th>
                        <th style="width: 10%">Status</th>
                        <th style="width: 15%"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($calculos as $calculo)
                        <tr>
                            <td class="{{ $calculo->ativo ? '' : 'text-muted' }}">{{ $calculo->nome }}</td>
                            <td class="{{ $calculo->ativo ? '' : 'text-muted' }}">{{ $calculo->descricao }}</td>
                            <td class="{{ $calculo->ativo ? '' : 'text-muted' }}">{{ $calculo->created_at ? $calculo->created_at->format('d/m/Y H:i') : 'N/A' }}</td>
                            <td>
                                @if($calculo->ativo)
                                    <small class="badge badge-info">Ativo</small>
                                @else
                                    <small class="badge badge-danger">Inativo</small>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('calculos.show', $calculo->id) }}" class="btn btn-sm btn-tool d-sm-inline-block" title="Visualizar">
                                    <i class="fa-solid fa-eye" style="color: green"></i>
                                </a>

                                <a href="{{ route('calculos.edit', $calculo->id) }}" class="btn btn-sm btn-tool d-sm-inline-block" title="Editar">
                                    <i class="fa-solid fa-pencil" style="color: #008ca5"></i>
                                </a>

                                {{-- Exclusão --}}
                                <form action="{{ route('calculos.destroy', $calculo->id) }}" method="POST" class="d-inline" id="removerForm{{ $calculo->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-tool d-sm-inline-block" title="Remover" onclick="confirmDeletar({{ $calculo->id }})">
                                        <i class="fa-regular fa-trash-can" style="color: darkred"></i>
                                    </button>
                                </form>

                                {{-- Alteração de Status --}}
                                <form action="{{ route('calculos.status', $calculo->id) }}" method="POST" class="d-inline" id="statusForm{{ $calculo->id }}">
                                    @csrf
                                    @method('PUT')
                                    <button type="button" class="btn btn-sm btn-tool d-sm-inline-block" title="{{ $calculo->ativo ? 'Inativar' : 'Ativar' }}" onclick="confirmStatus({{ $calculo->id }})">
                                        <i class="fa-solid fa-toggle-on" style="color: {{ $calculo->ativo ? '#233750' : '#5fc3b4' }}"></i>
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
                <h5><i class="fa-solid fa-circle-info"></i> Nenhum cálculo foi encontrado.</h5>
                <p>Cadastre seu cálculo no botão <strong>"Incluir Cálculo"</strong> no canto superior direito</p>
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

    <script>
        function confirmDeletar(id) {
            Swal.fire({
                title: 'Remover Cálculo!',
                text: 'Essa ação é irreversível. Deseja continuar?',
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
                    document.getElementById('removerForm' + id).submit();
                }
            });
        }

        function confirmStatus(id) {
            Swal.fire({
                title: 'Alterar Status!',
                text: 'Deseja alterar o status deste cálculo?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#008ca5',
                cancelButtonColor: '#5fc3b4',
                confirmButtonText: 'Sim, Alterar',
                cancelButtonText: 'Cancelar',
                iconHtml: '<i class="fa-solid fa-exclamation-circle" style="font-size: 1.5em;"></i>',
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('statusForm' + id).submit();
                }
            });
        }
    </script>

@stop