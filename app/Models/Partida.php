<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partida extends Model
{
    use HasFactory;
    protected $fillable = ['codigo', 'nombre'];

    public $additional_attributes = ['full_code'];

    public function getFullCodeAttribute()
    {
        return $this->codigo . ' - ' . $this->nombre;
    }
}
