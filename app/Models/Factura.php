<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Factura extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable, SoftDeletes;

    protected $fillable = ['solicitudcompra_id', 'provider_id', 'registeruser_id',
                            'tipofactura', 'fechafactura', 'montofactura', 'nrofactura',
                            'nroautorizacion', 'nrocontrol' ,'fechaingreso', 'gestion',
                            'condicion', 'deleteuser_id', 'sucursal_id', 'deleted_at'
                        ];

    public function solicitud()
    {
        return $this->belongsTo(SolicitudCompra::class, 'solicitudcompra_id');
    }

    public function proveedor()
    {
        return $this->belongsTo(Provider::class, 'provider_id');
    }
    public function detalle()
    {
        return $this->hasMany(DetalleFactura::class, 'factura_id');
    }
}
