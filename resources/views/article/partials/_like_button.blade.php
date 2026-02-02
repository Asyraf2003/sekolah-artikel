@php
  $likeToggleUrl = route('articles.like.toggle', $article->id);
@endphp

<div class="flex items-center gap-2">
  <button
    type="button"
    id="btn-like"
    class="inline-flex items-center gap-2 rounded-xl border border-gray-300 dark:border-gray-800 px-3 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-900"
    data-url="{{ $likeToggleUrl }}"
  >
    <span id="like-icon">♡</span>
    <span>{{ __('messages.article_like') ?? 'Like' }}</span>
  </button>

  <span id="like-msg" class="text-xs text-gray-500 dark:text-gray-400"></span>
</div>

<script>
(function () {
  const btn = document.getElementById('btn-like');
  if (!btn) return;

  const msg = document.getElementById('like-msg');
  const icon = document.getElementById('like-icon');

  function getFp() {
    // super simple fingerprint (buat start dulu). nanti kalau mau pakai lib fingerprintjs bisa.
    let fp = localStorage.getItem('fp');
    if (!fp) {
      fp = (crypto.randomUUID ? crypto.randomUUID() : String(Date.now()) + Math.random().toString(16).slice(2));
      localStorage.setItem('fp', fp);
    }
    return fp;
  }

  async function toggleLike() {
    msg.textContent = '';
    const url = btn.dataset.url;

    const body = new URLSearchParams();
    body.set('fp', getFp());

    const res = await fetch(url, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Accept': 'application/json',
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: body.toString()
    });

    const data = await res.json().catch(()=>null);
    if (!res.ok || !data || !data.ok) {
      msg.textContent = (data && data.msg) ? data.msg : 'Gagal like.';
      return;
    }

    icon.textContent = data.liked ? '♥' : '♡';
    msg.textContent = data.liked ? 'Liked' : 'Unliked';
  }

  btn.addEventListener('click', toggleLike);
})();
</script>
