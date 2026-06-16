<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class HistInvDelete extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;
    
    protected $fillable = [
        'inventario_id',
        'gestion',
        'start',
        'startUser_id',
        'finish',
        'finishUser_id',
        'observation',
        'observation1',
        'deleteObservation',
        'deleted_at',
        'deleteUser_id',

        'registeruser_id',
        'nameFile',
        'routeFile'
    ];

    public function histDetalleFactura()
    {
        return $this->hasMany(DetalleFactura::class, 'HistInvDelete_id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'registeruser_id');
    }

}
