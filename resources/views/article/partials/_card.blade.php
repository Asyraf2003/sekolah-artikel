@php
  $title = $a?->{"title_{$loc}"} ?: ($a->title_id ?? '');
  $desc  = $a?->{"excerpt_{$loc}"} ?: ($a->excerpt_id ?? '');
  $img   = $heroUrl($a->hero_image);
  $date  = optional($a->published_at)->translatedFormat('d M Y');
  $cats  = $a->categories?->take(2) ?? collect();
@endphp

<a href="{{ route('article', $a->slug) }}"
   class="group rounded-2xl border border-gray-200 dark:border-gray-800 overflow-hidden hover:shadow-md transition bg-white dark:bg-gray-900">
  <div class="aspect-[16/9] bg-gray-200 dark:bg-gray-800 overflow-hidden">
    @if($img)
      <img src="{{ $img }}" alt="{{ $title }}" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300">
    @endif
  </div>
  <div class="p-4 space-y-2">
    @if($cats->count())
      <div class="flex flex-wrap gap-2 text-xs text-gray-500 dark:text-gray-400">
        @foreach($cats as $c)
          <span class="px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
            {{ $c->{"name_{$loc}"} ?: $c->name_id }}
          </span>
        @endforeach
      </div>
    @endif

    <h3 class="font-semibold leading-snug line-clamp-2 group-hover:text-indigo-600">{{ $title }}</h3>

    @if($desc)
      <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-2">{{ $desc }}</p>
    @endif

    <div class="text-xs text-gray-500 dark:text-gray-400 flex flex-wrap gap-2">
      <span>{{ $a->author->name ?? 'Admin' }}</span>
      @if($date)<span>•</span><span>{{ $date }}</span>@endif
      <span>•</span><span>{{ number_format($a->view_count) }} views</span>
      <span>•</span><span>{{ $a->comment_count }} komentar</span>
    </div>
  </div>
</a>
