@php
  $loc = $loc ?? app()->getLocale();
  $rtl = $rtl ?? ($loc === 'ar');

  $heroUrl = $heroUrl ?? function (?string $p) {
    if (!$p) return null;
    if (\Illuminate\Support\Str::startsWith($p, ['http://','https://'])) return $p;
    if (\Illuminate\Support\Str::startsWith($p, ['storage/','article/','articles/'])) {
      return \Illuminate\Support\Facades\Storage::url($p);
    }
    return asset($p);
  };

  $titleFor = $titleFor ?? fn($m) => $m?->{"title_{$loc}"} ?: ($m->title_id ?? '');
  $excerptFor = $excerptFor ?? fn($m) => $m?->{"excerpt_{$loc}"} ?: ($m->excerpt_id ?? '');

  $contentHtmlFor = $contentHtmlFor ?? function ($m) use ($loc) {
    $html = $m?->{"content_html_{$loc}"} ?? null;
    if (is_string($html) && trim($html) !== '') return $html;

    $html = $m?->content_html_id ?? null;
    if (is_string($html) && trim($html) !== '') return $html;

    return '';
  };
@endphp
