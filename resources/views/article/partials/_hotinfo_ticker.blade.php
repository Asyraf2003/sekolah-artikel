@if(($hotInfos ?? collect())->count())
  @php
    $titleKey = "title_{$loc}";
    $items = $hotInfos->map(fn($h) => [
      'title' => $h->{$titleKey} ?: $h->title_id,
      'url'   => $h->url ?: route('articles.index'),
    ])->values();
  @endphp

  <div
    x-data="{ i:0, items: {{ $items->toJson() }} }"
    x-init="if(items.length>1){ setInterval(()=>{ i=(i+1)%items.length }, 3500) }"
    class="rounded-2xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 px-4 py-3 flex items-center gap-3"
  >
    <span class="text-amber-700 dark:text-amber-300 text-sm font-semibold">
      {{ __('messages.article_page_hot_info') ?? 'Hot Info' }}
    </span>

    <div class="h-6 relative overflow-hidden flex-1">
      <template x-for="(it,idx) in items" :key="idx">
        <a :href="it.url"
          class="absolute inset-0 flex items-center text-sm text-amber-900 dark:text-amber-200 transition-all duration-500"
          :class="i===idx ? 'opacity-100 translate-y-0' : 'opacity-0 -translate-y-3'">
          <span x-text="it.title"></span>
        </a>
      </template>
    </div>
  </div>
@endif
