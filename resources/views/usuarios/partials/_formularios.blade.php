<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fa-solid fa-file-alt mr-2"></i>
            Formulários
        </h3>
        <div class="card-tools d-flex align-items-center">
            <a href="#" class="btn btn-sm btn-tool d-sm-inline-block" title="Mais Informações">
                <i class="fas fa-bars"></i>
            </a>
        </div>
    </div>

    @if ($questionarios->count() > 0)
        {{-- TABELA DESKTOP --}}
        <div class="card-body d-none d-md-block">
            <table class="table datatable table-striped dtr-inline">
                <thead>
                    <tr>
                        <th style="width: 20%">Nome</th>
                        <th style="width: 10%">Questões</th>
                        <th style="width: 10%">Habilitado em</th>
                        <th style="width: 10%">Status</th>
                        <th style="width: 5%">Relatório</th>
                        <th style="width: 15%">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($questionarios as $formulario)
                        <tr>
                            <td><label class="badge badge-dark">{{ $formulario->formulario->label }}</label> | {{ $formulario->formulario->nome }}</td>
                            <td>{{ $formulario->formulario->perguntaCount() }}</td>
                            <td>{{ $formulario->created_at->translatedFormat('d \d\e F \d\e Y \à\s H:i') }}</td>
                            <td>
                                @if($formulario->status == 'novo')
                                    <label class="badge badge-primary">{{ strtoupper($formulario->status) }}</label>
                                @elseif($formulario->status == 'pendente')
                                    <label class="badge badge-warning">{{ strtoupper($formulario->status) }}</label>
                                @else
                                    <label class="badge badge-success">{{ strtoupper($formulario->status) }}</label>
                                @endif
                            </td>
                            <td>
                                @if($formulario->status === 'completo' || (Auth::user()->admin && $formulario->status === 'pendente'))
                                    <a href="{{ route('relatorio.show', ['formulario_id' => $formulario->formulario_id, 'usuario_id' => $usuario->id]) }}" class="btn btn-sm btn-tool" title="Relatório">
                                        <i class="fa-regular fa-rectangle-list" style="color: #008ca5"></i>
                                    </a>
                                    <a href="{{ route('relatorio.pdf', ['user' => $usuario->id, 'formulario' => $formulario->formulario_id]) }}" class="btn btn-sm btn-tool" target="_blank">
                                        <i class="fas fa-file-pdf" style="color: #008ca5"></i>
                                    </a>
                                    @if(Auth::user()->admin)
                                        <form method="POST" action="{{ route('relatorio.regenerar') }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="formulario_id" value="{{ $formulario->formulario_id }}">
                                            <input type="hidden" name="usuario_id" value="{{ $usuario->id }}">
                                            <button type="submit" class="btn btn-sm btn-tool" title="Regenerar con Nueva Estructura JSON">
                                                <i class="fas fa-rotate-right" style="color: #28a745"></i>
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if($formulario->status !== 'completo')
                                    <a href="#" class="btn btn-sm btn-tool" title="Iniciar">
                                        <i class="fa-solid fa-stopwatch" style="color: #008ca5"></i>
                                    </a>
                                    <span class="small d-block">Última: {{ $formulario->updated_at->translatedFormat('d \d\e F \d\e Y \à\s H:i') }}</span>
                                @else
                                    <a href="#" class="btn btn-sm btn-tool" title="Formulário finalizado!">
                                        <i class="fa-solid fa-check-double" style="color:rgb(46, 134, 11)"></i>
                                    </a>
                                    <span class="small d-block">Finalizado em: {{ $formulario->updated_at->translatedFormat('d \d\e F \d\e Y \à\s H:i') }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- CARDS MOBILE --}}
        <div class="card-body d-block d-md-none">
            @foreach ($questionarios as $formulario)
                <div class="card mb-3 shadow-sm">
                    <div class="card-body">
                        <h5 class="mb-2">
                            <label class="badge badge-dark">{{ $formulario->formulario->label }}</label> {{ $formulario->formulario->nome }}
                        </h5>
                        <p class="mb-1"><strong>Questões:</strong> {{ $formulario->formulario->perguntaCount() }}</p>
                        <p class="mb-1"><strong>Habilitado em:</strong> {{ $formulario->created_at->translatedFormat('d \d\e F \d\e Y \à\s H:i') }}</p>
                        <p class="mb-1">
                            <strong>Status:</strong>
                            @if($formulario->status == 'novo')
                                <span class="badge badge-primary">{{ strtoupper($formulario->status) }}</span>
                            @elseif($formulario->status == 'pendente')
                                <span class="badge badge-warning">{{ strtoupper($formulario->status) }}</span>
                            @else
                                <span class="badge badge-success">{{ strtoupper($formulario->status) }}</span>
                            @endif
                        </p>
                        @if($formulario->status === 'completo' || (Auth::user()->admin && $formulario->status === 'pendente'))
                            <div class="my-2">
                                <a href="{{ route('relatorio.show', ['formulario_id' => $formulario->formulario_id, 'usuario_id' => $usuario->id]) }}" class="btn btn-sm btn-tool" title="Relatório">
                                    <i class="fa-regular fa-rectangle-list" style="color: #008ca5"></i>
                                </a>
                                <a href="{{ route('relatorio.pdf', ['user' => $usuario->id, 'formulario' => $formulario->formulario_id]) }}" class="btn btn-sm btn-tool" target="_blank">
                                    <i class="fas fa-file-pdf" style="color: #008ca5"></i>
                                </a>
                                @if(Auth::user()->admin)
                                    <form method="POST" action="{{ route('relatorio.regenerar') }}" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="formulario_id" value="{{ $formulario->formulario_id }}">
                                        <input type="hidden" name="usuario_id" value="{{ $usuario->id }}">
                                        <button type="submit" class="btn btn-sm btn-tool" title="Regenerar con Nueva Estructura JSON">
                                            <i class="fas fa-rotate-right" style="color: #28a745"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                            <p class="small text-muted">
                                @if($formulario->status === 'completo')
                                    Finalizado em: {{ $formulario->updated_at->translatedFormat('d \d\e F \d\e Y \à\s H:i') }}
                                @else
                                    Pendente - Última atualização: {{ $formulario->updated_at->translatedFormat('d \d\e F \d\e Y \à\s H:i') }}
                                @endif
                            </p>
                        @else
                            <div class="my-2">
                                <a href="#" class="btn btn-sm btn-tool" title="Iniciar">
                                    <i class="fa-solid fa-stopwatch" style="color: #008ca5"></i>
                                </a>
                            </div>
                            <p class="small text-muted">Última atualização em: {{ $formulario->updated_at->translatedFormat('d \d\e F \d\e Y \à\s H:i') }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="row" style="margin: 20px;">
            <div class="callout callout-warning">
                <h5><i class="fa-solid fa-circle-info"></i> Nenhum Formulário foi encontrado.</h5>
                <p>Entre em contato com seu gestor para habilitar um novo formulário</p>
            </div>
        </div>
    @endif
</div>
