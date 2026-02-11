@extends('adminlte::page')

@section('title', 'Detalhes do Cliente')

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
            <div class="col-sm-6">
            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/home">Início</a></li>
                    <li class="breadcrumb-item"><a href="/clientes">Clientes</a></li>
                    <li class="breadcrumb-item active">Detalhes</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')

    @include('clientes.partials._dados_cliente')

@stop

@section('js')

    {{-- Script Global --}}
    <script src="{{ asset('../js/utils.js') }}"></script>

    {{-- Alteração de Status --}}
    <script>
        function confirmStatus(id) {
            Swal.fire({
                title: 'Alteração do Status!',
                text: 'Esta ação vai alterar o status do Colaborador. Você tem certeza?',
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