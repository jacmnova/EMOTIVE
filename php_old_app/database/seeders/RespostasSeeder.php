<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Crea asignaciones usuario_formulario completadas y respuestas de prueba
 * para el formulario Burnout 99 (formulario_id = 1).
 * Escala: 1 a 5.
 */
class RespostasSeeder extends Seeder
{
    public function run(): void
    {
        $formularioId = 1;
        $userIds = DB::table('users')->where('ativo', 1)->pluck('id')->toArray();
        $perguntas = DB::table('perguntas')->where('formulario_id', $formularioId)->get();

        if ($perguntas->isEmpty()) {
            $this->command->warn('No hay preguntas para formulario_id ' . $formularioId . '. Ejecuta antes PerguntasBurnOutSeeder.');
            return;
        }

        $now = now();
        $minVal = 1;
        $maxVal = 5;

        foreach ($userIds as $userId) {
            $yaTiene = DB::table('usuario_formulario')
                ->where('usuario_id', $userId)
                ->where('formulario_id', $formularioId)
                ->whereNull('deleted_at')
                ->exists();
            if ($yaTiene) {
                continue;
            }
            // Asignar formulario al usuario como completado
            DB::table('usuario_formulario')->insert([
                'usuario_id' => $userId,
                'formulario_id' => $formularioId,
                'status' => 'completo',
                'data_limite' => $now->copy()->addDays(30),
                'video_assistido' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // Una respuesta por pregunta (escala 1-5, valores variados para prueba)
            foreach ($perguntas as $index => $pergunta) {
                $valor = $minVal + ($index % ($maxVal - $minVal + 1));
                if ($index % 4 === 0) {
                    $valor = rand($minVal, $maxVal);
                }
                DB::table('respostas')->insert([
                    'user_id' => $userId,
                    'pergunta_id' => $pergunta->id,
                    'valor_resposta' => $valor,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        $this->command->info('RespostasSeeder: ' . count($userIds) . ' usuarios con formulario completado y ' . $perguntas->count() . ' respuestas cada uno.');
    }
}
