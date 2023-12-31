<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudCompra extends Model
{
    use HasFactory;

    protected $fillable = [
        'sucursal_id',
        'inventarioAlmacen_id',
        'direccionadministrativa',
        'unidadadministrativa',
        'modality_id',
        'registeruser_id',
        'nrosolicitud',
        'fechaingreso', 
        'gestion', 
        'condicion', 
        'deleteuser_id',
        'stock',
        'subSucursal_id'
                            
    ];


    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function unidad()
    {
        return $this->belongsTo(Unit::class, 'unidadadministrativa');
    }
                            
    public function direccion()
    {
        return $this->belongsTo(Direction::class, 'direccionadministrativa');
    }
    public function factura()
    {
        return $this->hasMany(Factura::class, 'solicitudcompra_id');
    }
    
    public function modality()
    {
        return $this->belongsTo(Modality::class, 'modality_id');
    }
}
