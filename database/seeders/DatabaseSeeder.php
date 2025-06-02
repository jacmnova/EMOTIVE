<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         $this->call([
            // FormularioSeeder::class,  // php artisan db:seed --class=FormularioSeeder
            // VariaveisSeeder::class, // php artisan db:seed --class=VariaveisSeeder
            // PerguntasSeeder::class, // php artisan db:seed --class=PerguntasSeeder
            // VariavelPerguntaSeeder::class, // php artisan db:seed --class=VariavelPerguntaSeeder
            
            UsuariosSeeder::class, // php artisan db:seed --class=UsuariosSeeder
            ClientesSeeder::class, // php artisan db:seed --class=ClientesSeeder
            CalculoSeeder::class, // php artisan db:seed --class=CalculoSeeder


            FormBurnOutSeeder::class, // php artisan db:seed --class=FormBurnOutSeeder
            VarBurnOutSeeder::class, // php artisan db:seed --class=VarBurnOutSeeder
            PerguntasBurnOutSeeder::class, // php artisan db:seed --class=PerguntasBurnOutSeeder
            VarPerguntaBurnOutSeeder::class, // php artisan db:seed --class=VarPerguntaBurnOutSeeder
            EtapaSeeder::class, // php artisan db:seed --class=EtapaSeeder
        ]);
    }
}
