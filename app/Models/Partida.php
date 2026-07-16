<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Partida extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;
    protected $fillable = ['codigo', 'nombre'];

    public $additional_attributes = ['full_code'];

    public function getFullCodeAttribute()
    {
        return $this->codigo . ' - ' . $this->nombre;
    }
}
