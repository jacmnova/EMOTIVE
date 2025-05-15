@extends('adminlte::page')

@section('title', 'Incluir Formulário')


@section('css')
    {{-- INCLUIDNO SUMMERNOTE --}}
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
                <h1>Incluir Formulário</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/home">Início</a></li>
                    <li class="breadcrumb-item"><a href="/formularios">Formulários</a></li>
                    <li class="breadcrumb-item active">Incluir</li>
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
                Incluir Formulário
            </h3>
        </div>

        <form action="{{ route('formularios.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="row">

                    <div class="form-group col-md-6">
                        <label for="nome">Nome:</label>
                        <input type="text" name="nome" id="nome" class="form-control" value="{{ old('nome') }}" required>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="nome">Label:</label>
                        <input type="text" name="label" id="label" class="form-control" value="{{ old('label') }}" required>
                    </div>

                </div>

                <div class="row">
                    <div class="form-group col-md-12">
                        <label for="descricao">Descrição</label>
                        <div wire:ignore>
                            <textarea class="form-control" name="descricao" id="descricao" wire:model="descricao">{{ old('descricao') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-12">
                        <label for="instrucoes">Instruções</label>
                        <div wire:ignore>
                            <textarea class="form-control" name="instrucoes" id="instrucoes" wire:model="instrucoes">{{ old('instrucoes') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="row">

                    <div class="form-group col-md-6">
                        <label for="calculo_id">Cálculo:</label>
                        <select name="calculo_id" id="calculo_id" class="form-control" required>
                            <option value="">-- Selecione --</option>
                            @foreach($calculos as $calculo)
                                <option value="{{ $calculo->id }}"
                                    {{ old('calculo_id', $formulario->calculo_id ?? '') == $calculo->id ? 'selected' : '' }}>
                                    {{ $calculo->nome }} - {{$calculo->formula}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                
                    <div class="form-group col-md-3">
                        <label for="score_ini">Score Inicial:</label>
                        <input type="text" name="score_ini" id="score_ini" class="form-control" value="{{ old('score_ini') }}" required>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="score_fim">Score Final:</label>
                        <input type="text" name="score_fim" id="score_fim" class="form-control" value="{{ old('score_fim') }}" required>
                    </div>

                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-default" style="width: 150px;">Salvar</button>
                <a href="{{ route('formularios.index') }}" class="btn btn-default" style="width: 150px;">Cancelar</a>
            </div>
        </form>
    </div>
@stop




@section('js')
    <!-- INCLUINDO SUMMERNOTE JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#descricao').summernote({
                placeholder: 'Descreva aqui o formulário...',
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

        $(document).ready(function() {
            $('#instrucoes').summernote({
                placeholder: 'Instruções sobre o questionário...',
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
@stop