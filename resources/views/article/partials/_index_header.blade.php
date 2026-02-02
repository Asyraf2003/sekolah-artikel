<section class="border-b border-gray-200/70 dark:border-gray-800/60">
  <div class="mx-auto max-w-8xl px-4 sm:px-6 lg:px-8 py-6 space-y-4">
    @include('article.partials._searchbar', ['q'=>$q])
    @include('article.partials._category_chips', [
      'categoriesChip'=>$categoriesChip ?? collect(),
      'catSlug'=>$catSlug, 'q'=>$q, 'tagSlug'=>$tagSlug, 'sort'=>$sort,
      'loc'=>$loc
    ])
    @include('article.partials._sort_chips', [
      'q'=>$q, 'catSlug'=>$catSlug, 'tagSlug'=>$tagSlug, 'sort'=>$sort
    ])
  </div>
</section>
