@php
  $loc = app()->getLocale();

  $heroUrl = function (?string $p) {
    if (!$p) return null;
    if (\Illuminate\Support\Str::startsWith($p, ['http://','https://'])) return $p;
    if (\Illuminate\Support\Str::startsWith($p, ['storage/','article/','articles/'])) {
      return \Illuminate\Support\Facades\Storage::url($p);
    }
    return asset($p);
  };

  $titleFor = fn($m) => $m?->{"title_{$loc}"} ?: ($m->title_id ?? '');
  $excerptFor = fn($m) => $m?->{"excerpt_{$loc}"} ?: ($m->excerpt_id ?? '');
@endphp

<x-page.index :title="__('messages.article_page_article')">
  <div class="min-h-screen bg-white dark:bg-gray-950">

    @include('article.partials._index_header', [
      'q'=>$q, 'categoriesChip'=>$categoriesChip, 'catSlug'=>$catSlug,
      'tagSlug'=>$tagSlug, 'sort'=>$sort, 'loc'=>$loc
    ])

    <section class="mx-auto max-w-8xl px-4 sm:px-6 lg:px-8 py-10">
      <div class="grid grid-cols-12 gap-6">

        {{-- LEFT: tags populer --}}
        <aside class="hidden lg:block col-span-12 lg:col-span-3 space-y-6 sticky top-20 self-start h-fit">
          @include('article.partials._tags_popular', ['tagsPopular'=>$tagsPopular])
        </aside>

        {{-- MAIN --}}
        <main class="col-span-12 lg:col-span-6 space-y-6">
          @if($articles->count())
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
              @foreach($articles as $a)
                @include('article.partials._card', [
                  'a' => $a,
                  'loc' => $loc,
                  'heroUrl' => $heroUrl,
                  'titleFor' => $titleFor,
                  'excerptFor' => $excerptFor,
                ])
              @endforeach
            </div>

            <div class="mt-6">
              {{ $articles->links() }}
            </div>
          @else
            <div class="rounded-2xl border border-gray-200 dark:border-gray-800 p-6 text-center">
              <div class="text-lg font-semibold mb-1">Tidak ada artikel</div>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                Coba ganti kata kunci / filter.
                <a href="{{ route('articles.index') }}" class="text-indigo-600 hover:underline">Reset</a>
              </p>
            </div>
          @endif
        </main>

        {{-- RIGHT: top + featured --}}
        <aside class="hidden lg:block col-span-12 lg:col-span-3 space-y-6 sticky top-20 self-start h-fit">
          @include('article.partials._top_list', ['top'=>$top, 'titleFor'=>$titleFor, 'heroUrl'=>$heroUrl])
          @include('article.partials._featured_grid', ['featured'=>$featured, 'titleFor'=>$titleFor, 'heroUrl'=>$heroUrl])
        </aside>

      </div>
    </section>
  </div>
</x-page.index>
