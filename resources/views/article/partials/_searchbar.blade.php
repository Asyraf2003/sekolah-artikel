<div class="flex items-center gap-3">
  <form action="{{ route('articles.index') }}" method="GET" class="flex-1 relative">
    <input
      type="search"
      name="q"
      value="{{ $q }}"
      placeholder="{{ __('messages.article_page_placeholder_search') }}"
      class="w-full rounded-2xl border border-gray-300 dark:border-gray-800 bg-gray-50 dark:bg-gray-900 px-4 py-3 pr-12 focus:outline-none focus:ring-2 focus:ring-indigo-500"
    />
    <button type="submit"
      class="absolute right-2 top-1/2 -translate-y-1/2 px-3 py-1 rounded-xl bg-indigo-600 text-white text-sm">
      {{ __('messages.article_page_search_button') }}
    </button>
  </form>

  <a href="{{ route('articles.index') }}"
     class="hidden sm:inline-flex rounded-xl px-3 py-2 text-sm border border-gray-300 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-900">
    {{ __('messages.article_page_show_all') }}
  </a>
</div>
