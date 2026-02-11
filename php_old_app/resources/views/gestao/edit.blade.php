@extends('adminlte::page')

@section('title', 'Editar Perfil')

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
                    <li class="breadcrumb-item active">Editar</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')

    <div class="row">
        <div class="col-md-6">
            @include('gestao.partials._imagem')
        </div>

        <div class="col-md-6">
                @include('gestao.partials._questionarios') 
        </div>
    </div>

@stop

<script>
    document.addEventListener('DOMContentLoaded', function() {

        document.getElementById('usuarioCheckbox').addEventListener('change', function() {
            document.getElementById('usuarioHidden').value = this.checked ? '1' : '0';
        });

        document.getElementById('gestorCheckbox').addEventListener('change', function() {
            document.getElementById('gestorHidden').value = this.checked ? '1' : '0';
        });

    });
</script>