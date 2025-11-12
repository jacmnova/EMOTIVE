<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Analise;
use App\Models\Resposta;
use App\Models\Variavel;
use App\Models\Formulario;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Traits\CalculaEjesAnaliticos;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class RelatorioController extends Controller
{
    use CalculaEjesAnaliticos;
    public function regenerarAnalise(Request $request)
    {
        $request->validate([
            'formulario_id' => ['required', 'integer', 'exists:formularios,id'],
            'usuario_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        if (!auth()->user()->admin) {
            return redirect()->back()->with('msgError', 'Ação não autorizada.');
        }

        // Remove análise anterior
        Analise::where('user_id', $request->usuario_id)
            ->where('formulario_id', $request->formulario_id)
            ->delete();

        return redirect()->route('relatorio.show', [
            'formulario_id' => $request->formulario_id,
            'usuario_id' => $request->usuario_id,
        ])->with('msgSuccess', 'Análise regenerada com sucesso.');
    }

    /**
     * Mostrar relatorio con token temporal para PDF
     */
    public function relatorioPDFTemp(Request $request, $token)
    {
        // Validar token desde cache
        $cacheKey = "pdf_token_{$token}";
        $data = Cache::get($cacheKey);
        
        if (!$data) {
            abort(404, 'Token inválido ou expirado');
        }
        
        $formularioId = $data['formulario_id'];
        $usuarioId = $data['usuario_id'];
        
        // Usar el mismo método que relatorioShow pero sin autenticación
        $user = User::findOrFail($usuarioId);
        $formulario = Formulario::with('perguntas.variaveis')->findOrFail($formularioId);

        $respostasUsuario = Resposta::where('user_id', $user->id)
            ->whereIn('pergunta_id', $formulario->perguntas->pluck('id'))
            ->get()
            ->keyBy('pergunta_id');

        // Cargar variables con preguntas, asegurando que se carguen todos los campos de las preguntas
        $variaveis = Variavel::with(['perguntas' => function($query) {
            $query->select('perguntas.id', 'perguntas.formulario_id', 'perguntas.numero_da_pergunta', 'perguntas.pergunta');
        }])
            ->where('formulario_id', $formulario->id)
            ->get();

        $pontuacoes = [];
        foreach ($variaveis as $variavel) {
            $pontuacao = 0;
            $totalRespostas = 0;
            
            if ($variavel->perguntas->isEmpty()) {
                continue;
            }
            
            foreach ($variavel->perguntas as $pergunta) {
                $resposta = $respostasUsuario->get($pergunta->id);
                $valorResposta = $this->obterValorRespostaComInversao($resposta, $pergunta);
                if ($valorResposta !== null) {
                    $pontuacao += $valorResposta;
                    $totalRespostas++;
                }
            }
            
            if ($totalRespostas === 0) {
                continue;
            }

            $faixa = $this->classificarPontuacao($pontuacao, $variavel);
            switch ($faixa) {
                case 'Baixa':
                    $recomendacao = $variavel->r_baixa;
                    $badge = 'info';
                    break;
                case 'Moderada':
                    $recomendacao = $variavel->r_moderada;
                    $badge = 'warning';
                    break;
                case 'Alta':
                    $recomendacao = $variavel->r_alta;
                    $badge = 'danger';
                    break;
                default:
                    $recomendacao = 'Sem dados.';
                    $badge = 'secondary';
                    break;
            }

            $pontuacoes[] = [
                'tag' => strtoupper($variavel->tag),
                'nome' => $variavel->nome,
                'valor' => $pontuacao,
                'faixa' => $faixa,
                'recomendacao' => $recomendacao,
                'badge' => $badge,
            ];
        }

        $analise = Analise::where('user_id', $usuarioId)
            ->where('formulario_id', $formularioId)
            ->first();

        $analiseTexto = $analise?->texto ?? 'Análise não disponível.';
        $analiseData = $analise?->created_at;
        $analiseHtml = nl2br(e($analiseTexto));
        $analiseHtml = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $analiseHtml);
        $analiseHtml = preg_replace('/###\s?(.*)/', '<h4>$1</h4>', $analiseHtml);

        // === CALCULAR ÍNDICES EE, PR, SO DIRECTAMENTE DESDE RESPOSTAS ===
        $indices = $this->calcularIndicesDesdeRespostas($respostasUsuario, $formulario->id);
        
        $pontuacoesParaCalculo = [];
        foreach ($pontuacoes as $ponto) {
            $pontuacoesParaCalculo[] = [
                'tag' => $ponto['tag'],
                'valor' => $ponto['valor'],
                'faixa' => $ponto['faixa']
            ];
        }
        $ejesAnaliticos = $this->calcularEjesAnaliticos($pontuacoesParaCalculo, $indices);
        $iid = $this->calcularIID($ejesAnaliticos);
        $nivelRisco = $this->determinarNivelRisco($iid);
        $planDesenvolvimento = $this->getPlanDesenvolvimento($nivelRisco);
        
        // Calcular promedio de índices (sin porcentaje) para mostrar en la puntuación
        $promedioIndices = ($indices['EE'] + $indices['PR'] + $indices['SO']) / 3;

        return view('participante.relatorio_emotive', compact(
            'formulario',
            'respostasUsuario',
            'pontuacoes',
            'variaveis',
            'user',
            'analiseTexto',
            'analiseHtml',
            'analiseData',
            'ejesAnaliticos',
            'iid',
            'promedioIndices',
            'nivelRisco',
            'planDesenvolvimento'
        ));
    }

    /**
     * Mostrar relatorio limpio para captura de imagen (sin layout adminlte)
     */
    public function relatorioPDFCaptureTemp(Request $request, $token)
    {
        // Validar token desde cache
        $cacheKey = "pdf_capture_token_{$token}";
        $data = Cache::get($cacheKey);
        
        if (!$data) {
            abort(404, 'Token inválido ou expirado');
        }
        
        $formularioId = $data['formulario_id'];
        $usuarioId = $data['usuario_id'];
        
        $user = User::findOrFail($usuarioId);
        $formulario = Formulario::with('perguntas.variaveis')->findOrFail($formularioId);

        $respostasUsuario = Resposta::where('user_id', $user->id)
            ->whereIn('pergunta_id', $formulario->perguntas->pluck('id'))
            ->get()
            ->keyBy('pergunta_id');

        $variaveis = Variavel::with(['perguntas' => function($query) {
            $query->select('perguntas.id', 'perguntas.formulario_id', 'perguntas.numero_da_pergunta', 'perguntas.pergunta');
        }])
            ->where('formulario_id', $formulario->id)
            ->get();

        $pontuacoes = [];
        foreach ($variaveis as $variavel) {
            $pontuacao = 0;
            $totalRespostas = 0;
            
            if ($variavel->perguntas->isEmpty()) {
                continue;
            }
            
            foreach ($variavel->perguntas as $pergunta) {
                $resposta = $respostasUsuario->get($pergunta->id);
                $valorResposta = $this->obterValorRespostaComInversao($resposta, $pergunta);
                if ($valorResposta !== null) {
                    $pontuacao += $valorResposta;
                    $totalRespostas++;
                }
            }
            
            if ($totalRespostas === 0) {
                continue;
            }

            // Calcular máximo posible y máximo del gráfico
            $maximoPosible = $totalRespostas * 6;
            $maximoGrafico = $maximoPosible > 100 ? 200 : 100;

            $faixa = $this->classificarPontuacao($pontuacao, $variavel);
            switch ($faixa) {
                case 'Baixa':
                    $recomendacao = $variavel->r_baixa;
                    $badge = 'info';
                    break;
                case 'Moderada':
                    $recomendacao = $variavel->r_moderada;
                    $badge = 'warning';
                    break;
                case 'Alta':
                    $recomendacao = $variavel->r_alta;
                    $badge = 'danger';
                    break;
                default:
                    $recomendacao = 'Sem dados.';
                    $badge = 'secondary';
                    break;
            }

            $pontuacoes[] = [
                'tag' => strtoupper($variavel->tag),
                'nome' => $variavel->nome,
                'valor' => $pontuacao,
                'pontuacao' => $pontuacao,
                'maximo_grafico' => $maximoGrafico,
                'faixa' => $faixa,
                'recomendacao' => $recomendacao,
                'badge' => $badge,
            ];
        }

        $analise = Analise::where('user_id', $usuarioId)
            ->where('formulario_id', $formularioId)
            ->first();

        $analiseTexto = $analise?->texto ?? 'Análise não disponível.';
        $analiseData = $analise?->created_at;
        $analiseHtml = nl2br(e($analiseTexto));
        $analiseHtml = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $analiseHtml);
        $analiseHtml = preg_replace('/###\s?(.*)/', '<h4>$1</h4>', $analiseHtml);

        // === CALCULAR ÍNDICES EE, PR, SO DIRECTAMENTE DESDE RESPOSTAS ===
        $indices = $this->calcularIndicesDesdeRespostas($respostasUsuario, $formulario->id);
        
        $pontuacoesParaCalculo = [];
        foreach ($pontuacoes as $ponto) {
            $pontuacoesParaCalculo[] = [
                'tag' => $ponto['tag'],
                'valor' => $ponto['valor'],
                'faixa' => $ponto['faixa']
            ];
        }
        $ejesAnaliticos = $this->calcularEjesAnaliticos($pontuacoesParaCalculo, $indices);
        $iid = $this->calcularIID($ejesAnaliticos);
        $nivelRisco = $this->determinarNivelRisco($iid);
        $planDesenvolvimento = $this->getPlanDesenvolvimento($nivelRisco);
        
        // Calcular promedio de índices (sin porcentaje) para mostrar en la puntuación
        $promedioIndices = ($indices['EE'] + $indices['PR'] + $indices['SO']) / 3;

        return view('participante.relatorio_emotive_capture', compact(
            'formulario',
            'respostasUsuario',
            'pontuacoes',
            'variaveis',
            'user',
            'analiseTexto',
            'analiseHtml',
            'analiseData',
            'ejesAnaliticos',
            'iid',
            'promedioIndices',
            'nivelRisco',
            'planDesenvolvimento'
        ));
    }

    /**
     * Mostrar relatorio web completo (con AdminLTE y todos los estilos) para captura PDF
     * Esta es la vista EXACTA que se ve en la web
     */
    public function relatorioPDFWebTemp(Request $request, $token)
    {
        // Validar token desde cache
        $cacheKey = "pdf_web_token_{$token}";
        $data = Cache::get($cacheKey);
        
        if (!$data) {
            abort(404, 'Token inválido ou expirado');
        }
        
        $formularioId = $data['formulario_id'];
        $usuarioId = $data['usuario_id'];
        
        $user = User::findOrFail($usuarioId);
        $formulario = Formulario::with('perguntas.variaveis')->findOrFail($formularioId);

        $respostasUsuario = Resposta::where('user_id', $user->id)
            ->whereIn('pergunta_id', $formulario->perguntas->pluck('id'))
            ->get()
            ->keyBy('pergunta_id');

        $variaveis = Variavel::with(['perguntas' => function($query) {
            $query->select('perguntas.id', 'perguntas.formulario_id', 'perguntas.numero_da_pergunta', 'perguntas.pergunta');
        }])
            ->where('formulario_id', $formulario->id)
            ->get();

        // Calcular puntuaciones usando la misma lógica que relatorioShow
        $pontuacoes = [];
        
        // Para EXTR, necesitamos contar 16 preguntas según CSV MAX
        $preguntaDuplicadaEXTR = \App\Models\Pergunta::where('formulario_id', $formularioId)
            ->where('pergunta', 'like', '%Recebo novas demandas antes de conseguir concluir%')
            ->first();
        $preguntaDuplicadaEXTRId = $preguntaDuplicadaEXTR ? $preguntaDuplicadaEXTR->id : null;
        
        foreach ($variaveis as $variavel) {
            $pontuacao = 0;
            $totalRespostas = 0;
            
            if ($variavel->perguntas->isEmpty()) {
                continue;
            }
            
            $esEXTR = (strtoupper($variavel->tag ?? '') === 'EXTR');
            
            foreach ($variavel->perguntas as $pergunta) {
                $contarDosVeces = ($esEXTR && $preguntaDuplicadaEXTRId && $pergunta->id == $preguntaDuplicadaEXTRId);
                $resposta = $respostasUsuario->get($pergunta->id);
                
                if (!$resposta || $resposta->valor_resposta === null) {
                    continue;
                }
                
                $valorResposta = $this->obterValorRespostaComInversao($resposta, $pergunta);
                if ($valorResposta !== null) {
                    if ($contarDosVeces) {
                        $pontuacao += $valorResposta * 2;
                        $totalRespostas += 2;
                    } else {
                        $pontuacao += $valorResposta;
                        $totalRespostas++;
                    }
                }
            }
            
            if ($totalRespostas === 0) {
                continue;
            }

            $maximoPosible = $totalRespostas * 6;
            $maximoGrafico = $maximoPosible > 100 ? 200 : 100;

            $faixa = $this->classificarPontuacao($pontuacao, $variavel);
            switch ($faixa) {
                case 'Baixa':
                    $recomendacao = $variavel->r_baixa;
                    $badge = 'info';
                    break;
                case 'Moderada':
                    $recomendacao = $variavel->r_moderada;
                    $badge = 'warning';
                    break;
                case 'Alta':
                    $recomendacao = $variavel->r_alta;
                    $badge = 'danger';
                    break;
                default:
                    $recomendacao = 'Sem dados.';
                    $badge = 'secondary';
                    break;
            }

            $tagMap = [
                'EXEM' => 'EXEM',
                'REPR' => 'REPR',
                'DECI' => 'DECI',
                'FAPS' => 'FAPS',
                'EXTR' => 'EXTR',
                'ASMO' => 'ASMO',
            ];
            $tagFinal = $tagMap[strtoupper($variavel->tag ?? '')] ?? strtoupper($variavel->tag ?? '');

            $pontuacoes[] = [
                'tag' => $tagFinal,
                'nome' => $variavel->nome ?? 'Sin nombre',
                'valor' => $pontuacao,
                'maximo_grafico' => $maximoGrafico,
                'faixa' => $faixa,
                'recomendacao' => $recomendacao,
                'badge' => $badge,
                'b' => $variavel->B ?? 0,
                'm' => $variavel->M ?? 0,
                'a' => $variavel->A ?? 0,
            ];
        }

        $analise = Analise::where('user_id', $usuarioId)
            ->where('formulario_id', $formularioId)
            ->first();

        $analiseTexto = $analise?->texto ?? 'Análise não disponível.';
        $analiseData = $analise?->created_at;
        $analiseHtml = nl2br(e($analiseTexto));
        $analiseHtml = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $analiseHtml);
        $analiseHtml = preg_replace('/###\s?(.*)/', '<h4>$1</h4>', $analiseHtml);

        // === CALCULAR ÍNDICES EE, PR, SO DIRECTAMENTE DESDE RESPOSTAS ===
        $indices = $this->calcularIndicesDesdeRespostas($respostasUsuario, $formulario->id);
        
        $pontuacoesParaCalculo = [];
        foreach ($pontuacoes as $ponto) {
            $pontuacoesParaCalculo[] = [
                'tag' => $ponto['tag'],
                'valor' => $ponto['valor'],
                'faixa' => $ponto['faixa']
            ];
        }
        $ejesAnaliticos = $this->calcularEjesAnaliticos($pontuacoesParaCalculo, $indices);
        $iid = $this->calcularIID($ejesAnaliticos);
        $nivelRisco = $this->determinarNivelRisco($iid);
        $planDesenvolvimento = $this->getPlanDesenvolvimento($nivelRisco);
        
        $promedioIndices = ($indices['EE'] + $indices['PR'] + $indices['SO']) / 3;

        // Renderizar la vista web COMPLETA (con AdminLTE y todos los estilos)
        // Esta es la vista EXACTA que se ve en la web
        return view('participante.relatorio_emotive', compact(
            'formulario',
            'respostasUsuario',
            'pontuacoes',
            'variaveis',
            'user',
            'analiseTexto',
            'analiseHtml',
            'analiseData',
            'ejesAnaliticos',
            'iid',
            'promedioIndices',
            'nivelRisco',
            'planDesenvolvimento'
        ));
    }

    /**
     * Mostrar relatorio para PDF (solo contenido dentro de div.content)
     */
    public function relatorioPDF(Request $request)
    {
        try {
            $validated = $request->validate([
                'formulario_id' => ['required', 'integer', 'exists:formularios,id'],
                'usuario_id' => ['required', 'integer', 'exists:users,id'],
            ]);

            $formularioId = $validated['formulario_id'];
            $usuarioId = $validated['usuario_id'];

            \Log::info('Accediendo a relatorioPDF', [
                'formulario_id' => $formularioId,
                'usuario_id' => $usuarioId
            ]);

            // Reutilizar la lógica de relatorioShow del DadosController
            // Para simplificar, vamos a llamar directamente a la misma lógica
            $user = User::findOrFail($usuarioId);
            $formulario = Formulario::with('perguntas')->findOrFail($formularioId);

        $perguntaIds = $formulario->perguntas->pluck('id')->toArray();
        $respostasUsuario = Resposta::where('user_id', $user->id)
            ->whereIn('pergunta_id', $perguntaIds)
            ->get()
            ->keyBy('pergunta_id');

        $variaveis = Variavel::with(['perguntas' => function($query) {
            $query->select('perguntas.id', 'perguntas.formulario_id', 'perguntas.numero_da_pergunta', 'perguntas.pergunta');
        }])
            ->where('formulario_id', $formulario->id)
            ->get();

        // Calcular puntuaciones (reutilizar lógica similar a relatorioShow)
        $pontuacoes = [];
        $preguntaDuplicadaEXTR = \App\Models\Pergunta::where('formulario_id', $formularioId)
            ->where('pergunta', 'like', '%Recebo novas demandas antes de conseguir concluir%')
            ->first();
        $preguntaDuplicadaEXTRId = $preguntaDuplicadaEXTR ? $preguntaDuplicadaEXTR->id : null;

        foreach ($variaveis as $variavel) {
            $pontuacao = 0;
            $totalRespostas = 0;
            
            if ($variavel->perguntas->isEmpty()) {
                continue;
            }
            
            $esEXTR = (strtoupper($variavel->tag ?? '') === 'EXTR');
            
            foreach ($variavel->perguntas as $pergunta) {
                $contarDosVeces = ($esEXTR && $preguntaDuplicadaEXTRId && $pergunta->id == $preguntaDuplicadaEXTRId);
                $resposta = $respostasUsuario->get($pergunta->id);
                
                if (!$resposta || $resposta->valor_resposta === null) {
                    continue;
                }
                
                // Usar el trait CalculaEjesAnaliticos que tiene obterValorRespostaComInversao
                $valorResposta = $this->obterValorRespostaComInversao($resposta, $pergunta);
                if ($valorResposta !== null) {
                    if ($contarDosVeces) {
                        $pontuacao += $valorResposta * 2;
                        $totalRespostas += 2;
                    } else {
                        $pontuacao += $valorResposta;
                        $totalRespostas++;
                    }
                }
            }
            
            if ($totalRespostas === 0) {
                continue;
            }

            $b = is_numeric($variavel->B) ? (float)$variavel->B : 0;
            $m = is_numeric($variavel->M) ? (float)$variavel->M : 0;
            $a = is_numeric($variavel->A) ? (float)$variavel->A : ($m + max(0, ($m - $b)));
            $pontuacaoNumerica = is_numeric($pontuacao) ? (float)$pontuacao : 0;
            $maximoPosible = $totalRespostas * 6;
            $maximoGrafico = $maximoPosible > 100 ? 200 : 100;
            
            $faixa = $this->classificarPontuacao($pontuacaoNumerica, $variavel);
            switch ($faixa) {
                case 'Baixa':
                    $recomendacao = $variavel->r_baixa ?? 'Sem dados.';
                    $badge = 'info';
                    break;
                case 'Moderada':
                    $recomendacao = $variavel->r_moderada ?? 'Sem dados.';
                    $badge = 'warning';
                    break;
                case 'Alta':
                    $recomendacao = $variavel->r_alta ?? 'Sem dados.';
                    $badge = 'danger';
                    break;
                default:
                    $recomendacao = 'Sem dados.';
                    $badge = 'secondary';
                    break;
            }

            $tagMapeado = strtoupper($variavel->tag ?? '');
            $tagMap = [
                'EXEM' => 'EXEM',
                'REPR' => 'REPR',
                'DECI' => 'DECI',
                'FAPS' => 'FAPS',
                'EXTR' => 'EXTR',
                'ASMO' => 'ASMO',
            ];
            $tagFinal = $tagMap[$tagMapeado] ?? $tagMapeado;
            
            $pontuacoes[] = [
                'tag' => $tagFinal,
                'nome' => $variavel->nome ?? 'Sin nombre',
                'valor' => $pontuacaoNumerica,
                'maximo_grafico' => $maximoGrafico,
                'faixa' => $faixa,
                'recomendacao' => $recomendacao,
                'badge' => $badge,
                'b' => $b,
                'm' => $m,
                'a' => $a,
            ];
        }

        $analise = Analise::where('user_id', $usuarioId)
            ->where('formulario_id', $formularioId)
            ->first();

        $analiseTexto = $analise?->texto ?? 'Análise não disponível.';
        $analiseData = $analise?->created_at;
        $analiseHtml = nl2br(e($analiseTexto));
        $analiseHtml = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $analiseHtml);
        $analiseHtml = preg_replace('/###\s?(.*)/', '<h4>$1</h4>', $analiseHtml);

        $indices = $this->calcularIndicesDesdeRespostas($respostasUsuario, $formulario->id);
        
        $pontuacoesParaCalculo = [];
        foreach ($pontuacoes as $ponto) {
            $pontuacoesParaCalculo[] = [
                'tag' => $ponto['tag'],
                'valor' => $ponto['valor'],
                'faixa' => $ponto['faixa']
            ];
        }
        $ejesAnaliticos = $this->calcularEjesAnaliticos($pontuacoesParaCalculo, $indices);
        $iid = $this->calcularIID($ejesAnaliticos);
        $nivelRisco = $this->determinarNivelRisco($iid);
        $planDesenvolvimento = $this->getPlanDesenvolvimento($nivelRisco);
        $promedioIndices = ($indices['EE'] + $indices['PR'] + $indices['SO']) / 3;

            return view('participante.relatorio_emotive_pdf', compact(
                'formulario',
                'respostasUsuario',
                'pontuacoes',
                'variaveis',
                'user',
                'analiseTexto',
                'analiseHtml',
                'analiseData',
                'ejesAnaliticos',
                'iid',
                'promedioIndices',
                'nivelRisco',
                'planDesenvolvimento'
            ));
        } catch (\Exception $e) {
            \Log::error('Error en relatorioPDF', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Devolver una respuesta de error en lugar de lanzar excepción
            return response('Error al generar el relatorio: ' . $e->getMessage(), 500);
        }
    }

    public function gerarPDF(Request $request)
    {
        // Validar que los parámetros estén presentes
        if (!$request->has('formulario') || !$request->has('user')) {
            return redirect()->back()->with('msgError', 'Parámetros faltantes para generar el PDF.');
        }
        
        $formularioId = $request->formulario;
        $usuarioId = $request->user;

        $user = User::findOrFail($usuarioId);
        
        try {
            // Aumentar tiempo de ejecución de PHP antes de hacer la llamada
            set_time_limit(600); // 10 minutos
            ini_set('max_execution_time', 600);
            ini_set('memory_limit', '512M');
            
            // Generar URL del relatorio para PDF
            // La API extrae el contenido del <div class="content"> de la página
            $baseUrl = config('app.url', 'http://localhost:8000');
            
            // Si la URL contiene localhost, cambiarla a 127.0.0.1 para que la API pueda acceder
            $baseUrl = str_replace('localhost', '127.0.0.1', $baseUrl);
            
            // Construir la URL con los parámetros
            $relatorioUrl = rtrim($baseUrl, '/') . '/meurelatorio/pdf?formulario_id=' . urlencode($formularioId) . '&usuario_id=' . urlencode($usuarioId);
            
            \Log::info('Generando PDF desde URL externa', [
                'url' => $relatorioUrl,
                'formulario_id' => $formularioId,
                'usuario_id' => $usuarioId,
                'base_url' => $baseUrl
            ]);
            
            // Llamar al servicio externo de conversión
            // La API tiene su propio timeout de 30 segundos para obtener la página
            // No necesitamos verificar la URL antes - la API lo hará
            $pdfServiceUrl = env('PDF_API_URL', 'http://127.0.0.1:8080/convert-url');
            
            // Preparar los datos según la documentación de la API
            $requestData = [
                'url' => $relatorioUrl
            ];
            
            $jsonData = json_encode($requestData);
            
            \Log::info('Enviando solicitud al servicio PDF', [
                'service_url' => $pdfServiceUrl,
                'url_enviada' => $relatorioUrl,
                'json_data' => $jsonData
            ]);
            
            $ch = curl_init($pdfServiceUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($jsonData)
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
            curl_setopt($ch, CURLOPT_TIMEOUT, 90); // 90 segundos (la API tiene 30s para obtener la página + tiempo de conversión)
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // 10 segundos para conectar
            
            $pdfContent = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            $curlInfo = curl_getinfo($ch);
            curl_close($ch);
            
            \Log::info('Respuesta del servicio PDF', [
                'http_code' => $httpCode,
                'content_length' => strlen($pdfContent),
                'curl_error' => $curlError,
                'content_type' => $curlInfo['content_type'] ?? 'unknown'
            ]);
            
            if ($curlError) {
                \Log::error('Error en curl al llamar servicio PDF', [
                    'error' => $curlError,
                    'http_code' => $httpCode
                ]);
                return redirect()->back()->with('msgError', 'Error al comunicarse con el servicio de PDF: ' . $curlError);
            }
            
            if ($httpCode !== 200) {
                // Intentar parsear la respuesta como JSON para obtener más detalles del error
                $errorResponse = json_decode($pdfContent, true);
                $errorMessage = 'Error del servicio de PDF (código HTTP: ' . $httpCode . ')';
                
                // Según la documentación, los códigos de error son:
                // 400: Error al obtener la URL (la URL no es accesible o hay un error HTTP)
                // 404: No se encontró un div con class='content' en la página
                // 500: Error al convertir HTML a PDF
                
                if ($httpCode === 400) {
                    $errorMessage = 'Error: La URL del relatorio no es accesible. Verifique que el servidor esté corriendo y que la URL sea correcta.';
                } elseif ($httpCode === 404) {
                    $errorMessage = 'Error: No se encontró el elemento <div class="content"> en la página. Verifique que la vista tenga este elemento.';
                } elseif ($httpCode === 500) {
                    $errorMessage = 'Error: No se pudo convertir el HTML a PDF.';
                }
                
                if ($errorResponse && isset($errorResponse['detail'])) {
                    $errorMessage .= ' Detalle: ' . $errorResponse['detail'];
                } elseif ($errorResponse && isset($errorResponse['message'])) {
                    $errorMessage .= ' Mensaje: ' . $errorResponse['message'];
                } elseif (!empty($pdfContent) && strlen($pdfContent) < 500) {
                    $errorMessage .= ' Respuesta: ' . $pdfContent;
                }
                
                \Log::error('Error del servicio PDF', [
                    'http_code' => $httpCode,
                    'url_enviada' => $relatorioUrl,
                    'response' => substr($pdfContent, 0, 500),
                    'parsed_response' => $errorResponse
                ]);
                
                return redirect()->back()->with('msgError', $errorMessage);
            }
            
            if (empty($pdfContent)) {
                \Log::error('Respuesta vacía del servicio PDF');
                return redirect()->back()->with('msgError', 'El servicio de PDF no devolvió contenido.');
            }
            
            // Descargar el PDF directamente sin redirecciones
            $fileName = "relatorio_emotive_{$user->name}.pdf";
            
            // Limpiar cualquier output previo
            if (ob_get_level()) {
                ob_end_clean();
            }
            
            return response($pdfContent, 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"')
                ->header('Content-Length', strlen($pdfContent))
                ->header('Cache-Control', 'no-cache, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
                
        } catch (\Exception $e) {
            \Log::error('Error generando PDF con servicio externo', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('msgError', 'Error al generar el PDF: ' . $e->getMessage());
        }
    }
    
    // Método antiguo - mantener por compatibilidad pero ya no se usa
    private function gerarPDFAntiguo(Request $request)
    {
        // Este método ya no se usa - se mantiene solo por referencia
        $formularioId = $request->formulario;
        $usuarioId = $request->user;

        $user = User::findOrFail($usuarioId);
        $formulario = Formulario::with('perguntas.variaveis')->findOrFail($formularioId);

        $respostasUsuario = Resposta::where('user_id', $user->id)
            ->whereIn('pergunta_id', $formulario->perguntas->pluck('id'))
            ->get()
            ->keyBy('pergunta_id');

        $variaveis = Variavel::with('perguntas')
            ->where('formulario_id', $formulario->id)
            ->get();

        // Calcular puntuaciones para TODAS las dimensiones (EXEM, REPR, DECI, FAPS, EXTR, ASMO)
        // Todas usan la misma lógica de inversión basada en numero_da_pergunta
        $pontuacoes = [];
        
        // Para EXTR, necesitamos contar 16 preguntas según CSV MAX
        // El CSV MAX tiene una pregunta duplicada (#62 y #76 son la misma)
        // Para que EXTR tenga 16 preguntas, necesitamos contar esa pregunta dos veces
        $preguntaDuplicadaEXTR = \App\Models\Pergunta::where('formulario_id', $formularioId)
            ->where('pergunta', 'like', '%Recebo novas demandas antes de conseguir concluir%')
            ->first();
        $preguntaDuplicadaEXTRId = $preguntaDuplicadaEXTR ? $preguntaDuplicadaEXTR->id : null;
        
        foreach ($variaveis as $variavel) {
            $pontuacao = 0;
            $totalRespostas = 0;
            
            // Verificar si la variable tiene preguntas asociadas
            if ($variavel->perguntas->isEmpty()) {
                continue; // Saltar variables sin preguntas
            }
            
            // Calcular puntuación basada en las respuestas
            // IMPORTANTE: La misma lógica se aplica para todas las dimensiones
            // Para EXTR, verificar si debemos contar la pregunta duplicada dos veces
            $esEXTR = (strtoupper($variavel->tag ?? '') === 'EXTR');
            
            foreach ($variavel->perguntas as $pergunta) {
                // Para EXTR, contar la pregunta duplicada dos veces
                $contarDosVeces = ($esEXTR && $preguntaDuplicadaEXTRId && $pergunta->id == $preguntaDuplicadaEXTRId);
                $resposta = $respostasUsuario->get($pergunta->id);
                // Aplicar la misma lógica de inversión para todas las dimensiones
                // Preguntas invertidas: 0→6, 1→5, 2→4, 3→3, 4→2, 5→1, 6→0
                // Preguntas normales: valor sin cambios
                $valorResposta = $this->obterValorRespostaComInversao($resposta, $pergunta);
                if ($valorResposta !== null) {
                    // Para EXTR, si es la pregunta duplicada, contarla dos veces
                    if ($contarDosVeces) {
                        $pontuacao += $valorResposta; // Primera vez
                        $pontuacao += $valorResposta; // Segunda vez (duplicada)
                        $totalRespostas += 2; // Contar como 2 respuestas
                    } else {
                        $pontuacao += $valorResposta;
                        $totalRespostas++;
                    }
                }
            }
            
            // Solo agregar si hay al menos una respuesta válida
            if ($totalRespostas === 0) {
                continue; // Saltar variables sin respuestas
            }
            
            // Calcular máximo posible y máximo del gráfico
            // Si el máximo posible > 100, el gráfico usa máximo 200, sino 100
            $maximoPosible = $totalRespostas * 6;
            $maximoGrafico = $maximoPosible > 100 ? 200 : 100;
            
            // Classificar faixa
            $faixa = $this->classificarPontuacao($pontuacao, $variavel);
            
            // Determinar recomendação
            $recomendacao = 'Sem recomendação disponível.';
            switch ($faixa) {
                case 'Baixa':
                    $recomendacao = $variavel->r_baixa ?? $recomendacao;
                    break;
                case 'Moderada':
                    $recomendacao = $variavel->r_moderada ?? $recomendacao;
                    break;
                case 'Alta':
                    $recomendacao = $variavel->r_alta ?? $recomendacao;
                    break;
            }
            
            $pontuacoes[] = [
                'nome' => $variavel->nome,
                'tag' => strtoupper($variavel->tag),
                'pontuacao' => $pontuacao,
                'valor' => $pontuacao, // Valor absoluto
                'maximo_grafico' => $maximoGrafico, // Máximo del gráfico (100 o 200)
                'faixa' => $faixa,
                'recomendacao' => $recomendacao,
            ];
        }

        $labels = collect($pontuacoes)->pluck('tag');
        $dataValores = collect($pontuacoes)->pluck('pontuacao');

        $graficosDir = storage_path('app/public/graficos');
        if (!file_exists($graficosDir)) {
            mkdir($graficosDir, 0755, true);
        }

        // GRÁFICO DE BARRAS
        $dataValores[] = 0;

        $configBarras = [
            'type' => 'bar',
            'data' => [
                'labels' => $labels,
                'datasets' => [[
                    'label' => 'Pontuação',
                    'data' => $dataValores,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.6)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 1
                ]]
            ],
            'options' => [
                'responsive' => true,
                'plugins' => [
                    'legend' => ['display' => false],
                    'title' => ['display' => false]
                ],
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                        'suggestedMin' => 0,
                        'min' => 0,
                        'max' => 120,
                        'ticks' => [
                            'min' => 0,
                            'max' => 120,
                            'stepSize' => 20
                        ]
                    ]
                ],
                'layout' => [
                    'padding' => 10
                ]
            ]
        ];

        // Generar gráfico de barras con timeout más corto
        // Agregar parámetros de tamaño para mejor calidad
        $urlGraficoBarras = 'https://quickchart.io/chart?width=800&height=400&c=' . urlencode(json_encode($configBarras));
        $imagemBarrasPath = $graficosDir . '/grafico_' . uniqid() . '.png';
        $context = stream_context_create([
            'http' => [
                'timeout' => 10, // Reducir timeout a 10 segundos
                'method' => 'GET',
                'header' => 'User-Agent: PHP'
            ]
        ]);
        $imagemBarrasContent = @file_get_contents($urlGraficoBarras, false, $context);
        if ($imagemBarrasContent !== false && strlen($imagemBarrasContent) > 0) {
            file_put_contents($imagemBarrasPath, $imagemBarrasContent);
            // Guardar la ruta completa para DomPDF
            $imagemBarrasPublicPath = $imagemBarrasPath; // Ruta completa del archivo guardado
            \Log::info('Gráfico de barras guardado', ['path' => $imagemBarrasPublicPath]);
        } else {
            $imagemBarrasPublicPath = null;
            \Log::warning('No se pudo descargar el gráfico de barras desde QuickChart');
        }

        // GRÁFICO DE RADAR E.MO.TI.VE - Igual a la web
        $ordemDimensoes = ['EXEM', 'REPR', 'DECI', 'FAPS', 'EXTR', 'ASMO'];
        $pontuacoesOrdenadas = [];
        foreach ($ordemDimensoes as $tag) {
            $ponto = collect($pontuacoes)->firstWhere('tag', $tag);
            if ($ponto) {
                $pontuacoesOrdenadas[] = $ponto;
            }
        }
        $labelsRadar = collect($pontuacoesOrdenadas)->pluck('tag')->map(function($tag) {
            $nomesCompletos = [
                'EXEM' => 'Exaustão Emocional',
                'REPR' => 'Realização Profissional',
                'DECI' => 'Despersonalização / Cinismo',
                'FAPS' => 'Fatores Psicossociais',
                'EXTR' => 'Excesso de Trabalho',
                'ASMO' => 'Assédio Moral'
            ];
            return $nomesCompletos[$tag] ?? $tag;
        });
        $dataRadar = collect($pontuacoesOrdenadas)->pluck('valor');
        
        // Calcular el máximo del gráfico: usar el máximo más alto de todas las dimensiones
        // Si algún máximo_grafico es 200, usar 200 para todo el gráfico; sino 100
        $maximosGrafico = collect($pontuacoesOrdenadas)->map(function($ponto) {
            return $ponto['maximo_grafico'] ?? 100;
        });
        $maximoGrafico = $maximosGrafico->max();

        $coresPorTag = [
            'EXEM' => '#8B4513',
            'REPR' => '#8B4513',
            'DECI' => '#FF8C00',
            'FAPS' => '#90EE90',
            'EXTR' => '#9370DB',
            'ASMO' => '#4169E1'
        ];
        $coresTransparentes = [
            'EXEM' => 'rgba(139, 69, 19, 0.25)',
            'REPR' => 'rgba(139, 69, 19, 0.25)',
            'DECI' => 'rgba(255, 140, 0, 0.25)',
            'FAPS' => 'rgba(144, 238, 144, 0.25)',
            'EXTR' => 'rgba(147, 112, 219, 0.25)',
            'ASMO' => 'rgba(65, 105, 225, 0.25)'
        ];

        $datasetsRadar = [];
        // Dataset principal (línea azul claro)
        $datasetsRadar[] = [
            'label' => 'Pontuação Geral',
            'data' => $dataRadar,
            'backgroundColor' => 'rgba(173, 216, 230, 0.4)',
            'borderColor' => 'rgba(173, 216, 230, 1)',
            'borderWidth' => 2.5,
            'pointBackgroundColor' => 'rgba(173, 216, 230, 1)',
            'pointBorderColor' => '#fff',
            'pointRadius' => 0,
            'fill' => true,
            'order' => 2
        ];

        // Datasets para cada dimensão (segmentos de colores)
        foreach ($pontuacoesOrdenadas as $index => $ponto) {
            $data = array_fill(0, count($labelsRadar), 0);
            $data[$index] = $ponto['valor'];

            $cor = $coresPorTag[$ponto['tag']] ?? '#808080';
            $corTransparente = $coresTransparentes[$ponto['tag']] ?? 'rgba(128, 128, 128, 0.25)';

            $datasetsRadar[] = [
                'label' => $ponto['nome'],
                'data' => $data,
                'backgroundColor' => $corTransparente,
                'borderColor' => 'transparent',
                'borderWidth' => 0,
                'pointBackgroundColor' => $cor,
                'pointBorderColor' => '#fff',
                'pointRadius' => 0,
                'fill' => true,
                'order' => 1
            ];
        }

        $configRadar = [
            'type' => 'radar',
            'data' => [
                'labels' => $labelsRadar,
                'datasets' => $datasetsRadar
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => true,
                'plugins' => [
                    'legend' => ['display' => false],
                    'tooltip' => ['enabled' => true],
                ],
                'scales' => [
                    'r' => [
                        'beginAtZero' => true,
                        'max' => $maximoGrafico, // Máximo dinámico (100 o 200)
                        'min' => 0,
                        'ticks' => [
                            'stepSize' => $maximoGrafico === 200 ? 40 : 20, // Si máximo es 200, stepSize 40; si es 100, stepSize 20
                            'font' => ['size' => 10, 'family' => 'Quicksand'],
                            'color' => '#666',
                            'backdropColor' => 'transparent'
                        ],
                        'grid' => ['color' => 'rgba(0, 0, 0, 0.15)', 'lineWidth' => 1],
                        'angleLines' => ['color' => 'rgba(0, 0, 0, 0.15)', 'lineWidth' => 1],
                        'pointLabels' => [
                            'font' => ['size' => 11, 'weight' => 'bold', 'family' => 'Quicksand'],
                            'color' => '#000',
                            'padding' => 15
                        ]
                    ]
                ]
            ]
        ];

        // Generar gráfico de radar con timeout más corto
        // Agregar parámetros de tamaño para mejor calidad
        $urlGraficoRadar = 'https://quickchart.io/chart?width=800&height=800&c=' . urlencode(json_encode($configRadar));
        $imagemRadarPath = $graficosDir . '/radar_' . uniqid() . '.png';
        $context = stream_context_create([
            'http' => [
                'timeout' => 10, // Reducir timeout a 10 segundos
                'method' => 'GET',
                'header' => 'User-Agent: PHP'
            ]
        ]);
        $imagemRadarContent = @file_get_contents($urlGraficoRadar, false, $context);
        if ($imagemRadarContent !== false && strlen($imagemRadarContent) > 0) {
            file_put_contents($imagemRadarPath, $imagemRadarContent);
            // Guardar la ruta completa para DomPDF
            $imagemRadarPublicPath = $imagemRadarPath; // Ruta completa del archivo guardado
            \Log::info('Gráfico de radar guardado', ['path' => $imagemRadarPublicPath]);
        } else {
            $imagemRadarPublicPath = null;
            \Log::warning('No se pudo descargar el gráfico de radar desde QuickChart');
        }
        
        // GRÁFICO DE RISCO DE DESCARRILAMENTO (IID) - Barra horizontal
        // === CALCULAR ÍNDICES EE, PR, SO DIRECTAMENTE DESDE RESPOSTAS ===
        $indices = $this->calcularIndicesDesdeRespostas($respostasUsuario, $formulario->id);
        $ejesAnaliticos = $this->calcularEjesAnaliticos($pontuacoes, $indices);
        $iid = $this->calcularIID($ejesAnaliticos);
        $nivelRisco = $this->determinarNivelRisco($iid);
        
        $configIID = [
            'type' => 'bar',
            'data' => [
                'labels' => ['Risco de Descarrilamento'],
                'datasets' => [[
                    'label' => 'IID',
                    'data' => [$iid],
                    'backgroundColor' => $nivelRisco['cor_hex'],
                    'borderColor' => $nivelRisco['cor_hex'],
                    'borderWidth' => 1
                ]]
            ],
            'options' => [
                'indexAxis' => 'y',
                'responsive' => true,
                'plugins' => [
                    'legend' => ['display' => false],
                    'title' => ['display' => true, 'text' => 'Índice Integrado de Descarrilamento (IID)']
                ],
                'scales' => [
                    'x' => [
                        'beginAtZero' => true,
                        'max' => 100,
                        'ticks' => ['stepSize' => 20]
                    ]
                ]
            ]
        ];
        
        // Generar gráfico IID con timeout más corto
        $urlGraficoIID = 'https://quickchart.io/chart?c=' . urlencode(json_encode($configIID));
        $imagemIIDPath = $graficosDir . '/iid_' . uniqid() . '.png';
        $context = stream_context_create([
            'http' => [
                'timeout' => 10, // Reducir timeout a 10 segundos
                'method' => 'GET',
                'header' => 'User-Agent: PHP'
            ]
        ]);
        $imagemIIDContent = @file_get_contents($urlGraficoIID, false, $context);
        if ($imagemIIDContent !== false && strlen($imagemIIDContent) > 0) {
            file_put_contents($imagemIIDPath, $imagemIIDContent);
            // Guardar la ruta completa para DomPDF
            $imagemIIDPublicPath = $imagemIIDPath; // Ruta completa del archivo guardado
            \Log::info('Gráfico IID guardado', ['path' => $imagemIIDPublicPath]);
        } else {
            $imagemIIDPublicPath = null;
            \Log::warning('No se pudo descargar el gráfico IID desde QuickChart');
        }

        // === ESTRATEGIA: Usar DomPDF con vista optimizada que se vea igual a la web ===
        // Usamos las imágenes de gráficos ya generadas (QuickChart.io) para que se vea idéntico
        
        // Preparar datos para la vista PDF
        $analise = Analise::where('user_id', $usuarioId)
            ->where('formulario_id', $formularioId)
            ->first();
        
        $analiseTexto = $analise?->texto ?? 'Análise não disponível.';
        
        // === CALCULAR ÍNDICES EE, PR, SO DIRECTAMENTE DESDE RESPOSTAS ===
        $indices = $this->calcularIndicesDesdeRespostas($respostasUsuario, $formulario->id);
        $ejesAnaliticos = $this->calcularEjesAnaliticos($pontuacoes, $indices);
        $iid = $this->calcularIID($ejesAnaliticos);
        $nivelRisco = $this->determinarNivelRisco($iid);
        $planDesenvolvimento = $this->getPlanDesenvolvimento($nivelRisco);
        
        // Calcular promedio de índices
        $promedioIndices = ($indices['EE'] + $indices['PR'] + $indices['SO']) / 3;
        
        // Las rutas ya vienen como rutas absolutas completas desde storage/app/public/graficos/
        // Verificar que las imágenes existan antes de pasarlas a la vista
        // Convertir a rutas relativas desde base_path() para DomPDF con chroot
        $imagemGraficoPath = null;
        $imagemRadarPath = null;
        $imagemIIDPath = null;
        
        $basePath = str_replace('\\', '/', realpath(base_path()));
        
        if ($imagemBarrasPublicPath && file_exists($imagemBarrasPublicPath)) {
            $fullPathNormalized = str_replace('\\', '/', realpath($imagemBarrasPublicPath));
            $imagemGraficoPath = str_replace($basePath . '/', '', $fullPathNormalized);
            \Log::info('Gráfico de barras preparado para PDF', [
                'original' => $imagemBarrasPublicPath,
                'relativa' => $imagemGraficoPath,
                'existe' => file_exists($imagemBarrasPublicPath),
                'tamaño' => file_exists($imagemBarrasPublicPath) ? filesize($imagemBarrasPublicPath) : 0
            ]);
        } else {
            \Log::warning('Gráfico de barras no encontrado', ['path' => $imagemBarrasPublicPath]);
        }
        
        if ($imagemRadarPublicPath && file_exists($imagemRadarPublicPath)) {
            $fullPathNormalized = str_replace('\\', '/', realpath($imagemRadarPublicPath));
            $imagemRadarPath = str_replace($basePath . '/', '', $fullPathNormalized);
            \Log::info('Gráfico de radar preparado para PDF', [
                'original' => $imagemRadarPublicPath,
                'relativa' => $imagemRadarPath,
                'existe' => file_exists($imagemRadarPublicPath),
                'tamaño' => file_exists($imagemRadarPublicPath) ? filesize($imagemRadarPublicPath) : 0
            ]);
        } else {
            \Log::warning('Gráfico de radar no encontrado', ['path' => $imagemRadarPublicPath]);
        }
        
        if ($imagemIIDPublicPath && file_exists($imagemIIDPublicPath)) {
            $fullPathNormalized = str_replace('\\', '/', realpath($imagemIIDPublicPath));
            $imagemIIDPath = str_replace($basePath . '/', '', $fullPathNormalized);
        }
        
        \Log::info('Rutas de imágenes para PDF', [
            'grafico' => $imagemGraficoPath ? 'existe' : 'no existe',
            'radar' => $imagemRadarPath ? 'existe' : 'no existe',
            'iid' => $imagemIIDPath ? 'existe' : 'no existe',
            'base_path' => $basePath
        ]);
        
        // Preparar datos para la vista PDF
        $data = [
            'user' => $user,
            'formulario' => $formulario,
            'respostasUsuario' => $respostasUsuario,
            'pontuacoes' => $pontuacoes,
            'variaveis' => $variaveis,
            'hoje' => now()->format('d/m/Y'),
            'imagemGrafico' => $imagemGraficoPath,
            'imagemRadar' => $imagemRadarPath,
            'imagemIID' => $imagemIIDPath,
            'analiseTexto' => $analiseTexto,
            'ejesAnaliticos' => $ejesAnaliticos,
            'iid' => $iid,
            'promedioIndices' => $promedioIndices,
            'nivelRisco' => $nivelRisco,
            'planDesenvolvimento' => $planDesenvolvimento,
            'isPdf' => true, // Flag para activar estilos PDF
        ];
        
        try {
            // Aumentar tiempo de ejecución para PDFs complejos
            set_time_limit(300); // 5 minutos
            ini_set('max_execution_time', 300);
            ini_set('memory_limit', '512M'); // Aumentar memoria también
            
            \Log::info('Iniciando generación de PDF', [
                'user_id' => $user->id,
                'formulario_id' => $formularioId,
                'imagenes' => [
                    'grafico' => $imagemGraficoPath ? 'si' : 'no',
                    'radar' => $imagemRadarPath ? 'si' : 'no',
                    'iid' => $imagemIIDPath ? 'si' : 'no'
                ]
            ]);
            
            // Usar DomPDF para generar el PDF directamente desde la vista HTML
            $viewPDF = view()->exists('pdf.relatorios.emotive') ? 'pdf.relatorios.emotive' : 'pdf.relatorios.qrp36';
            
            $pdf = Pdf::loadView($viewPDF, $data)
                ->setPaper('a4', 'portrait') // Usar A4 estándar
                ->setOption('enable-local-file-access', true)
                ->setOption('isRemoteEnabled', false) // Deshabilitar carga remota - solo usar imágenes locales
                ->setOption('isHtml5ParserEnabled', true)
                ->setOption('isPhpEnabled', false)
                ->setOption('enable-javascript', false)
                ->setOption('dpi', 150) // Aumentar DPI para mejor calidad
                ->setOption('defaultFont', 'DejaVu Sans')
                ->setOption('enable-font-subsetting', false)
                ->setOption('chroot', realpath(base_path())) // Limitar acceso a archivos - usar realpath para ruta absoluta
                ->setOption('logOutputFile', storage_path('logs/dompdf.log'))
                ->setOption('tempDir', sys_get_temp_dir())
                ->setOption('enableCssFloat', true) // Habilitar float para mejor layout
                ->setOption('enableInlineCss', true); // Asegurar que los estilos inline se apliquen
            
            \Log::info('PDF generado exitosamente');
            
            return $pdf->download("relatorio_emotive_{$user->name}.pdf");
            
        } catch (\Exception $e) {
            \Log::error('Error generando PDF con DomPDF', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $errorMessage = 'Error al generar el PDF. ';
            if (str_contains($e->getMessage(), 'Maximum execution time')) {
                $errorMessage .= 'El PDF está tardando demasiado tiempo. Se ha aumentado el límite a 5 minutos. Intente nuevamente.';
            } else {
                $errorMessage .= $e->getMessage();
            }
            return redirect()->back()->with('msgError', $errorMessage);
        }
    }

    /**
     * Encuentra la ruta de Node.js en el sistema
     */
    private function findNodePath(): ?string
    {
        // Intentar múltiples métodos para encontrar Node.js
        
        // Método 1: Usar 'which node' (Linux/Mac)
        $whichNode = @shell_exec('which node 2>/dev/null');
        if ($whichNode) {
            $nodePath = trim($whichNode);
            if (file_exists($nodePath) && is_executable($nodePath)) {
                return $nodePath;
            }
        }
        
        // Método 2: Usar 'where node' (Windows)
        $whereNode = @shell_exec('where node 2>nul');
        if ($whereNode) {
            $nodePath = trim(explode("\n", $whereNode)[0]);
            if (file_exists($nodePath) && is_executable($nodePath)) {
                return $nodePath;
            }
        }
        
        // Método 3: Rutas comunes
        $commonPaths = [
            '/usr/bin/node',
            '/usr/local/bin/node',
            '/opt/homebrew/bin/node', // Homebrew en Mac M1/M2
            '/usr/local/opt/node/bin/node',
            'C:\\Program Files\\nodejs\\node.exe',
            'C:\\Program Files (x86)\\nodejs\\node.exe',
        ];
        
        foreach ($commonPaths as $path) {
            if (file_exists($path) && is_executable($path)) {
                return $path;
            }
        }
        
        // Método 4: Buscar en PATH usando exec
        $paths = explode(PATH_SEPARATOR, getenv('PATH') ?: '');
        foreach ($paths as $path) {
            $nodePath = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'node';
            if (file_exists($nodePath) && is_executable($nodePath)) {
                return $nodePath;
            }
        }
        
        \Log::warning('No se pudo encontrar Node.js. Browsershot intentará usar la ruta por defecto.');
        return null;
    }

    /**
     * Obtiene el valor de respuesta aplicando inversión si la pregunta lo requiere
     * 
     * IMPORTANTE: Esta lógica se aplica uniformemente para TODAS las dimensiones:
     * EXEM, REPR, DECI, FAPS, EXTR, ASMO
     * 
     * Las preguntas que requieren inversión son las que tienen estos numero_da_pergunta: 
     * 48, 49, 50, 51, 52, 53, 54, 55, 78, 79, 81, 82, 83, 88, 90, 92, 93, 94, 95, 96, 97
     * 
     * Lógica de inversión (igual para todas las dimensiones):
     * - Preguntas invertidas: 0→6, 1→5, 2→4, 3→3, 4→2, 5→1, 6→0
     *   (En preguntas invertidas: 0 es el valor más alto, 6 es el valor más bajo)
     * - Preguntas normales: valor sin cambios
     */
    private function obterValorRespostaComInversao($resposta, $pergunta): ?int
    {
        if (!$resposta || $resposta->valor_resposta === null) {
            return null;
        }

        $valor = $resposta->valor_resposta;
        
        // Asegurar que la pregunta existe
        if (!$pergunta) {
            \Log::warning('Pregunta es null en obterValorRespostaComInversao');
            return $valor;
        }
        
        // Verificar si esta pregunta requiere inversión (usando helper por texto)
        $necesitaInversion = \App\Helpers\PerguntasInvertidasHelper::precisaInversao($pergunta);
        
        if ($necesitaInversion) {
            // Invertir el valor: 0→6, 1→5, 2→4, 3→3, 4→2, 5→1, 6→0
            // En preguntas invertidas: 0 es el valor más alto, 6 es el valor más bajo
            $valorInvertido = 6 - $valor;
            \Log::info('✅ APLICANDO INVERSIÓN', [
                'pergunta_id' => $pergunta->id,
                'texto_pergunta' => substr(trim($pergunta->pergunta ?? ''), 0, 50) . '...',
                'valor_original' => $valor,
                'valor_invertido' => $valorInvertido
            ]);
            return $valorInvertido;
        }
        
        return $valor;
    }

}
