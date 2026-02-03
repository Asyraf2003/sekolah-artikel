<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title_id','title_en','title_ar',
        'desc_id','desc_en','desc_ar',
        'event_date','link_url',
        'is_published','published_at','sort_order',
    ];

    protected $casts = [
        'event_date'    => 'date',
        'published_at'  => 'datetime',
        'is_published'  => 'boolean',
        'sort_order'    => 'integer',
    ];

    private function normalizeLocale(?string $loc): string
    {
        $loc = $loc ?: app()->getLocale();
        $loc = strtolower($loc);
        // aman kalau dapat "id_ID", "en_US", dll
        return substr($loc, 0, 2);
    }

    // Ambil judul sesuai locale (fallback ID → EN → AR → '')
    public function titleFor(?string $loc = null): string
    {
        $loc = $this->normalizeLocale($loc);

        $map = ['id' => 'title_id', 'en' => 'title_en', 'ar' => 'title_ar'];
        $k = $map[$loc] ?? 'title_id';

        return $this->{$k}
            ?: $this->title_id
            ?: $this->title_en
            ?: $this->title_ar
            ?: '';
    }

    public function descFor(?string $loc = null): ?string
    {
        $loc = $this->normalizeLocale($loc);

        $map = ['id' => 'desc_id', 'en' => 'desc_en', 'ar' => 'desc_ar'];
        $k = $map[$loc] ?? 'desc_id';

        return $this->{$k}
            ?: $this->desc_id
            ?: $this->desc_en
            ?: $this->desc_ar
            ?: null;
    }

    /** Scope: hanya yang sudah terbit */
    public function scopePublished(Builder $q): Builder
    {
        return $q->where('is_published', true)
            ->where(function (Builder $qq) {
                $qq->whereNull('published_at')
                   ->orWhere('published_at', '<=', now());
            });
    }

    /** Scope: urutan default */
    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('sort_order')
                 ->orderByDesc('event_date');
    }
}
