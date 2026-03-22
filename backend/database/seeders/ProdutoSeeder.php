<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProdutoSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('pt_BR');

        $usuarios = DB::table('usuario')->pluck('id', 'cpf')->toArray();

        for ($i = 0; $i < 30; $i++) {
            $cpfUsuario = array_rand($usuarios);
            $idUsuario = $usuarios[$cpfUsuario];

            DB::table('produto')->insert([
                'nome' => $faker->randomElement([
                    'Monitor AOC 24"', 'Teclado Mecânico RGB', 'Mouse Gamer Pro',
                    'Notebook Gamer i7', 'Impressora Laser Jet', 'Cadeira Office',
                    'Roteador WiFi 6', 'HD Externo 1TB', 'Webcam Full HD',
                    'Fone de Ouvido Noise Cancelling', 'Kit Cabos HDMI', 'Base Cooler'
                ]) . " " . $faker->word,
                'preco' => $faker->randomFloat(2, 50, 6500),
                'descricao' => $faker->sentence(10),
                'usuario_id' => $idUsuario,
                'usuario_inclusao' => $cpfUsuario,
                'usuario_alteracao' => $cpfUsuario,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}