<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetalleFactura extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable, SoftDeletes;
    protected $fillable = [
        'factura_id','registeruser_id', 'article_id', 'cantsolicitada', 'precio', 'totalbs',
        'cantrestante', 'fechaingreso', 'gestion','histcantsolicitada', 'histprecio', 'histtotalbs',
        'parent_id',
        'histcantrestante', 'histfechaingreso', 'histgestion', 'hist', 'condicion', 'deleteuser_id', 'sucursal_id', 'deleted_at',
        'deleteObservation', 'HistInvDelete_id'
      ];

    public function factura()
    {
      return $this->belongsTo(Factura::class,'factura_id');
    }

    public function article()
    {
      return $this->hasMany(Article::class, 'article_id');
    }
}
