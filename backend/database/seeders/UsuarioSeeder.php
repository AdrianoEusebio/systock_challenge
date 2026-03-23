<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Domain\Shared\Enums\TipoUsuario;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('pt_BR');

        // 1. Admin Principal
        $this->upsertUsuario([
            'nome' => 'Administrador do Sistema',
            'cpf' => '11111111111',
            'email' => 'admin@systock.com.mx',
            'senha' => Hash::make('123456'),
            'tipo_usuario' => TipoUsuario::ADMIN->value,
        ]);

        // 2. Cliente Principal
        $this->upsertUsuario([
            'nome' => 'Adriano Eusebio',
            'cpf' => '22222222222',
            'email' => 'cliente@systock.com.mx',
            'senha' => Hash::make('123456'),
            'tipo_usuario' => TipoUsuario::CLIENTE->value,
        ]);

        // 3. Usuários aleatórios
        for ($i = 0; $i < 3; $i++) {
            $this->upsertUsuario([
                'nome' => $faker->name,
                'cpf' => $faker->cpf(false),
                'email' => $faker->unique()->safeEmail,
                'senha' => Hash::make('password'),
                'tipo_usuario' => TipoUsuario::CLIENTE->value,
            ]);
        }
    }

    private function upsertUsuario(array $data): void
    {
        DB::table('usuario')->updateOrInsert(
            ['email' => $data['email']],
            array_merge($data, [
                'usuario_inclusao' => 'System',
                'usuario_alteracao' => 'System',
                'created_at' => now(),
                'updated_at' => now(),
            ])
        );
    }
}