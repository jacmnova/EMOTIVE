@extends('adminlte::page')

@section('title', 'Usuários')

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
                    <li class="breadcrumb-item active">Usuários</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Lista de Usuários</h3>
        <div class="card-tools d-flex align-items-center">
            <button type="button" class="btn btn-default btn-sm mr-2" onclick="window.location.href='{{ route('usuarios.create') }}'">
                <i class="fa-solid fa-plus mr-1"></i>
                Adicionar Usuário
            </button>

            <a href="#" id="toggleView" class="btn btn-sm btn-tool d-sm-inline-block" title="Alterar visualização">
                <i id="iconeVisualizacao" class="fa-solid fa-grip-vertical"></i>
            </a>

        </div>
    </div>

    <div id="visualizacaoCards" style="display: none;">
        {{-- Aqui vai o conteúdo de visualização em cards (já implementado) --}}
        @include('usuarios.partials._visualizacao_cards', ['perfis' => $perfis])
    </div>
    <div id="visualizacaoTabela">
        {{-- Aqui vai o conteúdo de visualização em tabela --}}
        @include('usuarios.partials._visualizacao_tabela', ['perfis' => $perfis])
    </div>

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
            title: 'Remover Usuário!',
            text: 'Esta ação vai remover o usuário. Você tem certeza?',
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

    function confirmStatus(id) {
        Swal.fire({
            title: 'Verificação de e-mail!',
            text: 'Esta ação vai alterar o status deste usuário. Você tem certeza?',
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
                document.getElementById('statusIdInput').value = id;
                document.getElementById('statusForm').submit();
            }
        });
    }

    document.getElementById('toggleView').addEventListener('click', function (e) {
        e.preventDefault();
        const cards = document.getElementById('visualizacaoCards');
        const tabela = document.getElementById('visualizacaoTabela');
        const icone = document.getElementById('iconeVisualizacao');

        if (cards.style.display === 'none') {
            cards.style.display = 'block';
            tabela.style.display = 'none';
            icone.classList.remove('fa-grip-vertical');
            icone.classList.add('fa-list');
        } else {
            cards.style.display = 'none';
            tabela.style.display = 'block';
            icone.classList.remove('fa-list');
            icone.classList.add('fa-grip-vertical');
        }
    });
</script>
@stop
