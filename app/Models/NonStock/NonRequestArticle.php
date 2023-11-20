<?php

namespace App\Models\NonStock;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NonRequestArticle extends Model
{
    use HasFactory;

    protected $fillable = [
        'non_request_id',
        'non_article_id',
        'quantity',
    ];
}
