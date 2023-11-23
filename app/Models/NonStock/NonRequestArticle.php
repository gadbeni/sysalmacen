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
        'article_presentation_id',
        'quantity',
        'unit_price',
        'reference_price',
    ];
    //establece la relacion con la tabla nonStockArticle
    public function nonStockArticle()
    {
        return $this->belongsTo(NonStockArticle::class, 'non_article_id');
    }
    //establece la relacion con la tabla ArticlePresentation
    public function articlePresentation()
    {
        return $this->belongsTo(ArticlePresentation::class, 'article_presentation_id');
    }
}
