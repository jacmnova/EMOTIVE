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
            FormularioSeeder::class,  // php artisan db:seed --class=FormularioSeeder
            VariaveisSeeder::class, // php artisan db:seed --class=VariaveisSeeder
            PerguntasSeeder::class, // php artisan db:seed --class=PerguntasSeeder
            UsuariosSeeder::class, // php artisan db:seed --class=UsuariosSeeder
            ClientesSeeder::class, // php artisan db:seed --class=ClientesSeeder
            CalculoSeeder::class, // php artisan db:seed --class=CalculoSeeder
            VariavelPerguntaSeeder::class, // php artisan db:seed --class=VariavelPerguntaSeeder
        ]);
    }
}
