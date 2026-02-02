@php
  $children = $groupedComments->get($c->id, collect());
  $indent = min($level, 4);
@endphp

<div class="rounded-2xl border border-gray-200 dark:border-gray-800 p-4" style="margin-left: {{ $indent * 16 }}px;">
  <div class="flex items-start justify-between gap-3">
    <div>
      <div class="font-semibold text-sm">
        {{ $c->user->name ?? $c->guest_name ?? 'Tamu' }}
      </div>
      <div class="text-xs text-gray-500 dark:text-gray-400">
        {{ $c->created_at->translatedFormat('d M Y H:i') }}
      </div>
    </div>

    <a href="{{ route('article', $article->slug) }}?reply_to={{ $c->id }}#comments"
       class="text-xs text-indigo-600 hover:underline">
      Balas
    </a>
  </div>

  <div class="mt-2 text-sm text-gray-700 dark:text-gray-200 whitespace-pre-line">
    {{ $c->body }}
  </div>

  @if($children->count())
    <div class="mt-3 space-y-3">
      @foreach($children as $ch)
        @include('article.partials._comment_item', [
          'c' => $ch,
          'groupedComments' => $groupedComments,
          'article' => $article,
          'level' => $level + 1,
        ])
      @endforeach
    </div>
  @endif
</div>
