<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class DetalleEgreso extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;


    protected $fillable = [
        'solicitudegreso_id',
        'detallefactura_id',
        'registeruser_id',
        'cantsolicitada',
        'precio',
        'totalbs',
        'gestion',
        'condicion',
        'deleteuser_id',
        'sucursal_id'
    ];
    
}
