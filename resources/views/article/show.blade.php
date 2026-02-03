@php
  $loc = app()->getLocale();
  $rtl = ($loc === 'ar');

  $tr = function(string $key, string $fallback){
    $v = __($key);
    return $v === $key ? $fallback : $v;
  };

  $heroUrl = function (?string $p) {
    if (!$p) return null;
    if (\Illuminate\Support\Str::startsWith($p, ['http://','https://'])) return $p;
    if (\Illuminate\Support\Str::startsWith($p, ['storage/','article/','articles/'])) {
      return \Illuminate\Support\Facades\Storage::url($p);
    }
    return asset($p);
  };

  $titleFor = fn($m) => $m?->{"title_{$loc}"} ?: ($m->title_id ?? '');

  $contentHtmlFor = function ($m) use ($loc) {
    $html = $m?->{"content_html_{$loc}"} ?? null;
    if (is_string($html) && trim($html) !== '') return $html;

    $html = $m?->content_html_id ?? null;
    if (is_string($html) && trim($html) !== '') return $html;

    return '';
  };

  $aTitle = $titleFor($article);
  $aHero  = $heroUrl($article->hero_image);
  $aDate  = optional($article->published_at)->translatedFormat('d M Y');
@endphp

<x-page.index :title="$aTitle">
  <div class="min-h-screen bg-white dark:bg-gray-950">

    <section class="border-b border-gray-200/70 dark:border-gray-800/60">
      <div class="mx-auto max-w-8xl px-4 sm:px-6 lg:px-8 py-6">
        <nav class="text-sm text-gray-500 dark:text-gray-400">
          <a href="{{ url('/') }}" class="hover:text-indigo-600">{{ $tr('messages.article_page_home','Home') }}</a> /
          <a href="{{ route('articles.index') }}" class="hover:text-indigo-600">{{ $tr('messages.article_page_article','Artikel') }}</a> /
          <span class="text-gray-700 dark:text-gray-200">{{ $aTitle }}</span>
        </nav>
      </div>
    </section>

    <section class="mx-auto max-w-8xl px-4 sm:px-6 lg:px-8 py-10">
      <div class="grid grid-cols-12 gap-6">

        {{-- LEFT SIDEBAR: Tag populer --}}
        <aside class="hidden lg:block col-span-12 lg:col-span-3 space-y-6 sticky top-20 self-start h-fit">
          @include('article.partials._tags_popular', ['tagsPopular' => $tagsPopular])
        </aside>

        {{-- MAIN --}}
        <main class="col-span-12 lg:col-span-6 space-y-8">
          <article class="space-y-4">
            <h1 class="text-2xl sm:text-3xl font-bold leading-tight">{{ $aTitle }}</h1>

            <div class="flex flex-wrap items-center gap-3 text-sm text-gray-500 dark:text-gray-400">
              @if($article->author)
                <span>{{ $tr('messages.article_by','Oleh') }} <span class="font-medium">{{ $article->author->name }}</span></span>
                <span>•</span>
              @endif
              @if($aDate)
                <time datetime="{{ $article->published_at->toDateString() }}">{{ $aDate }}</time>
                <span>•</span>
              @endif
              @if($article->reading_time)
                <span>{{ $article->reading_time }} {{ $tr('messages.article_time','menit baca') }}</span>
              @endif
            </div>

            @if($aHero)
              <figure class="rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-800">
                <img src="{{ $aHero }}" alt="{{ $aTitle }}" class="w-full">
              </figure>
            @endif

            @php $bodyHtml = $contentHtmlFor($article); @endphp
            @if($bodyHtml !== '')
              <div class="prose prose-indigo dark:prose-invert max-w-none {{ $rtl ? 'text-right' : '' }}" dir="{{ $rtl ? 'rtl' : 'ltr' }}">
                {!! $bodyHtml !!}
              </div>
            @else
              <div class="rounded-2xl border border-gray-200 dark:border-gray-800 p-6 text-sm text-gray-600 dark:text-gray-300">
                Konten artikel belum ada.
              </div>
            @endif
          </article>

          @include('article.partials._comments', [
            'article' => $article,
            'replyTo' => $replyTo,
            'groupedComments' => $groupedComments,
            'topLevelComments' => $topLevelComments,
          ])

          @if(($related ?? collect())->count())
            <section class="space-y-3">
              <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold">{{ $tr('messages.article_any','Artikel Lainnya') }}</h2>
                <a href="{{ route('articles.index') }}" class="text-sm text-indigo-600 hover:underline">
                  {{ $tr('messages.article_page_show_all','Lihat semua') }}
                </a>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($related as $r)
                  @php
                    $rTitle = $titleFor($r);
                    $rImg = $heroUrl($r->hero_image);
                  @endphp
                  <a href="{{ route('article', $r->slug) }}" class="group rounded-2xl border border-gray-200 dark:border-gray-800 overflow-hidden hover:shadow-md transition">
                    <div class="aspect-[4/3] bg-gray-200 dark:bg-gray-800 overflow-hidden">
                      @if($rImg)
                        <img src="{{ $rImg }}" alt="{{ $rTitle }}" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300">
                      @endif
                    </div>
                    <div class="p-4 space-y-1.5">
                      <h3 class="font-semibold group-hover:text-indigo-600 line-clamp-2">{{ $rTitle }}</h3>
                      <div class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $r->author->name ?? 'Admin' }} • {{ optional($r->published_at)->translatedFormat('d M Y') }}
                      </div>
                    </div>
                  </a>
                @endforeach
              </div>
            </section>
          @endif
        </main>

        {{-- RIGHT SIDEBAR: Top + Featured --}}
        <aside class="hidden lg:block col-span-12 lg:col-span-3 space-y-6 sticky top-20 self-start h-fit">
          @include('article.partials._top_list', ['top'=>$top, 'titleFor'=>$titleFor, 'heroUrl'=>$heroUrl])
          @include('article.partials._featured_grid', ['featured'=>$featured, 'titleFor'=>$titleFor, 'heroUrl'=>$heroUrl])
        </aside>

      </div>
    </section>
  </div>
</x-page.index>
