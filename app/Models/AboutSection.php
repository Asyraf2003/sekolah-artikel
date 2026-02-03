<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutSection extends Model
{
    protected $table = 'about_sections';

    protected $fillable = [
        'vision_content_html_id','vision_content_html_en','vision_content_html_ar',
        'vision_content_delta_id','vision_content_delta_en','vision_content_delta_ar',

        'mission_content_html_id','mission_content_html_en','mission_content_html_ar',
        'mission_content_delta_id','mission_content_delta_en','mission_content_delta_ar',
    ];

    protected $casts = [
        'vision_content_delta_id' => 'array',
        'vision_content_delta_en' => 'array',
        'vision_content_delta_ar' => 'array',

        'mission_content_delta_id' => 'array',
        'mission_content_delta_en' => 'array',
        'mission_content_delta_ar' => 'array',
    ];

    /**
     * Satu halaman About: ambil row pertama (buat display/edit).
     */
    public static function singleton(): self
    {
        return static::query()->first() ?? static::query()->create([]);
    }

    /** ===== DISPLAY HELPERS (HTML) ===== */
    public function visionHtml(?string $locale = null): string
    {
        return $this->localizedHtml('vision_content_html', $locale);
    }

    public function missionHtml(?string $locale = null): string
    {
        return $this->localizedHtml('mission_content_html', $locale);
    }

    /** ===== EDIT HELPERS (DELTA) ===== */
    public function visionDelta(?string $locale = null): array
    {
        return $this->localizedDelta('vision_content_delta', $locale);
    }

    public function missionDelta(?string $locale = null): array
    {
        return $this->localizedDelta('mission_content_delta', $locale);
    }

    /** ===== INTERNAL ===== */
    protected function localizedHtml(string $baseKey, ?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        foreach ($this->fallbackColumns($baseKey, $locale) as $col) {
            $v = $this->{$col} ?? null;
            if (is_string($v) && trim($v) !== '') return $v;
        }

        return '';
    }

    protected function localizedDelta(string $baseKey, ?string $locale = null): array
    {
        $locale ??= app()->getLocale();

        foreach ($this->fallbackColumns($baseKey, $locale) as $col) {
            $v = $this->{$col} ?? null;

            // normal case: sudah di-cast jadi array
            if (is_array($v) && !empty($v)) return $v;

            // jaga-jaga kalau pernah tersimpan string JSON
            if (is_string($v) && trim($v) !== '') {
                $decoded = json_decode($v, true);
                if (is_array($decoded)) return $decoded;
            }
        }

        return [];
    }

    protected function fallbackColumns(string $baseKey, string $locale): array
    {
        // Prioritas: locale aktif -> id -> en -> ar
        return [
            "{$baseKey}_{$locale}",
            "{$baseKey}_id",
            "{$baseKey}_en",
            "{$baseKey}_ar",
        ];
    }
}
