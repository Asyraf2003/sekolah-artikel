<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Article extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'author_id',
        'title_id', 'title_en', 'title_ar',
        'slug',
        'hero_image',
        'excerpt_id', 'excerpt_en', 'excerpt_ar',

        'content_delta_id','content_delta_en','content_delta_ar',
        'content_html_id','content_html_en','content_html_ar',

        'status',
        'published_at',
        'is_featured',
        'pinned_until',

        'view_count', 'comment_count', 'share_count', 'reading_time',
    ];

    protected $casts = [
        'published_at'   => 'datetime',
        'pinned_until'   => 'datetime',
        'is_featured'    => 'bool',

        'view_count'     => 'integer',
        'comment_count'  => 'integer',
        'share_count'    => 'integer',
        'reading_time'   => 'integer',

        'content_delta_id' => 'array',
        'content_delta_en' => 'array',
        'content_delta_ar' => 'array',
    ];

    /** RELATIONS */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->hasMany(ArticleLike::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'article_category', 'article_id', 'category_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'article_tag', 'article_id', 'tag_id');
    }

    /** SCOPES */
    public function scopePublished($q)
    {
        return $q->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    // “Scheduled” implicit: status published tapi tanggalnya masih masa depan
    public function scopeScheduled($q)
    {
        return $q->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '>', now());
    }

    public function scopeRecent($q)
    {
        return $q->published()->orderByDesc('published_at');
    }

    public function scopeFeatured($q)
    {
        return $q->published()->where('is_featured', true);
    }

    public function scopePinned($q)
    {
        return $q->published()
            ->where(function ($qq) {
                $qq->whereNull('pinned_until')->orWhere('pinned_until', '>=', now());
            });
    }

    public function scopeTop($q)
    {
        return $q->published()->orderByDesc('view_count');
    }

    public function scopeSearchTitle($q, string $term)
    {
        return $q->whereFullText([
            'title_id', 'title_en', 'title_ar',
            'slug',
            'excerpt_id', 'excerpt_en', 'excerpt_ar',
        ], $term);
    }

    /** HELPERS */
    public function titleFor(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();

        return $this->firstNonEmptyString(
            $this->{"title_{$locale}"} ?? null,
            $this->title_id ?? null,
            $this->title_en ?? null,
            $this->title_ar ?? null,
        ) ?? '';
    }

    protected function firstNonEmptyString(?string ...$values): ?string
    {
        foreach ($values as $v) {
            if (is_string($v) && trim($v) !== '') return $v;
        }
        return null;
    }

    /** SLUG AUTO */
    protected static function booted(): void
    {
        static::saving(function (Article $article) {
            if (blank($article->slug)) {
                $baseTitle = $article->titleFor('id') ?: $article->titleFor('en') ?: 'article';
                $article->slug = static::generateUniqueSlug($baseTitle, $article->id);
            }

            if ($article->status !== 'published') {
                $article->published_at = null;
            }

            if ($article->status === 'published' && is_null($article->published_at)) {
                $article->published_at = now();
            }
        });
    }

    protected static function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $slug = Str::slug($title);
        if ($slug === '') $slug = 'article';

        $base = $slug;
        $i = 2;

        while (
            static::query()
                ->withTrashed()
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}
