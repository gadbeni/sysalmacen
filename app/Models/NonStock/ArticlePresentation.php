<?php

namespace App\Models\NonStock;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticlePresentation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_presentation',
    ];
}
