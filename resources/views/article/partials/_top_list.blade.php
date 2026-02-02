@if(($top ?? collect())->count())
  <div class="rounded-2xl border border-gray-200 dark:border-gray-800 p-4">
    <h4 class="font-semibold mb-3">{{ __('messages.article_top_article') ?? 'Top Artikel' }}</h4>
    <div class="space-y-3">
      @foreach($top as $t)
        @php
          $tTitle = $titleFor($t);
          $tImg   = $heroUrl($t->hero_image);
        @endphp
        <a href="{{ route('article', $t->slug) }}" class="flex gap-3 group">
          <div class="h-16 w-24 bg-gray-200 dark:bg-gray-800 rounded-lg overflow-hidden">
            @if($tImg)
              <img src="{{ $tImg }}" alt="{{ $tTitle }}" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300">
            @endif
          </div>
          <div class="flex-1">
            <h5 class="text-sm font-semibold group-hover:text-indigo-600 leading-snug line-clamp-2">{{ $tTitle }}</h5>
            <div class="text-xs text-gray-500 dark:text-gray-400">
              {{ number_format($t->view_count) }} views • {{ $t->comment_count }} komentar
            </div>
          </div>
        </a>
      @endforeach
    </div>
  </div>
@endif
