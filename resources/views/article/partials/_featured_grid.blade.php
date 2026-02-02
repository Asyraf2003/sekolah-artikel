@if(($featured ?? collect())->count())
  <div class="rounded-2xl border border-gray-200 dark:border-gray-800 p-4">
    <h4 class="font-semibold mb-3">{{ __('messages.article_trend') ?? 'Featured' }}</h4>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      @foreach($featured as $f)
        @php
          $fTitle = $titleFor($f);
          $fImg   = $heroUrl($f->hero_image);
        @endphp
        <a href="{{ route('article', $f->slug) }}" class="group rounded-2xl border border-gray-200 dark:border-gray-800 overflow-hidden hover:shadow-md transition">
          <div class="aspect-[16/9] bg-gray-200 dark:bg-gray-800 overflow-hidden">
            @if($fImg)
              <img src="{{ $fImg }}" alt="{{ $fTitle }}" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300">
            @endif
          </div>
          <div class="p-4">
            <h3 class="font-semibold group-hover:text-indigo-600 line-clamp-2">{{ $fTitle }}</h3>
          </div>
        </a>
      @endforeach
    </div>
  </div>
@endif
