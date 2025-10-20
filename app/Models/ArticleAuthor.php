<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleAuthor extends Model
{
     protected $table = 'article_authors';

    protected $fillable = ['article_id', 'author_id'];
}
