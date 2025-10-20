<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleCategory extends Model
{
     protected $fillable = ['category_id', 'article_id'];
}
