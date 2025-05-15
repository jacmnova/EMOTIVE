<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsuariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->truncate();

        $perfis = [
            [
                'name' => 'Arley Humberto Rueda Rincon', 
                'email' => 'wheelkorner@gmail.com',
                'password' => '$2y$12$2VZ2YQsmwtZjQKBvsAfgVuNZUyAyalJCv04NnVNMsgyn4SpYcczZO', 
                'email_verified_at' => now(), 
                'created_at' => now(), 
                'sa' => 1, 
                'admin' => 1,
                'usuario' => 1,
                'gestor' => 1 , 
                'ativo' => 1
            ],
            [
                'name' => 'Dante Kollross Rincon', 
                'email' => 'dante@gmail.com',
                'password' => '$2y$12$2VZ2YQsmwtZjQKBvsAfgVuNZUyAyalJCv04NnVNMsgyn4SpYcczZO', 
                'email_verified_at' => now(), 
                'created_at' => now(), 
                'sa' => 0, 
                'admin' => 0,
                'usuario' => 1,
                'gestor' => 0 , 
                'ativo' => 1
            ],
            [
                'name' => 'Amuneth Rincon', 
                'email' => 'amuneth@gmail.com',
                'password' => '$2y$12$2VZ2YQsmwtZjQKBvsAfgVuNZUyAyalJCv04NnVNMsgyn4SpYcczZO', 
                'email_verified_at' => now(), 
                'created_at' => now(), 
                'sa' => 0, 
                'admin' => 0,
                'usuario' => 0,
                'gestor' => 1 , 
                'ativo' => 1
            ],
        ];

        DB::table('users')->insert($perfis);
    }
}