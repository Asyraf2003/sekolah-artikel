@if(($categoriesChip ?? collect())->count())
  <div class="flex flex-wrap items-center gap-2">
    @foreach($categoriesChip as $c)
      <a href="{{ route('articles.index', array_filter(['category'=>$c->slug,'q'=>$q,'tag'=>$tagSlug,'sort'=>$sort])) }}"
         class="rounded-full border border-gray-300 dark:border-gray-800 px-3 py-1.5 text-sm hover:bg-gray-100 dark:hover:bg-gray-900 {{ $catSlug===$c->slug ? 'bg-gray-100 dark:bg-gray-900' : '' }}">
        {{ $c->{"name_{$loc}"} ?: $c->name_id }}
      </a>
    @endforeach
  </div>
@endif
