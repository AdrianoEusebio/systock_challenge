<?php

namespace App\Models;

class Usuario extends BaseModel
{
    protected $table = 'usuario';

    protected $fillable = [
        'nome',
        'cpf',
        'email',
        'senha',
        'tipo_usuario'
    ];

    protected $hidden = [
        'senha'
    ];
}