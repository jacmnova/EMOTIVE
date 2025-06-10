@if(isset($analiseHtml))
    <div class="card">
        <div class="card-header border-0">
            <h2 class="card-title">
                <label class="badge badge-dark"> Análise Geral </label> | Saúde Emocional

                @if(isset($analiseData))
                    <small class="text-muted ml-2" style="color: silver;">
                        Gerado em {{ \Carbon\Carbon::parse($analiseData)->format('d/m/Y \à\s H:i') }}
                    </small>
                @endif
            </h2>

            <div class="card-tools d-flex">

                @if(Auth::user()->admin)
                    <form method="POST" action="{{ route('relatorio.regenerar') }}" class="d-inline ml-1">
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
                <div class="mb-4 p-3 border rounded" style="font-size: 16px;">
                    {!! $analiseHtml !!}
                </div>
            @endif
        </div>
    </div>
@endif
