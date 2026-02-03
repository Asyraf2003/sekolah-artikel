<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title_id','title_en','title_ar',
        'place_id','place_en','place_ar',
        'event_date','link_url',
        'is_published','sort_order',
    ];

    protected $casts = [
        'event_date'   => 'datetime',
        'is_published' => 'boolean',
        'sort_order'   => 'integer',
    ];

    private function normalizeLocale(?string $loc): string
    {
        $loc = $loc ?: app()->getLocale();
        $loc = strtolower($loc);
        return substr($loc, 0, 2); // id_ID -> id
    }

    public function titleFor(?string $loc = null): string
    {
        $loc = $this->normalizeLocale($loc);
        $map = ['id'=>'title_id','en'=>'title_en','ar'=>'title_ar'];
        $k = $map[$loc] ?? 'title_id';

        return $this->{$k}
            ?: $this->title_id
            ?: $this->title_en
            ?: $this->title_ar
            ?: '';
    }

    public function placeFor(?string $loc = null): string
    {
        $loc = $this->normalizeLocale($loc);
        $map = ['id'=>'place_id','en'=>'place_en','ar'=>'place_ar'];
        $k = $map[$loc] ?? 'place_id';

        return $this->{$k}
            ?: $this->place_id
            ?: $this->place_en
            ?: $this->place_ar
            ?: '';
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('is_published', true);
    }

    public function scopeUpcoming(Builder $q): Builder
    {
        return $q->where('event_date', '>=', now());
    }

    // default publik: event terdekat dulu
    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('event_date')->orderBy('sort_order')->orderBy('id');
    }
}
