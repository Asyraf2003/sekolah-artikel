<section id="comments" class="space-y-4">
  @if (session('success'))
    <div class="rounded-xl bg-green-50 text-green-700 px-4 py-3 border border-green-200">
      {{ session('success') }}
    </div>
  @endif

  @if ($errors->any())
    <div class="rounded-xl bg-red-50 text-red-700 px-4 py-3 border border-red-200">
      <ul class="list-disc pl-5 space-y-1">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <h2 class="text-lg font-semibold">
    {{ __('messages.article_comment') ?? 'Komentar' }} ({{ $article->comment_count }})
  </h2>

  <form action="{{ route('comments.store', $article->id) }}" method="POST" class="space-y-3">
    @csrf

    @if($replyTo)
      <input type="hidden" name="parent_id" value="{{ $replyTo->id }}">
    @endif

    <input type="text" name="website" class="hidden" tabindex="-1" autocomplete="off">

    @guest
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <input name="guest_name" value="{{ old('guest_name') }}"
               class="rounded-xl border border-gray-300 dark:border-gray-800 bg-gray-50 dark:bg-gray-900 px-3 py-2"
               placeholder="{{ __('messages.article_comment_name') ?? 'Nama' }}" required>
        <input name="guest_email" value="{{ old('guest_email') }}"
               class="rounded-xl border border-gray-300 dark:border-gray-800 bg-gray-50 dark:bg-gray-900 px-3 py-2"
               placeholder="{{ __('messages.article_comment_email') ?? 'Email' }}" type="email" required>
      </div>
    @endguest

    @if($replyTo)
      <div class="rounded-lg border border-indigo-200 dark:border-indigo-900 bg-indigo-50/50 dark:bg-indigo-900/30 px-3 py-2 text-sm">
        <div class="font-medium">
          Membalas: {{ $replyTo->user->name ?? $replyTo->guest_name ?? 'Pengguna' }}
        </div>
        <div class="text-gray-600 dark:text-gray-300 line-clamp-2">
          {{ \Illuminate\Support\Str::limit($replyTo->body, 140) }}
        </div>
      </div>
    @endif

    <textarea name="body"
      class="w-full rounded-xl border border-gray-300 dark:border-gray-800 bg-gray-50 dark:bg-gray-900 px-3 py-2"
      rows="3"
      placeholder="{{ __('messages.article_write_comment') ?? 'Tulis komentar...' }}"
      required>{{ old('body') }}</textarea>

    <div class="flex items-center gap-3">
      <button class="rounded-xl bg-indigo-600 text-white px-4 py-2">
        {{ __('messages.article_send_comment') ?? 'Kirim' }}
      </button>

      @if($replyTo)
        <a href="{{ route('article', $article->slug) }}#comments"
           class="text-sm text-gray-600 dark:text-gray-300 hover:underline">
          Batal balas
        </a>
      @endif
    </div>
  </form>

  <div class="space-y-3">
    @forelse($topLevelComments as $c)
      @include('article.partials._comment_item', [
        'c' => $c,
        'groupedComments' => $groupedComments,
        'article' => $article,
        'level' => 0,
      ])
    @empty
      <p class="text-sm text-gray-500">{{ __('messages.article_comment_empty') ?? 'Belum ada komentar.' }}</p>
    @endforelse
  </div>
</section>
