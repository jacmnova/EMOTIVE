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
                'ativo' => 1,
                'cliente_id' => 1
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
                'gestor' => 1, 
                'ativo' => 1,
                'cliente_id' => 2
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
                'ativo' => 1,
                'cliente_id' => 3
            ],

            ['name' => 'João Silva', 'email' => 'joao.silva@gmail.com', 'password' => '$2y$12$2VZ2YQsmwtZjQKBvsAfgVuNZUyAyalJCv04NnVNMsgyn4SpYcczZO', 'email_verified_at' => now(), 'created_at' => now(), 'sa' => 0, 'admin' => 0, 'usuario' => 1, 'gestor' => 0, 'ativo' => 1, 'cliente_id' => 1],
            ['name' => 'Maria Oliveira', 'email' => 'maria.oliveira@gmail.com', 'password' => '$2y$12$2VZ2YQsmwtZjQKBvsAfgVuNZUyAyalJCv04NnVNMsgyn4SpYcczZO', 'email_verified_at' => now(), 'created_at' => now(), 'sa' => 0, 'admin' => 0, 'usuario' => 1, 'gestor' => 0, 'ativo' => 1, 'cliente_id' => 1],
            ['name' => 'Carlos Santos', 'email' => 'carlos.santos@gmail.com', 'password' => '$2y$12$2VZ2YQsmwtZjQKBvsAfgVuNZUyAyalJCv04NnVNMsgyn4SpYcczZO', 'email_verified_at' => now(), 'created_at' => now(), 'sa' => 0, 'admin' => 0, 'usuario' => 1, 'gestor' => 0, 'ativo' => 1, 'cliente_id' => 1],
            ['name' => 'Ana Costa', 'email' => 'ana.costa@gmail.com', 'password' => '$2y$12$2VZ2YQsmwtZjQKBvsAfgVuNZUyAyalJCv04NnVNMsgyn4SpYcczZO', 'email_verified_at' => now(), 'created_at' => now(), 'sa' => 0, 'admin' => 0, 'usuario' => 1, 'gestor' => 0, 'ativo' => 1, 'cliente_id' => 1],
            ['name' => 'Lucas Lima', 'email' => 'lucas.lima@gmail.com', 'password' => '$2y$12$2VZ2YQsmwtZjQKBvsAfgVuNZUyAyalJCv04NnVNMsgyn4SpYcczZO', 'email_verified_at' => now(), 'created_at' => now(), 'sa' => 0, 'admin' => 0, 'usuario' => 1, 'gestor' => 0, 'ativo' => 1, 'cliente_id' => 1],
            ['name' => 'Juliana Alves', 'email' => 'juliana.alves@gmail.com', 'password' => '$2y$12$2VZ2YQsmwtZjQKBvsAfgVuNZUyAyalJCv04NnVNMsgyn4SpYcczZO', 'email_verified_at' => now(), 'created_at' => now(), 'sa' => 0, 'admin' => 0, 'usuario' => 1, 'gestor' => 0, 'ativo' => 1, 'cliente_id' => 1],
            ['name' => 'Rodrigo Teixeira', 'email' => 'rodrigo.teixeira@gmail.com', 'password' => '$2y$12$2VZ2YQsmwtZjQKBvsAfgVuNZUyAyalJCv04NnVNMsgyn4SpYcczZO', 'email_verified_at' => now(), 'created_at' => now(), 'sa' => 0, 'admin' => 0, 'usuario' => 1, 'gestor' => 0, 'ativo' => 1, 'cliente_id' => 1],
            ['name' => 'Paula Mendes', 'email' => 'paula.mendes@gmail.com', 'password' => '$2y$12$2VZ2YQsmwtZjQKBvsAfgVuNZUyAyalJCv04NnVNMsgyn4SpYcczZO', 'email_verified_at' => now(), 'created_at' => now(), 'sa' => 0, 'admin' => 0, 'usuario' => 1, 'gestor' => 0, 'ativo' => 1, 'cliente_id' => 2],
            ['name' => 'Felipe Araújo', 'email' => 'felipe.araujo@gmail.com', 'password' => '$2y$12$2VZ2YQsmwtZjQKBvsAfgVuNZUyAyalJCv04NnVNMsgyn4SpYcczZO', 'email_verified_at' => now(), 'created_at' => now(), 'sa' => 0, 'admin' => 0, 'usuario' => 1, 'gestor' => 0, 'ativo' => 1, 'cliente_id' => 2],
            ['name' => 'Camila Reis', 'email' => 'camila.reis@gmail.com', 'password' => '$2y$12$2VZ2YQsmwtZjQKBvsAfgVuNZUyAyalJCv04NnVNMsgyn4SpYcczZO', 'email_verified_at' => now(), 'created_at' => now(), 'sa' => 0, 'admin' => 0, 'usuario' => 1, 'gestor' => 0, 'ativo' => 1, 'cliente_id' => 2],
            ['name' => 'Marcos Souza', 'email' => 'marcos.souza@gmail.com', 'password' => '$2y$12$2VZ2YQsmwtZjQKBvsAfgVuNZUyAyalJCv04NnVNMsgyn4SpYcczZO', 'email_verified_at' => now(), 'created_at' => now(), 'sa' => 0, 'admin' => 0, 'usuario' => 1, 'gestor' => 0, 'ativo' => 1, 'cliente_id' => 2],
            ['name' => 'Fernanda Rocha', 'email' => 'fernanda.rocha@gmail.com', 'password' => '$2y$12$2VZ2YQsmwtZjQKBvsAfgVuNZUyAyalJCv04NnVNMsgyn4SpYcczZO', 'email_verified_at' => now(), 'created_at' => now(), 'sa' => 0, 'admin' => 0, 'usuario' => 1, 'gestor' => 0, 'ativo' => 1,'cliente_id' => 2],
            ['name' => 'Thiago Martins', 'email' => 'thiago.martins@gmail.com', 'password' => '$2y$12$2VZ2YQsmwtZjQKBvsAfgVuNZUyAyalJCv04NnVNMsgyn4SpYcczZO', 'email_verified_at' => now(), 'created_at' => now(), 'sa' => 0, 'admin' => 0, 'usuario' => 1, 'gestor' => 0, 'ativo' => 1, 'cliente_id' => 2],
            ['name' => 'Débora Lima', 'email' => 'debora.lima@gmail.com', 'password' => '$2y$12$2VZ2YQsmwtZjQKBvsAfgVuNZUyAyalJCv04NnVNMsgyn4SpYcczZO', 'email_verified_at' => now(), 'created_at' => now(), 'sa' => 0, 'admin' => 0, 'usuario' => 1, 'gestor' => 0, 'ativo' => 1, 'cliente_id' => 2],
            ['name' => 'Diego Ribeiro', 'email' => 'diego.ribeiro@gmail.com', 'password' => '$2y$12$2VZ2YQsmwtZjQKBvsAfgVuNZUyAyalJCv04NnVNMsgyn4SpYcczZO', 'email_verified_at' => now(), 'created_at' => now(), 'sa' => 0, 'admin' => 0, 'usuario' => 1, 'gestor' => 0, 'ativo' => 1, 'cliente_id' => 3],
            ['name' => 'Isabela Costa', 'email' => 'isabela.costa@gmail.com', 'password' => '$2y$12$2VZ2YQsmwtZjQKBvsAfgVuNZUyAyalJCv04NnVNMsgyn4SpYcczZO', 'email_verified_at' => now(), 'created_at' => now(), 'sa' => 0, 'admin' => 0, 'usuario' => 1, 'gestor' => 0, 'ativo' => 1, 'cliente_id' => 3],
            ['name' => 'Leonardo Dias', 'email' => 'leonardo.dias@gmail.com', 'password' => '$2y$12$2VZ2YQsmwtZjQKBvsAfgVuNZUyAyalJCv04NnVNMsgyn4SpYcczZO', 'email_verified_at' => now(), 'created_at' => now(), 'sa' => 0, 'admin' => 0, 'usuario' => 1, 'gestor' => 0, 'ativo' => 1, 'cliente_id' => 3],
            ['name' => 'Renata Farias', 'email' => 'renata.farias@gmail.com', 'password' => '$2y$12$2VZ2YQsmwtZjQKBvsAfgVuNZUyAyalJCv04NnVNMsgyn4SpYcczZO', 'email_verified_at' => now(), 'created_at' => now(), 'sa' => 0, 'admin' => 0, 'usuario' => 1, 'gestor' => 0, 'ativo' => 1, 'cliente_id' => 3],
            ['name' => 'Bruno Cardoso', 'email' => 'bruno.cardoso@gmail.com', 'password' => '$2y$12$2VZ2YQsmwtZjQKBvsAfgVuNZUyAyalJCv04NnVNMsgyn4SpYcczZO', 'email_verified_at' => now(), 'created_at' => now(), 'sa' => 0, 'admin' => 0, 'usuario' => 1, 'gestor' => 0, 'ativo' => 1, 'cliente_id' => 3],
            ['name' => 'Alessandra Vieira', 'email' => 'alessandra.vieira@gmail.com', 'password' => '$2y$12$2VZ2YQsmwtZjQKBvsAfgVuNZUyAyalJCv04NnVNMsgyn4SpYcczZO', 'email_verified_at' => now(), 'created_at' => now(), 'sa' => 0, 'admin' => 0, 'usuario' => 1, 'gestor' => 0, 'ativo' => 1,'cliente_id' => 3]


        ];

        DB::table('users')->insert($perfis);
    }
}