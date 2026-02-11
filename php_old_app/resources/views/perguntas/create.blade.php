@extends('adminlte::page')

@section('title', 'Incluir Pergunta')

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
                <h1>Incluir Pergunta</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/home">Início</a></li>
                    <li class="breadcrumb-item active">Perguntas</li>
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
                <i class="fa-solid fa-question" style="margin-right: 5px;"></i>
                Incluir Pergunta
            </h3>
        </div>

        <form action="{{ route('perguntas.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="row">

                    <div class="form-group col-md-12">
                        <label for="formulario_id">Formulário:</label>
                        <select name="formulario_id" id="formulario_id" class="form-control" required>
                            <option value="">-- Selecione --</option>
                            @foreach($formularios as $formulario)
                                <option value="{{ $formulario->id }}" 
                                    {{ old('formulario_id') == $formulario->id ? 'selected' : '' }}>
                                    {{ $formulario->label }} | {{ $formulario->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-2">
                        <label for="numero_da_pergunta">Nº Pergunta:</label>
                        <input type="text" name="numero_da_pergunta" id="numero_da_pergunta" class="form-control" value="{{ old('numero_da_pergunta') }}" required>
                    </div>

                    <div class="form-group col-md-10">
                        <label for="pergunta">Pergunta:</label>
                        <input type="text" name="pergunta" id="pergunta" class="form-control" value="{{ old('pergunta') }}" required>
                    </div>

                </div>

                {{-- <div class="form-group">
                    <label for="variavel_id">Variáveis:</label>
                    <select class="form-control select2" name="variavel_id[]" multiple="multiple" data-placeholder="Selecione as Variáveis" style="width: 100%;">
                        @foreach($variaveis as $variavel)
                            <option value="{{ $variavel->id }}" {{ (collect(old('variavel_id'))->contains($variavel->id)) ? 'selected' : '' }}>
                                {{ $variavel->nome }}
                            </option>
                        @endforeach
                    </select>
                </div> --}}



                <div class="form-group">
                    <label for="variavel_id">Variáveis:</label>
                    <select id="variavel_id" class="form-control select2" name="variavel_id[]" multiple="multiple" data-placeholder="Selecione as Variáveis" style="width: 100%;">
                        <!-- opções serão carregadas via JS -->
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

        $(".select2").select2({
            maximumSelectionLength: 3,
            language: {
                maximumSelected: function (e) {
                    return "Você pode selecionar no máximo " + e.maximum + " itens.";
                }
            }
        });

        $(document).ready(function() {
            $('.single2').select2({
                placeholder: 'Selecionar Cliente',
                allowClear: true
            });
        });
    </script>


<script>

    $('#formulario_id').on('change', function () {
        var formularioId = $(this).val();

        // limpa as opções atuais
        $('#variavel_id').empty();

        if (formularioId) {
            $.ajax({
                url: '/variaveis/formulario/' + formularioId,
                type: 'GET',
                success: function (data) {
                    data.forEach(function (variavel) {
                        $('#variavel_id').append(
                            $('<option>', {
                                value: variavel.id,
                                text: variavel.nome
                            })
                        );
                    });

                    $('#variavel_id').trigger('change'); // força o select2 a atualizar
                }
            });
        }
    });
</script>
@stop
