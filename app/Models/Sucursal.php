<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Prophecy\Promise\ReturnPromise;

class Sucursal extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $fillable = ['nombre', 'ubicacion', 'condicion'];

    public function sucursalUser()
    {
        return $this->hasMany(sucursalUser::class);
    }

    public function sucursalDirecion()
    {
        return $this->hasMany(SucursalDireccion::class);
    }
}
