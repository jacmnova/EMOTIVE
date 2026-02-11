<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fa-solid fa-layer-group" style="margin-right: 5px;"></i>
            Etapas do Formulário
        </h3>
    </div>

    <div class="card-body">
        {{-- Formulário para adicionar etapas --}}
        <form action="{{ route('etapas.adicionar') }}" method="POST" class="mb-3">
            @csrf
            <input type="hidden" name="formulario_id" value="{{ $formulario->id }}">
            <div class="row">
                <div class="form-group col-md-2">
                    <label for="etapa">Etapa</label>
                    <input type="number" name="etapa" id="etapa" class="form-control" required>
                </div>
                <div class="form-group col-md-2">
                    <label for="de">De</label>
                    <input type="number" name="de" id="de" class="form-control" required>
                </div>
                <div class="form-group col-md-2">
                    <label for="ate">Até</label>
                    <input type="number" name="ate" id="ate" class="form-control" required>
                </div>
                <div class="form-group col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-dark">
                        <i class="fa-solid fa-plus"></i> Adicionar Etapa
                    </button>
                </div>
            </div>
        </form>

        {{-- Tabela de etapas já salvas --}}
        @if($formulario->etapas->isNotEmpty())
            <table class="table table-striped mt-3">
                <thead>
                    <tr>
                        <th>Etapa</th>
                        <th>De</th>
                        <th>Até</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($formulario->etapas as $etapa)
                        <tr>
                            <td>{{ $etapa->etapa }}</td>
                            <td>{{ $etapa->de }}</td>
                            <td>{{ $etapa->ate }}</td>
                            <td>
                                <form action="{{ route('etapas.remover', $etapa->id) }}" method="POST" onsubmit="return confirm('Remover esta etapa?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">
                                        <i class="fa-solid fa-trash"></i> Remover
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="callout callout-warning">
                <h5><i class="fa-solid fa-circle-info"></i> Nenhuma etapa cadastrada.</h5>
                <p>Adicione as etapas para este formulário usando o formulário acima.</p>
            </div>
        @endif
    </div>
</div>
