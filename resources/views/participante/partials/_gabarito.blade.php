<div class="card">
    <div class="card-header border-0">
        <h2 class="card-title"><label class="badge badge-dark"> {{ $formulario->label }} </label> | {{ $formulario->nome }} | Resultado de respostas ao questionario </h2>
        <div class="card-tools">
            <a href="#" class="btn btn-tool btn-sm">
                <i class="fas fa-download"></i>
            </a>
            <a href="#" class="btn btn-tool btn-sm">
                <i class="fas fa-bars"></i>
            </a>
            <a href="#" class="btn btn-sm btn-tool">
                <i class="fa-solid fa-circle-info"></i>
            </a>
        </div>
    </div>
    <div class="card-body" style="box-shadow: none;">
        <div class="card mb-2 pl-4 pr-4 border-0" style="box-shadow: none;">
            <div class="card mb-2 pl-4 pr-4 border-0 text-center" style="box-shadow: none;">
                <h1>{{ $user->name }}</h1>
                <h4>{{ $user->email }}</h4>
            </div>
            <h5>Instruções</h5>
            <p>{!! $formulario->instrucoes !!}</p>
        </div>
        <div class="card mb-2 p-2" style="box-shadow: none;">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Pergunta</th>
                        <th>Variável</th>
                        <th>Resposta</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($formulario->perguntas as $pergunta)
                        @php
                            $resposta = $respostasUsuario->get($pergunta->id);
                        @endphp
                        <tr>
                            <td>
                                {{ $pergunta->numero_da_pergunta }} - {{ $pergunta->pergunta }}
                            </td>
                            <td>
                                @if($pergunta->variaveis->isNotEmpty())
                                    <label class="badge badge-dark">{{ $pergunta->variaveis->pluck('nome')->join(', ') }}</label>
                                @else
                                    <span class="text-muted">Nenhuma</span>
                                @endif
                            </td>
                            <td>
                                {{ $resposta->valor_resposta ?? 'Sem resposta' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer text-right">

    </div>
</div>