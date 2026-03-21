<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Produto extends BaseModel
{
    use HasFactory;

    protected $table = 'produto';

    protected $fillable = [
        'nome',
        'preco',
        'descricao',
        'usuario_id',
        'usuario_inclusao',
        'usuario_alteracao',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}