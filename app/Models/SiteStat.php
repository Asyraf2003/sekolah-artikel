<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteStat extends Model
{
    protected $table = 'site_stats';

    protected $fillable = [
        'value',
        'label_id','label_en','label_ar',
        'desc_id','desc_en','desc_ar',
        'is_active',
        'sort_order',
        // slot sengaja TIDAK dimasukin biar tidak bisa diubah sembarangan
    ];

    protected $casts = [
        'slot'       => 'integer',
        'value'      => 'integer',
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    private function normalizeLocale(?string $loc): string
    {
        $loc = $loc ?: app()->getLocale();
        $loc = strtolower($loc);
        return substr($loc, 0, 2); // id_ID -> id
    }

    public function labelFor(?string $loc = null): string
    {
        $loc = $this->normalizeLocale($loc);
        $map = ['id' => 'label_id', 'en' => 'label_en', 'ar' => 'label_ar'];
        $k = $map[$loc] ?? 'label_id';

        return $this->{$k}
            ?: $this->label_id
            ?: $this->label_en
            ?: $this->label_ar
            ?: '';
    }

    public function descFor(?string $loc = null): string
    {
        $loc = $this->normalizeLocale($loc);
        $map = ['id' => 'desc_id', 'en' => 'desc_en', 'ar' => 'desc_ar'];
        $k = $map[$loc] ?? 'desc_id';

        return $this->{$k}
            ?: $this->desc_id
            ?: $this->desc_en
            ?: $this->desc_ar
            ?: '';
    }

    // Angka tampil sesuai locale (ar -> Arabic-Indic digits)
    public function valueFor(?string $loc = null): string
    {
        $loc = $this->normalizeLocale($loc);
        $val = (string) ($this->value ?? 0);

        if ($loc !== 'ar') return $val;

        $map = ['0'=>'٠','1'=>'١','2'=>'٢','3'=>'٣','4'=>'٤','5'=>'٥','6'=>'٦','7'=>'٧','8'=>'٨','9'=>'٩'];
        return strtr($val, $map);
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function scopeOrdered($q)
    {
        return $q->orderBy('sort_order')->orderBy('slot');
    }
}
