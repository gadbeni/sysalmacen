<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Unit extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $connection = 'mamore';
    protected $table = 'unidades';
    protected $fillable = ['nombre', 'direccion_id', 'estado'];

    public function direction()
    {
        return $this->belongsTo(Direction::class, 'direccion_id');
    }

}
