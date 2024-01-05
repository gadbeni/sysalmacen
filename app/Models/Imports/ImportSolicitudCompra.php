<?php

namespace App\Models\Imports;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Sucursal;
use App\Models\Unit;
use App\Models\Direction;
use App\Models\Modality;

class ImportSolicitudCompra extends Model
{
    use HasFactory;
    protected $table = 'import_solicitud_compras';
    
    protected $fillable = [
        'sucursal_id',
        'inventarioAlmacen_id',
        'direccionadministrativa',
        'unidadadministrativa',
        'modality_id',
        'registeruser_id',

        'fechaingreso', 
        'gestion', 
        'condicion', 
        'deleteuser_id',
        'stock',
        'subSucursal_id',
        'import'
                            
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
        return $this->hasMany(ImportFactura::class, 'solicitudcompra_id');
    }
    
    public function modality()
    {
        return $this->belongsTo(Modality::class, 'modality_id');
    }
}
