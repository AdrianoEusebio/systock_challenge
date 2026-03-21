<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        // Usuário Admin
        DB::table('usuario')->insert([
            'nome' => 'Adriano Admin',
            'cpf' => '111.111.111-11',
            'email' => 'admin@systock.com',
            'senha' => Hash::make('senha123'),
            'tipo_usuario' => 1,
            'usuario_inclusao' => 'Sistema',
            'usuario_alteracao' => 'Sistema',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Usuários Comuns
        for ($i = 1; $i <= 3; $i++) {
            DB::table('usuario')->insert([
                'nome' => "Operador Logístico $i",
                'cpf' => "222.222.222-0$i",
                'email' => "operador$i@systock.com",
                'senha' => Hash::make('senha123'),
                'tipo_usuario' => 2,
                'usuario_inclusao' => '111.111.111-11',
                'usuario_alteracao' => '111.111.111-11',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}