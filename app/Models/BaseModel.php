<?php

namespace App\Models;

use App\Models\Traits\HasAuditAttributes;
use Carbon\Traits\Timestamp;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tymon\JWTAuth\Contracts\JWTSubject;

abstract class BaseModel extends Authenticatable implements JWTSubject
{
    use SoftDeletes, HasAuditAttributes, Timestamp;

    protected $hidden = [
        'usuario_inclusao',
        'usuario_alteracao',
        'deleted_at',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Override standard password field to use 'senha'
     */
    public function getAuthPassword()
    {
        return $this->senha;
    }
}
