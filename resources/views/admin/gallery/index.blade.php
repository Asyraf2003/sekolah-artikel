<x-page.admin>
    @php
        // Dummy Data (13 items)
        $dummy = collect([
            ['id'=>1,'nama'=>'Galeri Pantai','desc'=>'Foto suasana pantai sore.','link'=>'https://example.com/galeri/1','img'=>'https://picsum.photos/seed/galeri-1/800/800','date'=>'2026-01-28','status'=>'published'],
            ['id'=>2,'nama'=>'Galeri Gunung','desc'=>'Pendakian dan sunrise.','link'=>'https://example.com/galeri/2','img'=>'https://picsum.photos/seed/galeri-2/800/800','date'=>'2026-01-27','status'=>'draft'],
            ['id'=>3,'nama'=>'Galeri Kota','desc'=>'Street photo malam hari.','link'=>'https://example.com/galeri/3','img'=>'https://picsum.photos/seed/galeri-3/800/800','date'=>'2026-01-26','status'=>'published'],
            ['id'=>4,'nama'=>'Galeri Kuliner','desc'=>'Makanan lokal favorit.','link'=>'https://example.com/galeri/4','img'=>'https://picsum.photos/seed/galeri-4/800/800','date'=>'2026-01-25','status'=>'archived'],
            ['id'=>5,'nama'=>'Galeri Event','desc'=>'Dokumentasi acara internal.','link'=>'https://example.com/galeri/5','img'=>'https://picsum.photos/seed/galeri-5/800/800','date'=>'2026-01-24','status'=>'published'],
            ['id'=>6,'nama'=>'Galeri Produk','desc'=>'Foto katalog produk.','link'=>'https://example.com/galeri/6','img'=>'https://picsum.photos/seed/galeri-6/800/800','date'=>'2026-01-23','status'=>'draft'],
            ['id'=>7,'nama'=>'Galeri Workshop','desc'=>'Behind the scenes workshop.','link'=>'https://example.com/galeri/7','img'=>'https://picsum.photos/seed/galeri-7/800/800','date'=>'2026-01-22','status'=>'published'],
            ['id'=>8,'nama'=>'Galeri Alam','desc'=>'Hutan, sungai, dan kabut.','link'=>'https://example.com/galeri/8','img'=>'https://picsum.photos/seed/galeri-8/800/800','date'=>'2026-01-21','status'=>'published'],
            ['id'=>9,'nama'=>'Galeri Kantor','desc'=>'Foto area kerja dan tim.','link'=>'https://example.com/galeri/9','img'=>'https://picsum.photos/seed/galeri-9/800/800','date'=>'2026-01-20','status'=>'draft'],
            ['id'=>10,'nama'=>'Galeri Riset','desc'=>'Dokumentasi eksperimen.','link'=>'https://example.com/galeri/10','img'=>'https://picsum.photos/seed/galeri-10/800/800','date'=>'2026-01-19','status'=>'archived'],
            ['id'=>11,'nama'=>'Galeri Travel','desc'=>'Trip singkat akhir pekan.','link'=>'https://example.com/galeri/11','img'=>'https://picsum.photos/seed/galeri-11/800/800','date'=>'2026-01-18','status'=>'published'],
            ['id'=>12,'nama'=>'Galeri Komunitas','desc'=>'Kegiatan sosial dan komunitas.','link'=>'https://example.com/galeri/12','img'=>'https://picsum.photos/seed/galeri-12/800/800','date'=>'2026-01-17','status'=>'draft'],
            ['id'=>13,'nama'=>'Galeri Arsip','desc'=>'Koleksi lama untuk referensi.','link'=>'https://example.com/galeri/13','img'=>'https://picsum.photos/seed/galeri-13/800/800','date'=>'2026-01-16','status'=>'archived'],
        ]);

        // Query Params
        $q = trim((string) request('q', ''));
        $status = (string) request('status', '');
        $from = (string) request('from', '');
        $to = (string) request('to', '');
        $sort = (string) request('sort', 'newest');
        $view = (string) request('view', 'grid');

        $qsBase = request()->except('page');

        // Filtering
        $filtered = $dummy;

        if ($q !== '') {
            $qLower = mb_strtolower($q);
            $filtered = $filtered->filter(function ($g) use ($qLower) {
                return (stripos(mb_strtolower($g['nama']), $qLower) !== false)
                    || (stripos(mb_strtolower($g['desc']), $qLower) !== false)
                    || (stripos(mb_strtolower($g['link']), $qLower) !== false);
            });
        }

        if ($status !== '') {
            $filtered = $filtered->where('status', $status);
        }

        if ($from !== '') {
            $filtered = $filtered->filter(function ($g) use ($from) {
                return $g['date'] >= $from;
            });
        }

        if ($to !== '') {
            $filtered = $filtered->filter(function ($g) use ($to) {
                return $g['date'] <= $to;
            });
        }

        // Sorting
        switch ($sort) {
            case 'oldest':    $filtered = $filtered->sortBy('date'); break;
            case 'name_asc':  $filtered = $filtered->sortBy('nama'); break;
            case 'name_desc': $filtered = $filtered->sortByDesc('nama'); break;
            case 'newest':
            default:          $filtered = $filtered->sortByDesc('date'); break;
        }
        $filtered = $filtered->values();

        // Pagination
        $perPage = 6;
        $page = (int) request('page', 1);
        if ($page < 1) $page = 1;

        $items = $filtered->forPage($page, $perPage)->values();

        $galleries = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $filtered->count(),
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );

        $gridUrl = request()->url() . '?' . http_build_query(array_merge($qsBase, ['view' => 'grid']));
        $listUrl = request()->url() . '?' . http_build_query(array_merge($qsBase, ['view' => 'list']));

        $createUrl = url('/admin/gallery/create');
        $editBase  = url('/admin/gallery');
        $deleteBase= url('/admin/gallery');
    @endphp

    <div class="page-heading">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
            <div>
                <h3 class="mb-1">Galeri</h3>
                <p class="text-muted mb-0">Data dummy galeri untuk tampilan Mazer (dark/light ikut tema).</p>
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

                <a href="{{ $createUrl }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i>
                    Tambah Galeri
                </a>
            </div>
        </div>
    </div>

    <div class="page-content">
        <section class="section">
            {{-- Quick summary --}}
            <div class="card mb-3">
                <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div class="text-muted">
                        Menampilkan <strong>{{ $galleries->count() }}</strong> dari <strong>{{ $filtered->count() }}</strong> data.
                    </div>
                    <div class="small text-muted">
                        Per halaman: <strong>{{ $perPage }}</strong> | Total halaman: <strong>{{ $galleries->lastPage() }}</strong>
                    </div>
                </div>
            </div>

            {{-- Content --}}
            @if($view === 'list')
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                <tr>
                                    <th style="width: 72px;">Foto</th>
                                    <th>Info</th>
                                    <th style="width: 170px;">Tanggal</th>
                                    <th style="width: 130px;">Status</th>
                                    <th style="width: 220px;" class="text-end">Aksi</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($galleries as $g)
                                    <tr>
                                        <td>
                                            <div class="ratio ratio-1x1 rounded overflow-hidden" style="width: 56px;">
                                                <img src="{{ $g['img'] }}" alt="{{ $g['nama'] }}" style="object-fit: cover;">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $g['nama'] }} <span class="text-muted">#{{ $g['id'] }}</span></div>
                                            <div class="text-muted small">{{ $g['desc'] }}</div>
                                            <div class="small">
                                                <a href="{{ $g['link'] }}" target="_blank" rel="noopener" class="text-decoration-none">
                                                    {{ $g['link'] }}
                                                </a>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-muted">{{ $g['date'] }}</div>
                                        </td>
                                        <td>
                                            @php
                                                $badge = match($g['status']) {
                                                    'published' => 'bg-success',
                                                    'draft' => 'bg-warning',
                                                    'archived' => 'bg-secondary',
                                                    default => 'bg-light',
                                                };
                                            @endphp
                                            <span class="badge {{ $badge }}">{{ ucfirst($g['status']) }}</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ $g['link'] }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-box-arrow-up-right"></i> Kunjungi
                                            </a>
                                            <a href="{{ $editBase }}/{{ $g['id'] }}/edit" class="btn btn-sm btn-outline-warning">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </a>
                                            <form action="{{ $deleteBase }}/{{ $g['id'] }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('Hapus galeri #{{ $g['id'] }}?')">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            Tidak ada data. Coba longgarkan filternya.
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $galleries->onEachSide(1)->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            @else
                <div class="row g-3">
                    @forelse($galleries as $g)
                        @php
                            $badge = match($g['status']) {
                                'published' => 'bg-success',
                                'draft' => 'bg-warning',
                                'archived' => 'bg-secondary',
                                default => 'bg-light',
                            };
                        @endphp

                        <div class="col-12 col-sm-6 col-lg-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                        <div>
                                            <div class="fw-semibold mb-0">{{ $g['nama'] }}</div>
                                            <div class="text-muted small">ID: {{ $g['id'] }} • {{ $g['date'] }}</div>
                                        </div>
                                        <span class="badge {{ $badge }}">{{ ucfirst($g['status']) }}</span>
                                    </div>

                                    <div class="ratio ratio-1x1 rounded overflow-hidden mb-3">
                                        <img src="{{ $g['img'] }}" alt="{{ $g['nama'] }}" style="object-fit: cover;">
                                    </div>

                                    <div class="text-muted small mb-2">
                                        {{ $g['desc'] }}
                                    </div>

                                    <div class="small mb-3">
                                        <a href="{{ $g['link'] }}" target="_blank" rel="noopener" class="text-decoration-none">
                                            {{ $g['link'] }}
                                        </a>
                                    </div>

                                    <div class="d-flex flex-wrap gap-2">
                                        <a href="{{ $g['link'] }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-box-arrow-up-right"></i> Kunjungi
                                        </a>
                                        <a href="{{ $editBase }}/{{ $g['id'] }}/edit" class="btn btn-sm btn-outline-warning">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </a>
                                        <form action="{{ $deleteBase }}/{{ $g['id'] }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Hapus galeri #{{ $g['id'] }}?')">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body text-center text-muted py-4">
                                    Tidak ada data. Coba longgarkan filternya.
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>

                <div class="mt-3">
                    {{ $galleries->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </section>
    </div>

    {{-- Offcanvas Filter (Right Side) --}}
    <div class="offcanvas offcanvas-end mazer-filter" tabindex="-1" id="filterOffcanvas" aria-labelledby="filterOffcanvasLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="filterOffcanvasLabel">
                <i class="bi bi-funnel"></i> Filter Pencarian
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <div class="offcanvas-body">
            <form method="GET" action="{{ request()->url() }}">
                {{-- Keep view mode when filtering --}}
                <input type="hidden" name="view" value="{{ $view }}">

                <div class="mb-3">
                    <label class="form-label">Kata kunci</label>
                    <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Cari nama/desc/link...">
                </div>

                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="" {{ $status==='' ? 'selected' : '' }}>Semua</option>
                        <option value="published" {{ $status==='published' ? 'selected' : '' }}>Published</option>
                        <option value="draft" {{ $status==='draft' ? 'selected' : '' }}>Draft</option>
                        <option value="archived" {{ $status==='archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label">Dari</label>
                        <input type="date" name="from" value="{{ $from }}" class="form-control">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Sampai</label>
                        <input type="date" name="to" value="{{ $to }}" class="form-control">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Urutkan</label>
                    <select name="sort" class="form-select">
                        <option value="newest" {{ $sort==='newest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="oldest" {{ $sort==='oldest' ? 'selected' : '' }}>Terlama</option>
                        <option value="name_asc" {{ $sort==='name_asc' ? 'selected' : '' }}>Nama A-Z</option>
                        <option value="name_desc" {{ $sort==='name_desc' ? 'selected' : '' }}>Nama Z-A</option>
                    </select>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Terapkan
                    </button>

                    <a href="{{ request()->url() . '?' . http_build_query(['view' => $view]) }}"
                       class="btn btn-outline-secondary w-100">
                        Reset
                    </a>
                </div>

                <hr class="my-3">

                <div class="text-muted small">
                    Catatan: Ini masih data dummy. Nanti tinggal ganti ke query DB + paginate Laravel.
                </div>
            </form>
        </div>
    </div>
@push('styles')
<style>
/* =========================
   Dark theme support for Offcanvas Filter (Mazer)
   ========================= */

/* Target beberapa kemungkinan attribute tema yang dipakai Mazer */
html[data-bs-theme="dark"] .mazer-filter,
html[data-theme="dark"] .mazer-filter,
body[data-bs-theme="dark"] .mazer-filter,
body[data-theme="dark"] .mazer-filter {
    background-color: var(--bs-body-bg);
    color: var(--bs-body-color);
    border-left: 1px solid var(--bs-border-color);
}

/* Header */
html[data-bs-theme="dark"] .mazer-filter .offcanvas-header,
html[data-theme="dark"] .mazer-filter .offcanvas-header,
body[data-bs-theme="dark"] .mazer-filter .offcanvas-header,
body[data-theme="dark"] .mazer-filter .offcanvas-header {
    border-bottom: 1px solid var(--bs-border-color);
}

/* Form controls */
html[data-bs-theme="dark"] .mazer-filter .form-control,
html[data-bs-theme="dark"] .mazer-filter .form-select,
html[data-theme="dark"] .mazer-filter .form-control,
html[data-theme="dark"] .mazer-filter .form-select,
body[data-bs-theme="dark"] .mazer-filter .form-control,
body[data-bs-theme="dark"] .mazer-filter .form-select,
body[data-theme="dark"] .mazer-filter .form-control,
body[data-theme="dark"] .mazer-filter .form-select {
    background-color: var(--bs-body-bg);
    color: var(--bs-body-color);
    border-color: var(--bs-border-color);
}

/* Placeholder */
html[data-bs-theme="dark"] .mazer-filter .form-control::placeholder,
html[data-theme="dark"] .mazer-filter .form-control::placeholder,
body[data-bs-theme="dark"] .mazer-filter .form-control::placeholder,
body[data-theme="dark"] .mazer-filter .form-control::placeholder {
    color: rgba(255,255,255,.6);
}

/* Close button biar kelihatan */
html[data-bs-theme="dark"] .mazer-filter .btn-close,
html[data-theme="dark"] .mazer-filter .btn-close,
body[data-bs-theme="dark"] .mazer-filter .btn-close,
body[data-theme="dark"] .mazer-filter .btn-close {
    filter: invert(1) grayscale(100%);
    opacity: .8;
}
</style>
@endpush

</x-page.admin>
