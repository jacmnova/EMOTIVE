<form id="myForm" action="{{ route('usuario_formulario.store') }}" method="POST">
    @csrf
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa-solid fa-building-user" style="margin-right: 5px;"></i> Incluir Questionários</h3>
        </div>
        <div class="card-body">
            <div class="row">

                <input name="usuario_id" value="{{ $usuario->id }}" hidden>

                <div class="form-group col-md-8">
                    <label for="formulario_id">Questionários:</label>
                    <select name="formulario_id" id="formulario_id" class="form-control select2" required>
                        <option value="">-- Selecione --</option>
                        @foreach($formularios as $formulario)
                            <option value="{{ $formulario->formulario->id }}">
                                {{ $formulario->formulario->label }} | {{ $formulario->formulario->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Botão com ação personalizada --}}
                <div class="form-group col-md-4" style="margin-top: 32px;">
                    <button type="button" class="btn btn-default btn-block" onclick="confirmSubmit()">Incluir</button>
                </div>

            </div>
        </div>
    </div>
</form>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fa-solid fa-building-user" style="margin-right: 5px;"></i>Questionários</h3>
    </div>

    <div class="card-body mr-1">
        <table class="table datatable dtr-inline mr-1 ml-1">
            <thead>
                <tr>
                    <th style="width: 80%">Formulário</th>
                    <th style="width: 20%;text-align: center;">
                        Status
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach($questionarios as $questionario)
                    <tr>
                        <td>
                            @if($questionario->formulario)
                                <label class="badge badge-dark"> {{ $questionario->formulario->label }} </label> | {{ $questionario->formulario->nome }}
                            @else
                                <span class="text-muted">Formulário não disponível</span>
                            @endif
                        </td>

                        <td style="text-align: center;">
                            @if($questionario->status == 'novo')
                                <label class="badge badge-info">NOVO</label>
                            @elseif($questionario->status == 'pendente')
                                <label class="badge badge-warning">PENDENTE</label>
                            @else
                                <label class="badge badge-success">COMPLETO</label>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    function confirmSubmit() {
        Swal.fire({
            title: 'Liberar Questionário para Usuário',
            text: 'Esta ação irá liberar o questionário para o usuário selecionado. Esta ação não é reversível. Você tem certeza?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#008ca5',
            cancelButtonColor: '#5fc3b4',
            confirmButtonText: 'Sim, fazer isso!',
            cancelButtonText: 'Cancelar',
            iconHtml: '<i class="fa-solid fa-exclamation-circle text-danger" style="font-size: 1.5em;"></i>',
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('myForm').submit();
            }
        });
    }
</script>
