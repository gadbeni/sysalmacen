<?php

namespace App\Models\Imports;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Provider;

class ImportFactura extends Model
{
    use HasFactory;
    protected $table = 'import_facturas';

    protected $fillable = ['solicitudcompra_id', 'provider_id', 'registeruser_id',
                            'tipofactura', 'fechafactura', 'montofactura', 'nrofactura',
                            'nroautorizacion', 'nrocontrol' ,'fechaingreso', 'gestion',
                            'condicion', 'deleteuser_id', 'sucursal_id'
    ];

    public function solicitud()
    {
        return $this->belongsTo(ImportSolicitudCompra::class, 'solicitudcompra_id');
    }

    public function proveedor()
    {
        return $this->belongsTo(Provider::class, 'provider_id');
    }
    public function detalle()
    {
        return $this->hasMany(importDetalleFactura::class, 'factura_id');
    }
}
