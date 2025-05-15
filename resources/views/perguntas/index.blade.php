@extends('adminlte::page')

@section('title', 'Lista de Perguntas')

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
                    <li class="breadcrumb-item active">Perguntas</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fa-solid fa-question" style="margin-right: 5px;"></i>
            Lista de Perguntas
        </h3>
        <div class="card-tools d-flex align-items-center">
            <button type="button" class="btn btn-block btn-default btn-sm"
                    onclick="window.location.href='{{ route('perguntas.create') }}'"
                    style="margin-right: 10px;">
                <i class="fa-solid fa-plus" style="margin-right: 5px;"></i>
                Incluir Pergunta
            </button>

            <a href="#" class="btn btn-sm btn-tool d-sm-inline-block" title="Mais Informações">
                <i class="fas fa-bars"></i>
            </a>
        </div>
    </div>

    @if ($perguntas->count() > 0)
        <div class="card-body mr-1">
            <table class="table datatable table-striped dtr-inline mr-1 ml-1">
                <thead>
                    <tr>
                        <th style="width: 15%">Formulário</th>
                        <th style="width: 5%">Número</th>
                        <th style="width: 60%">Pergunta</th>
                        <th style="width: 10%">Criado em</th>
                        <th style="width: 10%">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($perguntas as $pergunta)
                        <tr>
                            <td>{{ $pergunta->formulario ? $pergunta->formulario->nome : 'N/A' }}</td>
                            <td>{{ $pergunta->numero_da_pergunta }}</td>
                            <td>{{ $pergunta->pergunta }}</td>
                            <td>
                                @if($pergunta->created_at)
                                    {{ $pergunta->created_at->format('d/m/Y H:i') }}
                                @else
                                    --
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('perguntas.show', $pergunta->id) }}"
                                   class="btn btn-sm btn-tool d-sm-inline-block"
                                   title="Ver Detalhes">
                                    <i class="fa-solid fa-eye" style="color: green"></i>
                                </a>
                                <a href="{{ route('perguntas.edit', $pergunta->id) }}"
                                   class="btn btn-sm btn-tool d-sm-inline-block"
                                   title="Editar">
                                    <i class="fa-solid fa-pencil" style="color: #008ca5"></i>
                                </a>
                                <form action="{{ route('perguntas.destroy', $pergunta->id) }}"
                                      method="POST"
                                      class="d-inline"
                                      id="removerForm-{{ $pergunta->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                            class="btn btn-sm btn-tool d-sm-inline-block"
                                            style="color: darkred"
                                            title="Remover"
                                            onclick="confirmDeletar({{ $pergunta->id }})">
                                        <i class="fa-regular fa-trash-can"></i>
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
                <h5><i class="fa-solid fa-circle-info"></i> Nenhuma Pergunta foi encontrada.</h5>
                <p>Cadastre sua Pergunta no botão
                   <strong>"Incluir Pergunta"</strong> no canto superior direito.</p>
            </div>
        </div>
    @endif

</div>
@stop

@section('js')
<script src="{{ asset('../js/utils.js') }}"></script>

    <script>
        function confirmDeletar(id) {
            Swal.fire({
                title: 'Remover Pergunta!',
                text: 'Esta ação vai remover a Pergunta. Você tem certeza?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#008ca5',
                cancelButtonColor: '#5fc3b4',
                confirmButtonText: 'Sim, Remover',
                cancelButtonText: 'Cancelar',
                iconHtml: '<i class="fa-solid fa-exclamation-circle text-danger" style="font-size: 1.5em;"></i>',
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
