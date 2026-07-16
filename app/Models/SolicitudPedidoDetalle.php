<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class SolicitudPedidoDetalle extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'solicitudPedido_id',
        'gestion',
        'sucursal_id',
        'article_id',
        'cantsolicitada',
        'cantentregada',
        'details',
        'status',
        'registerUser_Id',
        'deleted_at',
        'deletedUser_Id',

        'jsonDetails_id',
        'jsonCant'
    ];
    

    public function solicitud()
    {
        return $this->belongsTo(SolicitudPedido::class, 'solicitudPedido_id');
    }

    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id');
    }



}
