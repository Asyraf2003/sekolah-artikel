@include('article.partials._helpers')

<x-page.index :title="__('messages.article_page_article')">
  <div class="min-h-screen bg-white dark:bg-gray-950">

    @include('article.partials._index_header', [
      'q'=>$q, 'categoriesChip'=>$categoriesChip, 'catSlug'=>$catSlug,
      'tagSlug'=>$tagSlug, 'sort'=>$sort, 'loc'=>$loc
    ])

    <section class="mx-auto max-w-8xl px-4 sm:px-6 lg:px-8 py-10">
      <div class="grid grid-cols-12 gap-6">

        <main class="col-span-12 lg:col-span-9 space-y-6">
          @if($articles->count())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
              @foreach($articles as $a)
                @include('article.partials._card', ['a'=>$a, 'loc'=>$loc, 'heroUrl'=>$heroUrl])
              @endforeach
            </div>

            <div class="mt-6">
              {{ $articles->links() }}
            </div>
          @else
            <div class="rounded-2xl border border-gray-200 dark:border-gray-800 p-6 text-center">
              <div class="text-lg font-semibold mb-1">{{ __('messages.article_search_main_empty') ?? 'Tidak ada artikel' }}</div>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ __('messages.article_search_main_empty_desc') ?? 'Coba ganti kata kunci / filter.' }}
                <a href="{{ route('articles.index') }}" class="text-indigo-600 hover:underline">
                  {{ __('messages.article_search_main_empty_reset') ?? 'Reset' }}
                </a>
              </p>
            </div>
          @endif
        </main>

        @include('article.partials._sidebar_index', [
          'top'=>$top,
          'tagsPopular'=>$tagsPopular,
          'titleFor'=>$titleFor,
          'heroUrl'=>$heroUrl
        ])

      </div>
    </section>
  </div>
</x-page.index>
