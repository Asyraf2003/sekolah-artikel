<x-page.admin>
    @php
        $view = (string) request('view', 'list');
        $qsBase = request()->except('page');

        $gridUrl = route('admin.programs.index', array_merge($qsBase, ['view' => 'grid']));
        $listUrl = route('admin.programs.index', array_merge($qsBase, ['view' => 'list']));

        $q         = trim((string) request('q', ''));
        $published = (string) request('published', ''); // '' | '1' | '0'
        $sort      = (string) request('sort', 'ordered'); // ordered|latest|oldest|title_asc|title_desc

        $badgeFor = function ($p) {
            if (method_exists($p, 'trashed') && $p->trashed()) return 'bg-secondary';
            return $p->is_published ? 'bg-success' : 'bg-warning text-dark';
        };

        $statusTextFor = function ($p) {
            if (method_exists($p, 'trashed') && $p->trashed()) return 'Deleted';
            return $p->is_published ? 'Published' : 'Draft';
        };
    @endphp

    <div class="page-heading">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
            <div>
                <h3 class="mb-1">Program Unggulan</h3>
                <p class="text-muted mb-0">Kelola konten program unggulan (multi-bahasa)</p>
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

                <a href="{{ route('admin.programs.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i>
                    Tambah Program
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
                        Menampilkan <strong>{{ $programs->count() }}</strong> dari <strong>{{ $programs->total() }}</strong> data.
                    </div>
                    <div class="small text-muted">
                        @if($programs->total() > 0)
                            Range: <strong>{{ $programs->firstItem() }}</strong>–<strong>{{ $programs->lastItem() }}</strong>
                            | Halaman: <strong>{{ $programs->currentPage() }}</strong>/<strong>{{ $programs->lastPage() }}</strong>
                        @else
                            Tidak ada data
                        @endif
                    </div>
                </div>
            </div>

            @if($programs->count())

                @if($view === 'list')
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                    <tr>
                                        <th>Info</th>
                                        <th style="width: 140px;">Sort</th>
                                        <th style="width: 140px;">Status</th>
                                        <th style="width: 180px;">Dibuat</th>
                                        <th style="width: 210px;" class="text-end">Aksi</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($programs as $p)
                                        @php
                                            $badge = $badgeFor($p);
                                            $statusText = $statusTextFor($p);

                                            $hasEn = filled($p->title_en) || filled($p->desc_en);
                                            $hasAr = filled($p->title_ar) || filled($p->desc_ar);

                                            $title = $p->title_id ?: '—';
                                            $desc  = $p->desc_id ?: '';
                                        @endphp

                                        <tr>
                                            <td class="min-w-0">
                                                <div class="fw-semibold">
                                                    {{ $title }}
                                                    <span class="text-muted">#{{ $p->id }}</span>
                                                </div>

                                                <div class="text-muted small">
                                                    @if($hasEn)
                                                        <span class="badge rounded-pill bg-secondary-subtle text-body me-1">EN</span>
                                                    @endif
                                                    @if($hasAr)
                                                        <span class="badge rounded-pill bg-secondary-subtle text-body me-1">AR</span>
                                                    @endif
                                                </div>

                                                <div class="text-muted small">
                                                    {{ \Illuminate\Support\Str::limit(strip_tags($desc), 120) ?: '—' }}
                                                </div>
                                            </td>

                                            <td class="text-nowrap">
                                                <div class="text-muted">
                                                    <i class="bi bi-sort-numeric-down me-1"></i>
                                                    <strong>{{ $p->sort_order }}</strong>
                                                </div>
                                            </td>

                                            <td>
                                                <span class="badge {{ $badge }}">{{ $statusText }}</span>
                                            </td>

                                            <td class="text-nowrap text-muted">
                                                {{ $p->created_at?->format('d M Y H:i') ?? '—' }}
                                            </td>

                                            <td class="text-end">
                                                <div class="d-flex justify-content-end gap-1 flex-wrap">
                                                    <a href="{{ route('admin.programs.edit', $p->id) }}"
                                                       class="btn btn-sm btn-warning text-white" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>

                                                    <form action="{{ route('admin.programs.destroy', $p->id) }}"
                                                          method="POST"
                                                          class="d-inline m-0 p-0"
                                                          onsubmit="return confirm('Hapus program ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>

                                                    @if(method_exists($p, 'trashed') && $p->trashed() && \Illuminate\Support\Facades\Route::has('admin.programs.restore'))
                                                        <form action="{{ route('admin.programs.restore', $p->id) }}"
                                                              method="POST"
                                                              class="d-inline m-0 p-0"
                                                              onsubmit="return confirm('Restore program ini?')">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Restore">
                                                                <i class="bi bi-arrow-counterclockwise"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                {{ $programs->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="row g-3">
                        @foreach($programs as $p)
                            @php
                                $badge = $badgeFor($p);
                                $statusText = $statusTextFor($p);

                                $title = $p->title_id ?: '—';
                                $desc  = $p->desc_id ?: '';
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
                                                    ID: {{ $p->id }} • Sort: <strong>{{ $p->sort_order }}</strong>
                                                </div>
                                            </div>
                                            <span class="badge {{ $badge }}">{{ $statusText }}</span>
                                        </div>

                                        <div class="text-muted small mb-3">
                                            {{ \Illuminate\Support\Str::limit(strip_tags($desc), 140) ?: '—' }}
                                        </div>

                                        <div class="d-flex flex-wrap gap-2">
                                            <a href="{{ route('admin.programs.edit', $p->id) }}"
                                               class="btn btn-sm btn-outline-warning">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </a>

                                            <form action="{{ route('admin.programs.destroy', $p->id) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Hapus program ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </button>
                                            </form>
                                        </div>

                                        <hr class="my-3">

                                        <div class="small text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            {{ $p->created_at?->format('d M Y H:i') ?? '—' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-3">
                        {{ $programs->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
                    </div>
                @endif

            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-grid-1x2 display-6 text-muted d-block mb-2"></i>
                        <p class="text-muted mb-3">Belum ada program.</p>
                        <a href="{{ route('admin.programs.create') }}" class="btn btn-primary">
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
                <i class="bi bi-funnel"></i> Filter Program
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <div class="offcanvas-body">
            <form method="GET" action="{{ route('admin.programs.index') }}">
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
                        <option value="ordered"    {{ $sort === 'ordered' ? 'selected' : '' }}>Sort order</option>
                        <option value="latest"     {{ $sort === 'latest' ? 'selected' : '' }}>Terbaru dibuat</option>
                        <option value="oldest"     {{ $sort === 'oldest' ? 'selected' : '' }}>Terlama dibuat</option>
                        <option value="title_asc"  {{ $sort === 'title_asc' ? 'selected' : '' }}>Judul A–Z</option>
                        <option value="title_desc" {{ $sort === 'title_desc' ? 'selected' : '' }}>Judul Z–A</option>
                    </select>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Terapkan
                    </button>

                    <a href="{{ route('admin.programs.index', ['view' => $view]) }}"
                       class="btn btn-outline-secondary w-100">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-page.admin>
