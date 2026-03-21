<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProdutoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('produto')->insert([
            [
                'nome' => 'Motor de Popa 40HP',
                'preco' => 15500.00,
                'descricao' => 'Equipamento essencial para transporte fluvial.',
                'usuario_id' => 1,
                'usuario_inclusao' => '111.111.111-11',
                'usuario_alteracao' => '111.111.111-11',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Colete Salva-Vidas Pro',
                'preco' => 250.00,
                'descricao' => 'Segurança obrigatória para logística fluvial.',
                'usuario_id' => 2,
                'usuario_inclusao' => '222.222.222-01',
                'usuario_alteracao' => '222.222.222-01',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}