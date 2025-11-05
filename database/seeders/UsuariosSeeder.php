<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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

        // Contraseña para todas las cuentas: admin123
        $password = Hash::make('admin123');

        $perfis = [
            [
                'name' => 'Arley Humberto Rueda Rincon', 
                'email' => 'wheelkorner@gmail.com',
                'password' => $password, 
                'email_verified_at' => now(), 
                'created_at' => now(), 
                'sa' => 1, 
                'admin' => 1,
                'usuario' => 1,
                'gestor' => 1 , 
                'ativo' => 1,
                'cliente_id' => null
            ],

            ['name' => 'Administrador', 'email' => 'desenvolvedor@fellipelli.com.br', 'password' => $password, 'email_verified_at' => now(), 'created_at' => now(), 'sa' => 0, 'admin' => 1, 'usuario' => 1, 'gestor' => 1, 'ativo' => 1, 'cliente_id' => null],
            ['name' => 'Gestor', 'email' => 'arley.rincon@fellipelli.com.br', 'password' => $password, 'email_verified_at' => now(), 'created_at' => now(), 'sa' => 0, 'admin' => 0, 'usuario' => 1, 'gestor' => 1, 'ativo' => 1, 'cliente_id' => 1],

        ];

        DB::table('users')->insert($perfis);
    }
}