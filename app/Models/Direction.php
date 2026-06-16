<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Direction extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $connection = 'mamore';
    protected $table = 'direcciones';
    protected $fillable = ['nombre'];

    public function solicitudCompra()
    {
        return $this->hasMany(SolicitudCompra::class);
    }
}
