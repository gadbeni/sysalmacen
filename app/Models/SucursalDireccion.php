<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class SucursalDireccion extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'sucursal_id', 'direccionAdministrativa_id', 'status', 'deleted_at'
    ];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function direction()
    {
        return $this->belongsTo(Direction::class, 'direccionAdministrativa_id');
    }
}
