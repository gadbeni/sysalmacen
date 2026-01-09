<?php

namespace App\Models\NonStock;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NonStockArticle extends Model
{
    use HasFactory;

    protected $fillable = [
        'registerUser_id',
        'name_description',
    ];
}
