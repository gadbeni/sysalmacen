<?php

namespace App\Models\NonStock;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Sucursal;
use App\Models\User;

class NonStockRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'sucursal_id',
        'subSucursal_id',
        'registerUser_id',
        'registerUser_name',
        'date_request',
        'gestion',
        'nro_request',
        'direction_id',
        'direction_name',
        'unit_id',
        'unit_name',
        'job',
        'status',
        'date_status',
        'statusUser_id',
        'deleted_at',
    ];
    //Establece la relacion con la tabla sucursal
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }
    //Establece la relacion con la tabla Usuario
    public function user()
    {
        return $this->belongsTo(User::class, 'registerUser_id');
    }
}
