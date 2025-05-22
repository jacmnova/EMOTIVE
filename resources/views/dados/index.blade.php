@extends('adminlte::page')

@section('title', env('APP_ALIAS'))

@section('css')
{{-- INCLUINDO SUMMERNOTE --}}
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">

    <style>

        .nav-tabs .nav-link {
            color: #6c757d;/* Cor de texto das abas */
            background-color: transparent; /* Cor de fundo das abas */
            border-radius: 4px; /* Bordas arredondadas para as abas */
        }

        .nav-tabs .nav-link:hover {
            color: #6c757d !important;  /* Cor de texto ao passar o mouse */
            background-color: #f8f9fa; /* Cor de fundo ao passar o mouse */
        }

        .nav-tabs .nav-link.active {
            color: #ffffff; /* Cor de texto da aba ativa */
            background-color: #343a40; /* Cor de fundo da aba ativa */
        }

    </style>


    {{-- CSS Global --}}
    <link href="{{ asset('css/utils.css') }}" rel="stylesheet">
@stop


@section('content_header')

@if(Session::has('msgSuccess'))
    <div class="alert alert-success alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <i class="fa-solid fa-circle-user" style="margin-right: 5px"></i> {!! Session::get('msgSuccess') !!}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <i class="fa-solid fa-triangle-exclamation"></i>
        <strong>Ocorreu um problema:</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(Session::has('msgError'))
    <div class="alert alert-danger alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <i class="fa-solid fa-triangle-exclamation"></i> {{ Session::get('msgError') }}
    </div>
@endif

    <!-- Breadcrumb -->
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">

            <p class="lead mb-0" style="font-size: 18px;">Cadastro de Dados do Usuário.</p>

            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="home">Início</a></li>
                    <li class="breadcrumb-item active">Usuário</li>
                </ol>
            </div>
        </div>
    </div>

@stop

@section('content')

    @if(Auth::user()->deleted == null)
        <div class="row">

            <div class="col-md-6">
                @include('dados.partials._dados')            
            </div>

            <div class="col-md-6">
                @if(Auth::user()->cliente_id !== null && Auth::user()->gestor === 1)
                    @include('dados.partials._gestor')
                @else
                    @if(Auth::user()->gestor !== 1)
                        @include('dados.partials._user')
                    @endif
                @endif
            </div>

        </div>
    @endif

@stop

@section('js')
    {{-- Script Global --}}
    <script src="{{ asset('js/utils.js') }}"></script>

    <!-- INCLUINDO SUMMERNOTE JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#descricao').summernote({
                placeholder: 'Outras Observações sobre você...',
                tabsize: 2,
                height: 200,
                toolbar: [
                    ['style', ['style']],
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['fontname', ['fontname']],
                    ['font', ['strikethrough', 'superscript', 'subscript']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video','codeview']],
                    ['misc', ['undo', 'redo']]
                ]
            });
            $('.note-btn-group button').removeClass('dropdown-toggle');
        });

    </script>


    <script>
        document.getElementById('toggleButton').addEventListener('click', function() {
            var hiddenRow = document.querySelector('.hidden-content');
            if (hiddenRow.style.display === 'none') {
                hiddenRow.style.display = 'block';
                this.textContent = 'Menos...';
            } else {
                hiddenRow.style.display = 'none';
                this.textContent = 'Mais...';
            }
        });
    </script>

    <script>
        document.getElementById('toggleButton1').addEventListener('click', function() {
            var hiddenRow = document.querySelector('.hidden-content1');
            if (hiddenRow.style.display === 'none') {
                hiddenRow.style.display = 'block';
                this.textContent = 'Cancelar';
            } else {
                hiddenRow.style.display = 'none';
                this.textContent = 'Incluir dadois Bancários';
            }
        });
    </script>

<script>
        document.getElementById('toggleButton2').addEventListener('click', function() {
            var hiddenRow = document.querySelector('.hidden-content2');
            if (hiddenRow.style.display === 'none') {
                hiddenRow.style.display = 'block';
                this.textContent = 'Cancelar';
            } else {
                hiddenRow.style.display = 'none';
                this.textContent = 'Incluir Endereço';
            }
        });
    </script>


<script>
    function validarCPF(cpf) {
        cpf = cpf.replace(/[^\d]+/g,''); // Remove caracteres não numéricos

        if (cpf == '') return false; // Verifica se está vazio

        if (cpf.length != 11 || 
            cpf == "00000000000" || 
            cpf == "11111111111" || 
            cpf == "22222222222" || 
            cpf == "33333333333" || 
            cpf == "44444444444" || 
            cpf == "55555555555" || 
            cpf == "66666666666" || 
            cpf == "77777777777" || 
            cpf == "88888888888" || 
            cpf == "99999999999")
            return false;

        let soma = 0;
        let resto;

        // Validação do primeiro dígito
        for (let i = 1; i <= 9; i++) {
            soma += parseInt(cpf.charAt(i - 1)) * (11 - i);
        }
        resto = (soma * 10) % 11;

        // O primeiro dígito verificador
        if (resto == 10 || resto == 11) {
            resto = 0;
        }
        if (resto != parseInt(cpf.charAt(9))) {
            return false;
        }

        soma = 0;

        // Validação do segundo dígito
        for (let i = 1; i <= 10; i++) {
            soma += parseInt(cpf.charAt(i - 1)) * (12 - i);
        }
        resto = (soma * 10) % 11;

        // O segundo dígito verificador
        if (resto == 10 || resto == 11) {
            resto = 0;
        }
        if (resto != parseInt(cpf.charAt(10))) {
            return false;
        }

        return true;
    }

    function validarCPFInput() {
        let cpfInput = document.getElementById('cpf');
        let cpf = cpfInput.value.trim();

        if (cpf === "") {
            return true; // Se o campo estiver vazio, não faz nada
        }

        if (!validarCPF(cpf)) {
            Swal.fire({
                icon: 'error',
                title: 'CPF inválido!',
                text: 'Por favor, insira um CPF válido.'
            }).then(() => {
                cpfInput.value = ""; // Limpa o campo
                setTimeout(function() {
                    cpfInput.focus(); // Foca no campo novamente
                }, 10);
            });
            return false;
        }
        return true;
    }
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