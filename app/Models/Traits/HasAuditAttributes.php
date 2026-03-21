<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\Auth;

trait HasAuditAttributes
{
    protected static function bootHasAuditAttributes()
    {
        static::creating(function ($model) {
            $model->usuario_inclusao = Auth::user()?->cpf ?? 'undefined';
            $model->usuario_alteracao = Auth::user()?->cpf ?? 'undefined';
        });

        static::updating(function ($model) {
            $model->usuario_alteracao = Auth::user()?->cpf ?? 'undefined';
        });
    }
}