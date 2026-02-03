<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Extracurricular extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name_id','name_en','name_ar',
        'is_published','sort_order',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'sort_order'   => 'integer',
    ];

    private function normalizeLocale(?string $loc): string
    {
        $loc = $loc ?: app()->getLocale();
        $loc = strtolower($loc);
        return substr($loc, 0, 2); // id_ID -> id
    }

    public function nameFor(?string $loc = null): string
    {
        $loc = $this->normalizeLocale($loc);
        $map = ['id'=>'name_id','en'=>'name_en','ar'=>'name_ar'];
        $k = $map[$loc] ?? 'name_id';

        return $this->{$k}
            ?: $this->name_id
            ?: $this->name_en
            ?: $this->name_ar
            ?: '';
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('is_published', true);
    }

    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('sort_order')->orderBy('id');
    }
}
