@extends('adminlte::page')

@section('title', 'Lista de Formulários')

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
                <i class="fa-solid fa-file-alt" style="margin-right: 5px;"></i>
                Lista de Formulários
            </h3>
            <div class="card-tools d-flex align-items-center">

                <button type="button" class="btn btn-block btn-default btn-sm"
                        onclick="window.location.href='{{ route('formularios.create') }}'"
                        style="margin-right: 10px;">
                    <i class="fa-solid fa-plus" style="margin-right: 5px;"></i>
                    Adicionar Formulário
                </button>

                <a href="#" class="btn btn-sm btn-tool d-sm-inline-block" title="Mais Informações">
                    <i class="fas fa-bars"></i>
                </a>

            </div>
        </div>

        @if ($formularios->count() > 0)
            <div class="card-body mr-1">
                <table class="table datatable table-striped dtr-inline mr-1 ml-1">
                    <thead>
                        <tr>
                            <th style="width: 60%">Nome</th>
                            <!-- <th style="width: 40%">Descrição</th> -->
                            <th style="width: 10%">Questões</th>
                            <th style="width: 10%">Dimensões</th>
                            <th style="width: 20%">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($formularios as $formulario)
                            <tr>
                                <td> <label class="badge badge-dark"> {{ $formulario->label }} </label> | {{ $formulario->nome }}</td>
                                <!-- <td>{!! \Illuminate\Support\Str::limit($formulario->descricao, 80, '...') !!}</td> -->
                                <td>{{ $formulario->perguntaCount() }}</td>
                                <td>{{ $formulario->variaveisCount() }}</td>

                                <td>
                                    <a href="{{ route('formularios.show', $formulario->id) }}"
                                    class="btn btn-sm btn-tool" title="Ver Detalhes">
                                        <i class="fa-solid fa-eye" style="color: green"></i>
                                    </a>
                                    <a href="{{ route('formularios.edit', $formulario->id) }}"
                                    class="btn btn-sm btn-tool" title="Editar">
                                        <i class="fa-solid fa-pencil" style="color: #008ca5"></i>
                                    </a>
                                    <form action="{{ route('formularios.destroy', $formulario->id) }}"
                                        method="POST"
                                        class="d-inline"
                                        id="removerForm-{{ $formulario->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                                class="btn btn-sm btn-tool"
                                                style="color: darkred"
                                                title="Remover"
                                                onclick="confirmarRemocao({{ $formulario->id }})">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </form>
                                    {{-- Alteração de Status --}}
                                    <form action="{{ route('formularios.status', $formulario->id) }}" method="POST" class="d-inline" id="statusForm">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status_id" id="statusIdInput">
                                        <button type="button" class="btn btn-sm btn-tool d-sm-inline-block">
                                            <i class="fa-solid fa-toggle-on" style="color: #233750"></i>
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
                    <h5><i class="fa-solid fa-circle-info"></i> Nenhum Formulário foi encontrado.</h5>
                    <p>Cadastre um novo formulário usando o botão <strong>"Adicionar Formulário"</strong> no canto superior direito.</p>
                </div>
            </div>
        @endif
        
    </div>
@stop

@section('js')
    <script src="{{ asset('../js/utils.js') }}"></script>

    <script>
        function confirmarRemocao(id) {
            Swal.fire({
                title: 'Remover Formulário!',
                text: 'Esta ação vai remover o formulário. Você tem certeza?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#008ca5',
                cancelButtonColor: '#5fc3b4',
                confirmButtonText: 'Sim, Remover',
                cancelButtonText: 'Cancelar',
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('removerForm-' + id).submit();
                }
            });
        }
    </script>
@stop
