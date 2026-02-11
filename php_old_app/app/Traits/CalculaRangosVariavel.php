<?php

namespace App\Traits;

trait CalculaRangosVariavel
{
    /**
     * Calcula los rangos B, M, A de forma general basado en:
     * - Número de preguntas asociadas
     * - Score máximo por pregunta (del formulario)
     * 
     * Fórmula según el CSV del emulador:
     * - B = 33.3% del máximo (redondeado)
     * - M = 66.7% del máximo (redondeado)
     * - A = M + 1 (inicio de faixa alta)
     * 
     * @param int $totalPerguntas Número de preguntas asociadas a la variable
     * @param int $scoreFim Score máximo por pregunta (default: 6)
     * @return array ['B' => int, 'M' => int, 'A' => int]
     */
    public static function calcularRangosGenerales(int $totalPerguntas, int $scoreFim = 6): array
    {
        if ($totalPerguntas <= 0) {
            return ['B' => 0, 'M' => 0, 'A' => 0];
        }
        
        $max = $totalPerguntas * $scoreFim;
        
        // Calcular rangos basados en porcentajes del CSV
        // B = 33.3% del máximo (redondeado)
        // M = 66.7% del máximo (redondeado)
        // A = M + 1 (inicio de faixa alta)
        $b = round($max * 0.333);
        $m = round($max * 0.667);
        $a = $m + 1;
        
        // Asegurar que A no exceda el máximo
        if ($a > $max) {
            $a = $max;
        }
        
        return [
            'B' => (int)$b,
            'M' => (int)$m,
            'A' => (int)$a
        ];
    }
    
    /**
     * Actualiza automáticamente los rangos B, M, A de una variable
     * basado en sus preguntas asociadas y el score_fim del formulario
     * 
     * @param \App\Models\Variavel $variavel
     * @return bool
     */
    public static function actualizarRangosAutomaticamente($variavel): bool
    {
        if (!$variavel || !$variavel->formulario) {
            return false;
        }
        
        $totalPerguntas = $variavel->perguntas()->count();
        $scoreFim = $variavel->formulario->score_fim ?? 6;
        
        $rangos = self::calcularRangosGenerales($totalPerguntas, $scoreFim);
        
        $variavel->B = $rangos['B'];
        $variavel->M = $rangos['M'];
        $variavel->A = $rangos['A'];
        
        return $variavel->save();
    }
}

