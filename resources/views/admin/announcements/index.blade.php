<x-page.admin>
    @php
        $Str = \Illuminate\Support\Str::class;

        // Normalize locale: "id_ID" -> "id"
        $locRaw = (string) app()->getLocale();
        $loc = strtolower(substr($locRaw, 0, 2));
        $rtl = ($loc === 'ar');

        $view = (string) request('view', 'list');
        $qsBase = request()->except('page');

        $gridUrl = route('admin.announcements.index', array_merge($qsBase, ['view' => 'grid']));
        $listUrl = route('admin.announcements.index', array_merge($qsBase, ['view' => 'list']));

        $q         = trim((string) request('q', ''));
        $published = (string) request('published', '');
        $sort      = (string) request('sort', 'latest');

        $titleFor = function ($m) use ($loc) {
            return method_exists($m, 'titleFor')
                ? $m->titleFor($loc)
                : ($m?->{"title_{$loc}"} ?: ($m->title_id ?? ''));
        };

        $descFor = function ($m) use ($loc) {
            return method_exists($m, 'descFor')
                ? ($m->descFor($loc) ?? '')
                : ($m?->{"desc_{$loc}"} ?: ($m->desc_id ?? ''));
        };

        $badgeFor = function ($a) {
            if (method_exists($a, 'trashed') && $a->trashed()) return 'bg-secondary';
            return $a->is_published ? 'bg-success' : 'bg-warning text-dark';
        };

        $statusTextFor = function ($a) {
            if (method_exists($a, 'trashed') && $a->trashed()) return 'Deleted';
            return $a->is_published ? 'Published' : 'Draft';
        };

        $safeHref = function (?string $href) use ($Str): ?string {
            $href = trim((string) $href);
            if ($href === '') return null;
            if ($href === '#') return $href;
            if ($Str::startsWith($href, ['/'])) return $href;
            if ($Str::startsWith($href, ['https://', 'http://'])) return $href;
            return null; // block javascript:, data:, etc.
        };
    @endphp

    <div dir="{{ $rtl ? 'rtl' : 'ltr' }}">
        <div class="page-heading">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                <div>
                    <h3 class="mb-1">Pengumuman</h3>
                    <p class="text-muted mb-0">Kelola pengumuman sekolah (multi-bahasa)</p>
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

                    <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i>
                        Tambah Pengumuman
                    </a>
                </div>
            </div>
        </div>

        <div class="page-content">
            <section class="section">

                {{-- Alerts --}}
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

                {{-- Quick summary --}}
                <div class="card mb-3">
                    <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div class="text-muted">
                            Menampilkan
                            <strong>{{ $announcements->count() }}</strong>
                            dari
                            <strong>{{ $announcements->total() }}</strong>
                            data.
                        </div>
                        <div class="small text-muted">
                            @if($announcements->total() > 0)
                                Range:
                                <strong>{{ $announcements->firstItem() }}</strong>–<strong>{{ $announcements->lastItem() }}</strong>
                                | Halaman:
                                <strong>{{ $announcements->currentPage() }}</strong>/<strong>{{ $announcements->lastPage() }}</strong>
                            @else
                                Tidak ada data
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Content --}}
                @if($announcements->count())

                    @if($view === 'list')
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead>
                                        <tr>
                                            <th>Info</th>
                                            <th style="width: 170px;">Tanggal</th>
                                            <th style="width: 180px;">Terbit</th>
                                            <th style="width: 130px;">Status</th>
                                            <th style="width: 230px;" class="text-end">Aksi</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($announcements as $a)
                                            @php
                                                $title = $titleFor($a);
                                                $desc  = $descFor($a);

                                                $badge = $badgeFor($a);
                                                $statusText = $statusTextFor($a);

                                                $hasEn = filled($a->title_en) || filled($a->desc_en);
                                                $hasAr = filled($a->title_ar) || filled($a->desc_ar);

                                                $href = $safeHref($a->link_url);
                                            @endphp

                                            <tr>
                                                <td class="min-w-0">
                                                    <div class="fw-semibold">
                                                        {{ $title }}
                                                        <span class="text-muted">#{{ $a->id }}</span>
                                                    </div>

                                                    <div class="text-muted small">
                                                        ID: {{ $Str::limit($a->title_id, 35) }}
                                                        @if($hasEn)
                                                            <span class="badge rounded-pill bg-secondary-subtle text-body ms-1">EN</span>
                                                        @endif
                                                        @if($hasAr)
                                                            <span class="badge rounded-pill bg-secondary-subtle text-body ms-1">AR</span>
                                                        @endif
                                                    </div>

                                                    <div class="text-muted small">
                                                        {{ $Str::limit(strip_tags($desc), 90) ?: '—' }}
                                                    </div>

                                                    @if($href)
                                                        <div class="small mt-1">
                                                            <a href="{{ $href }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                                                                <i class="bi bi-link-45deg"></i> {{ $a->link_url }}
                                                            </a>
                                                        </div>
                                                    @elseif(filled($a->link_url))
                                                        <div class="small mt-1 text-muted">
                                                            <i class="bi bi-link-45deg"></i> {{ $a->link_url }}
                                                        </div>
                                                    @endif
                                                </td>

                                                <td class="text-nowrap">
                                                    <div class="text-muted">
                                                        <i class="bi bi-calendar2-week me-1"></i>
                                                        {{ $a->event_date?->format('d M Y') ?? '—' }}
                                                    </div>
                                                    <div class="small text-muted">
                                                        Sort: <strong>{{ $a->sort_order }}</strong>
                                                    </div>
                                                </td>

                                                <td class="text-nowrap">
                                                    <div class="text-muted">
                                                        <i class="bi bi-broadcast me-1"></i>
                                                        {{ $a->published_at?->format('d M Y H:i') ?? '—' }}
                                                    </div>
                                                </td>

                                                <td>
                                                    <span class="badge {{ $badge }}">{{ $statusText }}</span>
                                                </td>

                                                <td class="text-end">
                                                    <div class="d-flex justify-content-end gap-1">
                                                        @if($a->is_published && $href)
                                                            <a href="{{ $href }}" target="_blank" rel="noopener noreferrer"
                                                               class="btn btn-sm btn-primary" title="Kunjungi">
                                                                <i class="bi bi-box-arrow-up-right"></i>
                                                            </a>
                                                        @endif

                                                        <a href="{{ route('admin.announcements.edit', $a->id) }}"
                                                           class="btn btn-sm btn-warning text-white" title="Edit">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </a>

                                                        <form action="{{ route('admin.announcements.destroy', $a->id) }}"
                                                              method="POST"
                                                              class="d-inline m-0 p-0"
                                                              onsubmit="return confirm('Hapus pengumuman ini?')">
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
                                    {{ $announcements->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="row g-3">
                            @foreach($announcements as $a)
                                @php
                                    $title = $titleFor($a);
                                    $desc  = $descFor($a);

                                    $badge = $badgeFor($a);
                                    $statusText = $statusTextFor($a);

                                    $hasEn = filled($a->title_en) || filled($a->desc_en);
                                    $hasAr = filled($a->title_ar) || filled($a->desc_ar);

                                    $href = $safeHref($a->link_url);
                                @endphp

                                <div class="col-12 col-sm-6 col-lg-4">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                                <div class="min-w-0">
                                                    <div class="fw-semibold mb-0 text-truncate" title="{{ $title }}">
                                                        {{ $title }}
                                                    </div>
                                                    <div class="text-muted small">
                                                        ID: {{ $a->id }}
                                                        • Event: {{ $a->event_date?->format('d M Y') ?? '—' }}
                                                    </div>
                                                </div>
                                                <span class="badge {{ $badge }}">{{ $statusText }}</span>
                                            </div>

                                            <div class="text-muted small mb-2">
                                                {{ $Str::limit(strip_tags($desc), 140) ?: '—' }}
                                            </div>

                                            @if($href)
                                                <div class="small mb-3">
                                                    <a href="{{ $href }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                                                        <i class="bi bi-link-45deg"></i> {{ $a->link_url }}
                                                    </a>
                                                </div>
                                            @elseif(filled($a->link_url))
                                                <div class="small mb-3 text-muted">
                                                    <i class="bi bi-link-45deg"></i> {{ $a->link_url }}
                                                </div>
                                            @endif

                                            <div class="d-flex flex-wrap gap-2">
                                                @if($a->is_published && $href)
                                                    <a href="{{ $href }}" target="_blank" rel="noopener noreferrer"
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-box-arrow-up-right"></i> Kunjungi
                                                    </a>
                                                @endif

                                                <a href="{{ route('admin.announcements.edit', $a->id) }}"
                                                   class="btn btn-sm btn-outline-warning">
                                                    <i class="bi bi-pencil-square"></i> Edit
                                                </a>

                                                <form action="{{ route('admin.announcements.destroy', $a->id) }}"
                                                      method="POST"
                                                      class="d-inline"
                                                      onsubmit="return confirm('Hapus pengumuman ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-trash"></i> Hapus
                                                    </button>
                                                </form>
                                            </div>

                                            <hr class="my-3">

                                            <div class="d-flex justify-content-between small text-muted">
                                                <div>
                                                    <i class="bi bi-broadcast me-1"></i>
                                                    {{ $a->published_at?->format('d M Y H:i') ?? '—' }}
                                                </div>
                                                <div>
                                                    Sort: <strong>{{ $a->sort_order }}</strong>
                                                </div>
                                            </div>

                                            <div class="small text-muted mt-2">
                                                @if($hasEn)
                                                    <span class="badge rounded-pill bg-secondary-subtle text-body me-1">EN</span>
                                                @endif
                                                @if($hasAr)
                                                    <span class="badge rounded-pill bg-secondary-subtle text-body me-1">AR</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-3">
                            {{ $announcements->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
                        </div>
                    @endif

                @else
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-megaphone display-6 text-muted d-block mb-2"></i>
                            <p class="text-muted mb-3">Belum ada pengumuman.</p>
                            <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-lg me-1"></i> Buat yang pertama
                            </a>
                        </div>
                    </div>
                @endif

            </section>
        </div>

        {{-- Offcanvas Filter --}}
        <div class="offcanvas offcanvas-end mazer-filter" tabindex="-1" id="filterOffcanvas" aria-labelledby="filterOffcanvasLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="filterOffcanvasLabel">
                    <i class="bi bi-funnel"></i> Filter Pengumuman
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>

            <div class="offcanvas-body">
                <form method="GET" action="{{ route('admin.announcements.index') }}">
                    <input type="hidden" name="view" value="{{ $view }}">

                    <div class="mb-3">
                        <label class="form-label">Kata kunci</label>
                        <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Cari judul / deskripsi…">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="published" class="form-select">
                            <option value=""  {{ $published === ''  ? 'selected' : '' }}>Semua</option>
                            <option value="1" {{ $published === '1' ? 'selected' : '' }}>Published</option>
                            <option value="0" {{ $published === '0' ? 'selected' : '' }}>Draft</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Urutkan</label>
                        <select name="sort" class="form-select">
                            <option value="latest"       {{ $sort === 'latest' ? 'selected' : '' }}>Terbaru dibuat</option>
                            <option value="oldest"       {{ $sort === 'oldest' ? 'selected' : '' }}>Terlama dibuat</option>
                            <option value="event_latest" {{ $sort === 'event_latest' ? 'selected' : '' }}>Event terbaru</option>
                            <option value="event_oldest" {{ $sort === 'event_oldest' ? 'selected' : '' }}>Event terlama</option>
                            <option value="title_asc"    {{ $sort === 'title_asc' ? 'selected' : '' }}>Judul A–Z</option>
                            <option value="title_desc"   {{ $sort === 'title_desc' ? 'selected' : '' }}>Judul Z–A</option>
                        </select>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Terapkan
                        </button>

                        <a href="{{ route('admin.announcements.index', ['view' => $view]) }}"
                           class="btn btn-outline-secondary w-100">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-page.admin>
