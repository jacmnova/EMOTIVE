@extends('adminlte::page')

@section('title', 'Incluir Cliente')


@section('css')
    {{-- INCLUINDO SUMMERNOTE --}}
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">

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

    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}

    {{-- CSS Global --}}
    <link href="{{ asset('css/utils.css') }}" rel="stylesheet">

@stop

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
                    <li class="breadcrumb-item"><a href="/home">Início</a></li>
                    <li class="breadcrumb-item active">Clientes</li>
                    <li class="breadcrumb-item active">Incluir</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa-solid fa-building-user" style="margin-right: 5px;"></i> Incluir Cliente</h3>
            <div class="card-tools">
                <a href="#" class="btn btn-sm btn-tool d-sm-inline-block" title="Audio">
                    <i class="fa-solid fa-circle-play"></i>
                </a>
            </div>
        </div>

        <form action="{{ route('clientes.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">

                <div class="row">
                    <div class="form-group col-md-2">
                        <label for="tipo">Tipo:</label>
                        <select name="tipo" id="tipo" class="form-control select2" required>
                            <option value="cnpj">CNPJ</option>
                            <option value="cpf">CPF</option>
                            <option value="internacional">Internacional</option>
                        </select>
                    </div>
    
                    <div class="form-group col-md-2">
                        <label for="cpf_cnpj">CNPJ:</label>
                        <input type="text" name="cpf_cnpj" id="cpf_cnpj" class="form-control" required>
                    </div>

                    <div class="form-group col-md-8">
                        <label for="nome_fantasia">Nome Fantasia:</label>
                        <input type="text" name="nome_fantasia" id="nome_fantasia" class="form-control" required>
                    </div>
                </div>
                <div class="row">

                    <div class="form-group col-md-6">
                        <label for="razao_social">Razão Social:</label>
                        <input type="text" name="razao_social" id="razao_social" class="form-control" required>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="usuario_id">Usuário Associado:</label>
                        <select name="usuario_id" id="usuario_id" class="form-control select2">
                            <option value="">-- Selecione --</option>
                            @foreach($usuarios as $usuario)
                                <option value="{{ $usuario->id }}">
                                    {{ $usuario->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div class="row">
                    <div class="form-group col-md-3">
                        <label for="contato">Contato:</label>
                        <input type="text" name="contato" id="contato" class="form-control" required>
                    </div>

                    <div class="form-group col-md-5">
                        <label for="email">E-mail:</label>
                        <input type="text" name="email" id="email" class="form-control" required>
                    </div>

                    <div class="form-group col-md-2">
                        <label for="telefone">Telefone:</label>
                        <input type="text" name="telefone" id="telefone" class="form-control" required>
                    </div>

                    <div class="form-group col-md-2">
                        <label for="ativo">Ativo:</label>
                        <select name="ativo" id="ativo" class="form-control select2" required>
                            <option value="1">Sim</option>
                            <option value="0">Não</option>
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
@stop

@section('js')

{{-- Script Global --}}
<script src="{{ asset('js/utils.js') }}"></script>


    <script>
        $(document).ready(function() {
            // Armazenar o valor inicial do rótulo
            var labelOriginalText = $("label[for='cpf_cnpj']").text();

            // Monitorar as mudanças na seleção
            $("#tipo").change(function() {
                // Obter o valor selecionado
                var selectedOption = $(this).val();

                // Atualizar dinamicamente o rótulo com base na opção selecionada
                $("label[for='cpf_cnpj']").text(selectedOption.toUpperCase());

            });
        });
    </script>



<script>
    function validarCNPJ(cnpj) {
        cnpj = cnpj.replace(/[^\d]+/g,'');

        if (cnpj == '') return false;

        if (cnpj.length != 14)
            return false;

        if (cnpj == "00000000000000" || 
            cnpj == "11111111111111" || 
            cnpj == "22222222222222" || 
            cnpj == "33333333333333" || 
            cnpj == "44444444444444" || 
            cnpj == "55555555555555" || 
            cnpj == "66666666666666" || 
            cnpj == "77777777777777" || 
            cnpj == "88888888888888" || 
            cnpj == "99999999999999")
            return false;

        let tamanho = cnpj.length - 2
        let numeros = cnpj.substring(0,tamanho);
        let digitos = cnpj.substring(tamanho);
        let soma = 0;
        let pos = tamanho - 7;
        for (let i = tamanho; i >= 1; i--) {
            soma += numeros.charAt(tamanho - i) * pos--;
            if (pos < 2) pos = 9;
        }
        let resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
        if (resultado != digitos.charAt(0)) return false;

        tamanho = tamanho + 1;
        numeros = cnpj.substring(0,tamanho);
        soma = 0;
        pos = tamanho - 7;
        for (let i = tamanho; i >= 1; i--) {
            soma += numeros.charAt(tamanho - i) * pos--;
            if (pos < 2) pos = 9;
        }
        resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
        if (resultado != digitos.charAt(1)) return false;

        return true;
    }

    function validarCNPJInput() {
        let cnpjInput = document.getElementById('cnpj');
        let cnpj = cnpjInput.value.trim();

        if (cnpj === "") {
            return true;
        }

        if (!validarCNPJ(cnpj)) {
            Swal.fire({
                icon: 'error',
                title: 'CNPJ inválido!',
                text: 'Por favor, insira um CNPJ válido.'
            }).then(() => {
                cnpjInput.value = "";
                setTimeout(function() {
                    cnpjInput.focus();
                }, 10);
            });
            return false;
        }
        return true;
    }
</script>
@stop