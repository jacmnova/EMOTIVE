# 🔍 Verificar y Corregir Formularios Pendientes

## Verificar Estado de Formularios

```bash
cd /var/www/laravel/EMOTIVE

php artisan tinker
```

```php
use App\Models\UsuarioFormulario;
use App\Models\Resposta;
use App\Models\Formulario;

// Ver todos los formularios pendientes
$pendientes = UsuarioFormulario::where('status', 'pendente')->with('formulario')->get();

foreach($pendientes as $uf) {
    $formulario = $uf->formulario;
    $totalPerguntas = $formulario->perguntas->count();
    $respostas = Resposta::where('user_id', $uf->usuario_id)
        ->whereIn('pergunta_id', $formulario->perguntas->pluck('id'))
        ->count();
    
    $percentual = $totalPerguntas > 0 ? round(($respostas / $totalPerguntas) * 100) : 0;
    
    echo "Usuario {$uf->usuario_id} - Formulario {$uf->formulario_id} ({$formulario->nome ?? 'N/A'}):\n";
    echo "  Respostas: {$respostas}/{$totalPerguntas} ({$percentual}%)\n";
    echo "  Status: {$uf->status}\n";
    echo "  Debería estar completo: " . ($respostas >= $totalPerguntas && $totalPerguntas > 0 ? 'Sí' : 'No') . "\n\n";
}
exit
```

## Marcar Formularios Completos Manualmente

```bash
cd /var/www/laravel/EMOTIVE

php artisan tinker
```

```php
use App\Models\UsuarioFormulario;
use App\Models\Resposta;
use App\Models\Formulario;

// Buscar y marcar formularios completos
$pendientes = UsuarioFormulario::where('status', 'pendente')->get();
$corrigidos = 0;

foreach($pendientes as $uf) {
    $formulario = Formulario::find($uf->formulario_id);
    if (!$formulario) continue;
    
    $totalPerguntas = $formulario->perguntas->count();
    $respostas = Resposta::where('user_id', $uf->usuario_id)
        ->whereIn('pergunta_id', $formulario->perguntas->pluck('id'))
        ->count();
    
    if ($respostas >= $totalPerguntas && $totalPerguntas > 0) {
        $uf->status = 'completo';
        $uf->save();
        $corrigidos++;
        echo "✅ Formulario {$uf->formulario_id} del usuario {$uf->usuario_id} marcado como completo ({$respostas}/{$totalPerguntas})\n";
    }
}

echo "\n✅ Total corregidos: {$corrigidos}\n";
exit
```

## Verificar Configuración de OpenAI

```bash
cd /var/www/laravel/EMOTIVE

# Ver si tiene API key
grep OPENAI .env

# Si no está, agregar en .env
# OPENAI_API_KEY=tu_api_key_aqui
# OPENAI_API_URL=https://api.openai.com/v1/chat/completions
# OPENAI_MODEL=gpt-4o
```

## Generar Analise Manualmente

```bash
cd /var/www/laravel/EMOTIVE

php artisan tinker
```

```php
use App\Models\UsuarioFormulario;
use App\Models\Analise;
use App\Models\Resposta;
use App\Models\Formulario;
use App\Models\Variavel;
use App\Models\User;
use Illuminate\Support\Facades\Http;

// Para un formulario específico
$usuarioId = 1; // Cambiar por el ID del usuario
$formularioId = 1; // Cambiar por el ID del formulario

$analise = Analise::where('user_id', $usuarioId)
    ->where('formulario_id', $formularioId)
    ->first();

if ($analise) {
    echo "Analise ya existe para este formulario\n";
    exit;
}

// Generar analise
$user = User::find($usuarioId);
$formulario = Formulario::with('perguntas')->find($formularioId);
$variaveis = Variavel::with('perguntas')->where('formulario_id', $formularioId)->get();

$respostasUsuario = Resposta::where('user_id', $usuarioId)
    ->whereIn('pergunta_id', $formulario->perguntas->pluck('id'))
    ->get()
    ->keyBy('pergunta_id');

$pontuacoes = [];
foreach ($variaveis as $variavel) {
    $pontuacao = 0;
    foreach ($variavel->perguntas as $pergunta) {
        $resposta = $respostasUsuario->get($pergunta->id);
        if ($resposta) {
            $pontuacao += $resposta->valor_resposta ?? 0;
        }
    }
    $b = $variavel->B ?? 0;
    $m = $variavel->M ?? 0;
    $faixa = $pontuacao <= $b ? 'Baixa' : ($pontuacao <= $m ? 'Moderada' : 'Alta');
    
    $pontuacoes[] = [
        'tag' => strtoupper($variavel->tag),
        'valor' => $pontuacao,
        'faixa' => $faixa,
    ];
}

// Generar prompt y analise (similar al método del controlador)
// ... (código para generar analise)

exit
```

## Ver Logs de Errores

```bash
cd /var/www/laravel/EMOTIVE

# Ver errores relacionados con formularios
tail -100 storage/logs/laravel.log | grep -i "formulario\|analise\|resposta"

# Ver errores de OpenAI
tail -100 storage/logs/laravel.log | grep -i "openai\|api"
```

¡Ya agregué el método `finalizar`! Ahora necesitas hacer push del cambio al servidor.

