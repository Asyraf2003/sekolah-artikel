<div class="flex flex-wrap items-center gap-2">
  @php
    $tr = function (string $key, string $fallback) {
      $v = __($key);
      return $v === $key ? $fallback : $v;
    };

    $sorts = [
      'recent'   => $tr('messages.article_sort_recent', 'Terbaru'),
      'top'      => $tr('messages.article_sort_top', 'Terpopuler'),
      'featured' => $tr('messages.article_sort_featured', 'Featured'),
    ];
  @endphp

  @foreach($sorts as $key => $label)
    <a href="{{ route('articles.index', array_filter(['q'=>$q,'category'=>$catSlug,'tag'=>$tagSlug,'sort'=>$key])) }}"
      class="text-sm rounded-xl px-3 py-2 border border-gray-300 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-900 {{ $sort===$key ? 'bg-gray-100 dark:bg-gray-900' : '' }}">
      {{ $label }}
    </a>
  @endforeach
</div>
