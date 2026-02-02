@php
  $loc = $loc ?? app()->getLocale();
@endphp

@if(($tagsPopular ?? collect())->count())
  <div class="rounded-2xl border border-gray-200 dark:border-gray-800 p-4">
    <h4 class="font-semibold mb-3">Tag Populer</h4>
    <div class="flex flex-wrap gap-2">
      @foreach($tagsPopular as $t)
        <a href="{{ route('articles.index', ['tag'=>$t->slug]) }}"
           class="text-xs rounded-full bg-gray-100 dark:bg-gray-900 px-3 py-1 border border-gray-200 dark:border-gray-800 hover:bg-gray-200 dark:hover:bg-gray-800">
          #{{ $t->name }}
          @if(isset($t->published_articles_count))
            <span class="ms-1 text-[11px] text-gray-500">({{ $t->published_articles_count }})</span>
          @endif
        </a>
      @endforeach
    </div>
  </div>
@endif
