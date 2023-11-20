<?php

namespace App\Models\NonStock;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NonStockRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'sucursal_id',
        'subSucursal_id',
        'registerUser_id',
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
    ];
}
