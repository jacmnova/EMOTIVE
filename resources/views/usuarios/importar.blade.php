@extends('adminlte::page')

@section('title', 'Importar Usuários')

@section('content_header')
    @if(Session::has('msgSuccess'))
        <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <i class="fa-regular fa-bell mr-1"></i> {!! Session::get('msgSuccess') !!}
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
            <div class="col-sm-6"></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Início</a></li>
                    <li class="breadcrumb-item active">Importação de Usuários</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')

<div class="card">
    <div class="card-header">
        <h3 class="card-title"> <i class="fa-solid fa-sitemap mr-2"></i>Importação de Usuários em Lote</h3>
        <div class="card-tools">

            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                <i class="fas fa-minus"></i>
            </button>

            <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                <i class="fas fa-times"></i>
            </button>

        </div>
    </div>
        <form action="{{ route('gestor.importar') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="card-body">
                <div class="alert alert-secondary" role="alert">
                    <h5><i class="fa-solid fa-circle-info mr-1"></i> Instruções para Importação</h5>
                    <p>Para importar usuários em lote, envie um arquivo <strong>CSV</strong> com as seguintes colunas:</p>

                    <ul class="mb-2">
                        <li><strong>email</strong>: endereço de e-mail válido de cada usuário</li>
                        <li><strong>nome</strong>: nome completo do usuário</li>
                    </ul>

                    <p>O cabeçalho da primeira linha <strong>deve estar presente</strong>, como no exemplo:</p>

                    <pre class="bg-light p-2 border rounded text-left">
                        email,nome
                        joao.silva@empresa.com,João da Silva
                        maria.souza@empresa.com,Maria Souza
                    </pre>

                    <p>Todos os usuários importados receberão uma senha padrão e um e-mail de boas-vindas com instruções de acesso.</p>
                </div>
                <div class="form-group">
                    <label for="arquivo">Selecione o arquivo CSV:</label>
                    <input type="file" name="arquivo" class="form-control" required>
                </div>


            </div>

            <div class="card-footer text-right">
                <button type="submit" class="btn btn-secondary" style="width: 250px;">Importar Lote</button>
            </div>

        </form>

</div>

@stop