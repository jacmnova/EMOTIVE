<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EtapaSeeder extends Seeder
{

    public function run()
    {
        DB::table('formulario_etapas')->truncate();

        DB::table('formulario_etapas')->insert([

            [
                'formulario_id' => 1,
                'etapa' => 1,
                'de' => 1,
                'ate' => 36,
            ],
            [
                'formulario_id' => 1,
                'etapa' => 2,
                'de' => 37,
                'ate' => 52,
            ],
            [
                'formulario_id' => 1,
                'etapa' => 3,
                'de' => 53,
                'ate' => 74,
            ],
            [
                'formulario_id' => 1,
                'etapa' => 4,
                'de' => 75,
                'ate' => 99,
            ],
        ]);
    }
}