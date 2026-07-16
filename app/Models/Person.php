<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Person extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $connection = 'mamore';
    protected $table = 'people';
    protected $fillable = ['first_name', 'last_name', 'paternal_surname', 'maternal_surname'];
}
