<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProdutoSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('pt_BR');

        $usuarios = DB::table('usuario')->get();

        foreach ($usuarios as $usuario) {
            $quantidadeProdutos = $usuario->email === 'admin@systock.com.mx' ? 5 : 20;

            for ($i = 0; $i < $quantidadeProdutos; $i++) {
                DB::table('produto')->insert([
                    'nome' => $faker->randomElement([
                        'Monitor AOC 24"', 'Teclado Mecânico RGB', 'Mouse Gamer Pro',
                        'Notebook Gamer i7', 'Impressora Laser Jet', 'Cadeira Office',
                        'Roteador WiFi 6', 'HD Externo 1TB', 'Webcam Full HD',
                        'Fone de Ouvido Noise Cancelling', 'Kit Cabos HDMI', 'Base Cooler'
                    ]) . " " . $faker->word,
                    'preco' => $faker->randomFloat(2, 50, 6500),
                    'descricao' => $faker->sentence(10),
                    'usuario_id' => $usuario->id,
                    'usuario_inclusao' => $usuario->cpf,
                    'usuario_alteracao' => $usuario->cpf,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}