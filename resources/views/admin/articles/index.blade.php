<x-page.admin :title="'Artikel'">
    @php
        $view = (string) request('view', 'list');
        $qsBase = request()->except('page');

        $gridUrl = request()->url() . '?' . http_build_query(array_merge($qsBase, ['view' => 'grid']));
        $listUrl = request()->url() . '?' . http_build_query(array_merge($qsBase, ['view' => 'list']));

        $q        = trim((string) request('q', ''));
        $status   = (string) request('status', '');
        $featured = (string) request('featured', '');
        $hot      = (string) request('hot', ''); // legacy param; dipakai sebagai "Pinned" di UI
        $catSlug  = (string) request('category', '');
        $tagSlug  = (string) request('tag', '');
        $sort     = (string) request('sort', 'published_at_desc');

        $now = now();
    @endphp

    <div class="page-heading">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
            <div>
                <h3 class="mb-1">Artikel</h3>
                <p class="text-muted mb-0">Kelola konten artikel multi-bahasa (Quill)</p>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <div class="btn-group" role="group" aria-label="View toggle">
                    <a href="{{ $gridUrl }}"
                       class="btn btn-outline-primary {{ $view === 'grid' ? 'active' : '' }}"
                       title="Tampilan Grid">
                        <i class="bi bi-grid-3x3-gap"></i>
                    </a>
                    <a href="{{ $listUrl }}"
                       class="btn btn-outline-primary {{ $view === 'list' ? 'active' : '' }}"
                       title="Tampilan List">
                        <i class="bi bi-list-ul"></i>
                    </a>
                </div>

                <button class="btn btn-outline-secondary"
                        type="button"
                        data-bs-toggle="offcanvas"
                        data-bs-target="#filterOffcanvas"
                        aria-controls="filterOffcanvas">
                    <i class="bi bi-funnel"></i>
                    Filter
                </button>

                <a href="{{ route('admin.articles.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i>
                    Tambah Artikel
                </a>
            </div>
        </div>
    </div>

    <div class="page-content">
        <section class="section">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-1"></i>{{ $errors->first() }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card mb-3">
                <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div class="text-muted">
                        Menampilkan
                        <strong>{{ $articles->count() }}</strong>
                        dari
                        <strong>{{ $articles->total() }}</strong>
                        data.
                    </div>
                    <div class="small text-muted">
                        @if($articles->total() > 0)
                            Range:
                            <strong>{{ $articles->firstItem() }}</strong>–<strong>{{ $articles->lastItem() }}</strong>
                            | Halaman:
                            <strong>{{ $articles->currentPage() }}</strong>/<strong>{{ $articles->lastPage() }}</strong>
                        @else
                            Tidak ada data
                        @endif
                    </div>
                </div>
            </div>

            @if($articles->count())
                @if($view === 'list')
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width: 84px;">Cover</th>
                                            <th>Info</th>
                                            <th style="width: 150px;">Status</th>
                                            <th style="width: 190px;">Terbit</th>
                                            <th style="width: 190px;">Metrik</th>
                                            <th style="width: 240px;" class="text-end">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($articles as $a)
                                            @php
                                                $path = $a->hero_image;
                                                $imgUrl = null;

                                                if ($path) {
                                                    if (\Illuminate\Support\Str::startsWith($path, ['http://','https://'])) {
                                                        $imgUrl = $path;
                                                    } elseif (\Illuminate\Support\Str::startsWith($path, ['storage/', 'articles/', 'article/', 'gallery/', 'images/'])) {
                                                        $imgUrl = \Illuminate\Support\Facades\Storage::url($path);
                                                    } else {
                                                        $imgUrl = asset($path);
                                                    }
                                                }

                                                $isScheduled = $a->status === 'published' && $a->published_at && $a->published_at->gt($now);
                                                $isPublishedLive = $a->status === 'published' && $a->published_at && $a->published_at->lte($now);

                                                $statusLabel = $a->status === 'archived'
                                                    ? 'Archived'
                                                    : ($a->status === 'draft'
                                                        ? 'Draft'
                                                        : ($isScheduled ? 'Scheduled' : 'Published'));

                                                $statusClass = $a->status === 'archived'
                                                    ? 'bg-dark'
                                                    : ($a->status === 'draft'
                                                        ? 'bg-secondary'
                                                        : ($isScheduled ? 'bg-info text-dark' : 'bg-success'));

                                                $isPinnedActive = $a->pinned_until && $a->pinned_until->gte($now);

                                                $locale = app()->getLocale();
                                                $excerpt = match ($locale) {
                                                    'en' => $a->excerpt_en,
                                                    'ar' => $a->excerpt_ar,
                                                    default => $a->excerpt_id,
                                                } ?? ($a->excerpt_id ?? $a->excerpt_en ?? $a->excerpt_ar);

                                                $title = $a->title_id ?: ($a->title_en ?: ($a->title_ar ?: '—'));
                                            @endphp

                                            <tr>
                                                <td>
                                                    @if($imgUrl)
                                                        <div class="ratio ratio-1x1 rounded overflow-hidden" style="width: 56px;">
                                                            <img src="{{ $imgUrl }}" alt="{{ $title }}" style="object-fit: cover;">
                                                        </div>
                                                    @else
                                                        <div class="rounded d-flex align-items-center justify-content-center border"
                                                             style="width:56px;height:56px">
                                                            <i class="bi bi-image text-body-secondary"></i>
                                                        </div>
                                                    @endif
                                                </td>

                                                <td class="min-w-0">
                                                    <div class="fw-semibold text-truncate" title="{{ $title }}">
                                                        {{ $title }}
                                                        <span class="text-muted">#{{ $a->id }}</span>
                                                    </div>

                                                    <div class="small text-muted">
                                                        <code class="text-body">{{ $a->slug }}</code>
                                                        @if($a->author)
                                                            <span class="ms-2"><i class="bi bi-person-circle me-1"></i>{{ $a->author->name }}</span>
                                                        @endif
                                                    </div>

                                                    <div class="small text-muted mt-1">
                                                        @if($a->title_id)
                                                            <span class="badge rounded-pill bg-secondary-subtle text-body me-1">ID</span>
                                                        @endif
                                                        @if($a->title_en)
                                                            <span class="badge rounded-pill bg-secondary-subtle text-body me-1">EN</span>
                                                        @endif
                                                        @if($a->title_ar)
                                                            <span class="badge rounded-pill bg-secondary-subtle text-body me-1">AR</span>
                                                        @endif

                                                        @if($a->is_featured)
                                                            <span class="badge rounded-pill text-bg-warning ms-1">
                                                                <i class="bi bi-star-fill me-1"></i>Featured
                                                            </span>
                                                        @endif
                                                        @if($isPinnedActive)
                                                            <span class="badge rounded-pill text-bg-primary ms-1">
                                                                <i class="bi bi-pin-angle-fill me-1"></i>Pinned
                                                            </span>
                                                        @endif
                                                    </div>

                                                    <div class="text-muted small mt-1">
                                                        {{ $excerpt ? \Illuminate\Support\Str::limit($excerpt, 90) : '—' }}
                                                    </div>

                                                    <div class="small text-muted mt-1">
                                                        @if($a->relationLoaded('categories') && $a->categories->count())
                                                            <i class="bi bi-folder2 me-1"></i>
                                                            {{ $a->categories->pluck('name')->take(2)->join(', ') }}@if($a->categories->count() > 2), …@endif
                                                        @endif
                                                        @if($a->relationLoaded('tags') && $a->tags->count())
                                                            <span class="ms-2"><i class="bi bi-hash me-1"></i>{{ $a->tags->pluck('name')->take(2)->join(', ') }}@if($a->tags->count() > 2), …@endif</span>
                                                        @endif
                                                    </div>
                                                </td>

                                                <td>
                                                    <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
                                                </td>

                                                <td class="text-nowrap">
                                                    @if($a->published_at)
                                                        <div class="text-muted">
                                                            <i class="bi bi-calendar2-week me-1"></i>
                                                            {{ $a->published_at->format('d M Y H:i') }}
                                                        </div>
                                                    @else
                                                        <div class="text-muted">—</div>
                                                    @endif
                                                </td>

                                                <td class="text-nowrap">
                                                    <div class="small text-muted">
                                                        <div><i class="bi bi-eye me-1"></i>{{ number_format($a->view_count) }}</div>
                                                        <div><i class="bi bi-chat-dots me-1"></i>{{ number_format($a->comment_count) }}</div>
                                                        <div><i class="bi bi-share me-1"></i>{{ number_format($a->share_count) }}</div>
                                                        <div><i class="bi bi-clock me-1"></i>{{ (int)$a->reading_time }}m</div>
                                                    </div>
                                                </td>

                                                <td class="text-end">
                                                    <div class="d-flex justify-content-end gap-1 flex-wrap">
                                                        @if($isPublishedLive)
                                                            <a href="{{ route('article', $a->slug) }}" target="_blank" rel="noopener"
                                                               class="btn btn-sm btn-primary" title="Kunjungi">
                                                                <i class="bi bi-box-arrow-up-right"></i>
                                                            </a>
                                                        @endif

                                                        <a href="{{ route('admin.articles.edit', $a->id) }}"
                                                           class="btn btn-sm btn-warning text-white" title="Edit">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </a>

                                                        <form action="{{ route('admin.articles.destroy', $a->id) }}"
                                                              method="POST"
                                                              onsubmit="return confirm('Hapus artikel ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                {{ $articles->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="row g-3">
                        @foreach($articles as $a)
                            @php
                                $path = $a->hero_image;
                                $imgUrl = null;

                                if ($path) {
                                    if (\Illuminate\Support\Str::startsWith($path, ['http://','https://'])) {
                                        $imgUrl = $path;
                                    } elseif (\Illuminate\Support\Str::startsWith($path, ['storage/', 'articles/', 'article/', 'gallery/', 'images/'])) {
                                        $imgUrl = \Illuminate\Support\Facades\Storage::url($path);
                                    } else {
                                        $imgUrl = asset($path);
                                    }
                                }

                                $isScheduled = $a->status === 'published' && $a->published_at && $a->published_at->gt($now);
                                $isPublishedLive = $a->status === 'published' && $a->published_at && $a->published_at->lte($now);

                                $statusLabel = $a->status === 'archived'
                                    ? 'Archived'
                                    : ($a->status === 'draft'
                                        ? 'Draft'
                                        : ($isScheduled ? 'Scheduled' : 'Published'));

                                $statusClass = $a->status === 'archived'
                                    ? 'bg-dark'
                                    : ($a->status === 'draft'
                                        ? 'bg-secondary'
                                        : ($isScheduled ? 'bg-info text-dark' : 'bg-success'));

                                $isPinnedActive = $a->pinned_until && $a->pinned_until->gte($now);

                                $locale = app()->getLocale();
                                $excerpt = match ($locale) {
                                    'en' => $a->excerpt_en,
                                    'ar' => $a->excerpt_ar,
                                    default => $a->excerpt_id,
                                } ?? ($a->excerpt_id ?? $a->excerpt_en ?? $a->excerpt_ar);

                                $title = $a->title_id ?: ($a->title_en ?: ($a->title_ar ?: '—'));
                            @endphp

                            <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                                <div class="card h-100">
                                    @if($imgUrl)
                                        <div class="ratio ratio-16x9">
                                            <img src="{{ $imgUrl }}" alt="{{ $title }}" style="object-fit: cover;">
                                        </div>
                                    @endif

                                    <div class="card-body d-flex flex-column">
                                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                            <div class="min-w-0">
                                                <div class="fw-semibold text-truncate" title="{{ $title }}">
                                                    {{ $title }}
                                                </div>
                                                <div class="text-muted small">
                                                    <code class="text-body">{{ $a->slug }}</code>
                                                </div>
                                            </div>
                                            <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
                                        </div>

                                        <div class="mb-2 d-flex flex-wrap gap-1">
                                            @if($a->is_featured)
                                                <span class="badge rounded-pill text-bg-warning">
                                                    <i class="bi bi-star-fill me-1"></i>Featured
                                                </span>
                                            @endif
                                            @if($isPinnedActive)
                                                <span class="badge rounded-pill text-bg-primary">
                                                    <i class="bi bi-pin-angle-fill me-1"></i>Pinned
                                                </span>
                                            @endif
                                            @if($a->title_en)
                                                <span class="badge rounded-pill bg-secondary-subtle text-body">EN</span>
                                            @endif
                                            @if($a->title_ar)
                                                <span class="badge rounded-pill bg-secondary-subtle text-body">AR</span>
                                            @endif
                                        </div>

                                        <div class="text-muted small mb-2">
                                            {{ $excerpt ? \Illuminate\Support\Str::limit($excerpt, 90) : '—' }}
                                        </div>

                                        <div class="small text-body-secondary mb-2">
                                            <div class="d-flex flex-wrap gap-3">
                                                <span title="Views"><i class="bi bi-eye me-1"></i>{{ number_format($a->view_count) }}</span>
                                                <span title="Comments"><i class="bi bi-chat-dots me-1"></i>{{ number_format($a->comment_count) }}</span>
                                                <span title="Shares"><i class="bi bi-share me-1"></i>{{ number_format($a->share_count) }}</span>
                                                <span title="Reading time"><i class="bi bi-clock me-1"></i>{{ (int)$a->reading_time }}m</span>
                                            </div>
                                            <div class="mt-1">
                                                @if($a->author)
                                                    <span><i class="bi bi-person-circle me-1"></i>{{ $a->author->name }}</span>
                                                @endif
                                            </div>
                                            <div class="mt-1">
                                                @if($a->published_at)
                                                    <i class="bi bi-calendar2-week me-1"></i>{{ $a->published_at->format('d M Y') }}
                                                @else
                                                    <i class="bi bi-pencil me-1"></i> Draft
                                                @endif
                                            </div>
                                        </div>

                                        <div class="mt-auto d-flex flex-wrap gap-2">
                                            @if($isPublishedLive)
                                                <a href="{{ route('article', $a->slug) }}" target="_blank" rel="noopener"
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-box-arrow-up-right"></i> Kunjungi
                                                </a>
                                            @endif

                                            <a href="{{ route('admin.articles.edit', $a->id) }}"
                                               class="btn btn-sm btn-outline-warning">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </a>

                                            <form action="{{ route('admin.articles.destroy', $a->id) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Hapus artikel ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-3">
                        {{ $articles->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-file-earmark-text display-6 text-muted d-block mb-2"></i>
                        <p class="text-muted mb-3">Belum ada artikel.</p>
                        <a href="{{ route('admin.articles.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-lg me-1"></i> Buat yang pertama
                        </a>
                    </div>
                </div>
            @endif

        </section>
    </div>

    <div class="offcanvas offcanvas-end mazer-filter" tabindex="-1" id="filterOffcanvas" aria-labelledby="filterOffcanvasLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="filterOffcanvasLabel">
                <i class="bi bi-funnel"></i> Filter Artikel
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <div class="offcanvas-body">
            <form method="GET" action="{{ route('admin.articles.index') }}">
                <input type="hidden" name="view" value="{{ $view }}">

                <div class="mb-3">
                    <label class="form-label">Kata kunci</label>
                    <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Cari judul / slug…">
                </div>

                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="" @selected($status === '')>Semua</option>
                        <option value="draft" @selected($status === 'draft')>Draft</option>
                        <option value="published" @selected($status === 'published')>Published</option>
                        <option value="scheduled" @selected($status === 'scheduled')>Scheduled</option>
                        <option value="archived" @selected($status === 'archived')>Archived</option>
                    </select>
                </div>

                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label">Featured</label>
                        <select name="featured" class="form-select">
                            <option value="" @selected($featured === '')>Semua</option>
                            <option value="yes" @selected($featured === 'yes')>Ya saja</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Pinned</label>
                        <select name="hot" class="form-select">
                            <option value="" @selected($hot === '')>Semua</option>
                            <option value="yes" @selected($hot === 'yes')>Ya saja</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3 mt-3">
                    <label class="form-label">Kategori (slug)</label>
                    <input type="text" name="category" value="{{ $catSlug }}" class="form-control" placeholder="mis. berita-sekolah">
                </div>

                <div class="mb-3">
                    <label class="form-label">Tag (slug)</label>
                    <input type="text" name="tag" value="{{ $tagSlug }}" class="form-control" placeholder="mis. pengumuman">
                </div>

                <div class="mb-3">
                    <label class="form-label">Urutkan</label>
                    <select name="sort" class="form-select">
                        <option value="published_at_desc" @selected($sort === 'published_at_desc')>Terbaru</option>
                        <option value="published_at_asc"  @selected($sort === 'published_at_asc')>Terlama</option>
                        <option value="title_asc"         @selected($sort === 'title_asc')>Judul A–Z</option>
                        <option value="title_desc"        @selected($sort === 'title_desc')>Judul Z–A</option>
                        <option value="views_desc"        @selected($sort === 'views_desc')>Views terbanyak</option>
                    </select>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Terapkan
                    </button>

                    <a href="{{ route('admin.articles.index', array_filter(['view' => $view])) }}"
                       class="btn btn-outline-secondary w-100">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-page.admin>
