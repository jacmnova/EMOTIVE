@extends('adminlte::page')

@section('title', 'Editar Pergunta')

@section('css')
    {{-- <link rel="stylesheet" href="../css/utils.css"> --}}

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
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Editar Pergunta</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="/home">Início</a></li>
                <li class="breadcrumb-item"><a href="{{ route('perguntas.index') }}">Perguntas</a></li>
                <li class="breadcrumb-item active">Editar</li>
            </ol>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fa-solid fa-edit" style="margin-right: 5px;"></i>
            Editar Pergunta
        </h3>
    </div>

    <form action="{{ route('perguntas.update', $pergunta->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="row">

                <div class="form-group col-md-12">
                    <label for="formulario_id">Formulário:</label>
                    <select name="formulario_id" id="formulario_id" class="form-control" required>
                        <option value="">-- Selecione --</option>
                        @foreach($formularios as $formulario)
                            <option value="{{ $formulario->id }}" 
                                {{ old('formulario_id', $pergunta->formulario_id) == $formulario->id ? 'selected' : '' }}>
                                {{ $formulario->label }} | {{ $formulario->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>

            <div class="row">
                <div class="form-group col-md-2">
                    <label for="numero_da_pergunta">Nº Pergunta:</label>
                    <input type="text" name="numero_da_pergunta" id="numero_da_pergunta"
                           class="form-control" value="{{ old('numero_da_pergunta', $pergunta->numero_da_pergunta) }}" required>
                </div>

                <div class="form-group col-md-10">
                    <label for="pergunta">Pergunta:</label>
                    <input type="text" name="pergunta" id="pergunta" class="form-control" value="{{ old('pergunta', $pergunta->pergunta) }}" required>
                </div>
            </div>

            <div class="form-group">
                <label for="variavel_id">Variáveis:</label>
                <select id="variavel_id" class="form-control select2" name="variavel_id[]" multiple="multiple" data-placeholder="Selecione as Variáveis" style="width: 100%;">
                    <!-- preenchido via JS -->
                </select>
            </div>

        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-default" style="width: 150px;">Salvar</button>
            <a href="{{ route('perguntas.index') }}" class="btn btn-default" style="width: 150px;">Cancelar</a>
        </div>
    </form>
</div>
@stop

@section('js')
    <script>

        // Inicializa o select2 para a seleção de variáveis
        $(".select2").select2({
            maximumSelectionLength: 6,
            language: {
                maximumSelected: function (e) {
                    return "Você pode selecionar no máximo " + e.maximum + " itens.";
                }
            }
        });

        document.addEventListener("DOMContentLoaded", function(){
            document.getElementById('formulario_id').dispatchEvent(new Event('change'));
        });
    </script>

    <script>
        const selectedVariaveis = @json(old('variavel_id', $pergunta->variaveis->pluck('id')->toArray()));

        function carregarVariaveis(formularioId, selected = []) {
            $('#variavel_id').empty();

            if (formularioId) {
                $.get('/variaveis/formulario/' + formularioId, function (data) {
                    data.forEach(function (variavel) {
                        let option = $('<option>', {
                            value: variavel.id,
                            text: variavel.nome,
                            selected: selected.includes(variavel.id)
                        });

                        $('#variavel_id').append(option);
                    });

                    $('#variavel_id').trigger('change');
                });
            }
        }

        $(document).ready(function () {
            $(".select2").select2({
                maximumSelectionLength: 3,
                language: {
                    maximumSelected: function (e) {
                        return "Você pode selecionar no máximo " + e.maximum + " itens.";
                    }
                }
            });

            let formularioId = $('#formulario_id').val();
            carregarVariaveis(formularioId, selectedVariaveis);

            $('#formulario_id').on('change', function () {
                carregarVariaveis($(this).val(), []);
            });
        });
    </script>


@stop
