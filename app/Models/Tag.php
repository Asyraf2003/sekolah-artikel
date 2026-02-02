<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['name','slug','use_count'];

    protected $casts = [
        'use_count' => 'integer',
    ];

    public function scopePopular($q)
    {
        return $q->orderByDesc('use_count');
    }

    public function scopePopularPublished($q)
    {
        return $q
            ->withCount([
                'articles as published_articles_count' => fn($a) => $a->published()
            ])
            ->having('published_articles_count', '>', 0)
            ->orderByDesc('published_articles_count')
            ->orderByDesc('use_count'); 
    }

    public function articles()
    {
        return $this->belongsToMany(\App\Models\Article::class, 'article_tag', 'tag_id', 'article_id');
    }

}
