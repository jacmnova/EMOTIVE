@if(isset($analiseTexto))
    <div class="card">
        <div class="card-header border-0">
            <h2 class="card-title">
                <label class="badge badge-dark"> Análise Geral </label> | Saúde Emocional
                @if(isset($analiseData))
                    <small class="text-muted" style="margin-left: 10px;color: silver;">
                        Gerado em {{ \Carbon\Carbon::parse($analiseData)->format('d/m/Y \à\s H:i') }}
                    </small>
                @endif
            </h2>
            <div class="card-tools">
                <a href="#" class="btn btn-tool btn-sm"><i class="fas fa-download"></i></a>
                <a href="#" class="btn btn-tool btn-sm"><i class="fas fa-bars"></i></a>
                <a href="#" class="btn btn-sm btn-tool"><i class="fa-solid fa-circle-info"></i></a>


                @if(Auth::user()->admin)
                    <form method="POST" action="{{ route('relatorio.regenerar') }}" class="d-inline">
                        @csrf
                        <input type="hidden" name="formulario_id" value="{{ $formulario->id }}">
                        <input type="hidden" name="usuario_id" value="{{ $user->id }}">
                        <button type="submit" class="btn btn-sm btn-tool" title="Regenerar Análise">
                            <i class="fas fa-rotate-right"></i>
                        </button>
                    </form>
                @endif

            </div>
        </div>

        <div class="card-body">
            @if(Str::startsWith($analiseTexto, 'Erro ao gerar análise'))
                <div class="alert alert-warning" role="alert">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    {{ $analiseTexto }}
                </div>
            @else
                <div class="mb-4 p-3 border rounded">
                    <p style="white-space: pre-wrap; font-size: 16px;">
                        {{ $analiseTexto }}
                    </p>
                </div>
            @endif
        </div>
    </div>
@endif
