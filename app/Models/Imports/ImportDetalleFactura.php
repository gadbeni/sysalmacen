<?php

namespace App\Models\Imports;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Article;

class ImportDetalleFactura extends Model
{
    use HasFactory;
    protected $table = 'import_detalle_facturas';

    protected $fillable = [
        'factura_id','registeruser_id', 'article_id', 'cantsolicitada', 'precio', 'totalbs',
        'cantrestante', 'fechaingreso', 'gestion','histcantsolicitada', 'histprecio', 'histtotalbs',
        'parent_id',
        'histcantrestante', 'histfechaingreso', 'histgestion', 'hist', 'condicion', 'deleteuser_id', 'sucursal_id',
        'deleteObservation', 'HistInvDelete_id'
    ];

    public function factura()
    {
      return $this->belongsTo(ImportFactura::class,'factura_id');
    }

    public function article()
    {
      return $this->hasMany(Article::class, 'article_id');
    }
}
