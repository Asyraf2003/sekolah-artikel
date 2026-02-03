<x-page.admin>
    @php
        $view = (string) request('view', 'list');
        $qsBase = request()->except('page');

        $gridUrl = route('admin.extracurriculars.index', array_merge($qsBase, ['view' => 'grid']));
        $listUrl = route('admin.extracurriculars.index', array_merge($qsBase, ['view' => 'list']));

        $q         = trim((string) request('q', ''));
        $published = (string) request('published', '');
        $sort      = (string) request('sort', 'ordered');

        $badgeFor = function ($e) {
            if (method_exists($e, 'trashed') && $e->trashed()) return 'bg-secondary';
            return $e->is_published ? 'bg-success' : 'bg-warning text-dark';
        };

        $statusTextFor = function ($e) {
            if (method_exists($e, 'trashed') && $e->trashed()) return 'Deleted';
            return $e->is_published ? 'Published' : 'Draft';
        };
    @endphp

    <div class="page-heading">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
            <div>
                <h3 class="mb-1">Ekstrakurikuler</h3>
                <p class="text-muted mb-0">Kelola daftar ekstrakurikuler (multi-bahasa)</p>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <div class="btn-group" role="group" aria-label="View toggle">
                    <a href="{{ $gridUrl }}" class="btn btn-outline-primary {{ $view === 'grid' ? 'active' : '' }}" title="Tampilan Grid">
                        <i class="bi bi-grid-3x3-gap"></i>
                    </a>
                    <a href="{{ $listUrl }}" class="btn btn-outline-primary {{ $view === 'list' ? 'active' : '' }}" title="Tampilan List">
                        <i class="bi bi-list-ul"></i>
                    </a>
                </div>

                <button class="btn btn-outline-secondary"
                        type="button"
                        data-bs-toggle="offcanvas"
                        data-bs-target="#filterOffcanvas"
                        aria-controls="filterOffcanvas">
                    <i class="bi bi-funnel"></i> Filter
                </button>

                <a href="{{ route('admin.extracurriculars.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Tambah Ekstrakurikuler
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
                        Menampilkan <strong>{{ $extracurriculars->count() }}</strong> dari <strong>{{ $extracurriculars->total() }}</strong> data.
                    </div>
                    <div class="small text-muted">
                        @if($extracurriculars->total() > 0)
                            Range: <strong>{{ $extracurriculars->firstItem() }}</strong>–<strong>{{ $extracurriculars->lastItem() }}</strong>
                            | Halaman: <strong>{{ $extracurriculars->currentPage() }}</strong>/<strong>{{ $extracurriculars->lastPage() }}</strong>
                        @else
                            Tidak ada data
                        @endif
                    </div>
                </div>
            </div>

            @if($extracurriculars->count())

                @if($view === 'list')
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th style="width: 120px;">Sort</th>
                                        <th style="width: 140px;">Status</th>
                                        <th style="width: 180px;">Dibuat</th>
                                        <th style="width: 220px;" class="text-end">Aksi</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($extracurriculars as $e)
                                        @php
                                            $badge = $badgeFor($e);
                                            $statusText = $statusTextFor($e);

                                            $hasEn = filled($e->name_en);
                                            $hasAr = filled($e->name_ar);
                                        @endphp

                                        <tr>
                                            <td class="min-w-0">
                                                <div class="fw-semibold">
                                                    {{ $e->name_id }}
                                                    <span class="text-muted">#{{ $e->id }}</span>
                                                </div>

                                                <div class="text-muted small">
                                                    @if($hasEn)
                                                        <span class="badge rounded-pill bg-secondary-subtle text-body me-1">EN</span>
                                                        {{ \Illuminate\Support\Str::limit($e->name_en, 50) }}
                                                    @endif
                                                </div>

                                                <div class="text-muted small">
                                                    @if($hasAr)
                                                        <span class="badge rounded-pill bg-secondary-subtle text-body me-1">AR</span>
                                                        {{ \Illuminate\Support\Str::limit($e->name_ar, 50) }}
                                                    @endif
                                                </div>
                                            </td>

                                            <td class="text-nowrap text-muted">
                                                <i class="bi bi-sort-numeric-down me-1"></i>
                                                <strong>{{ $e->sort_order }}</strong>
                                            </td>

                                            <td>
                                                <span class="badge {{ $badge }}">{{ $statusText }}</span>
                                            </td>

                                            <td class="text-nowrap text-muted">
                                                {{ $e->created_at?->format('d M Y H:i') ?? '—' }}
                                            </td>

                                            <td class="text-end">
                                                <div class="d-flex justify-content-end gap-1 flex-wrap">
                                                    <a href="{{ route('admin.extracurriculars.edit', $e->id) }}"
                                                       class="btn btn-sm btn-warning text-white" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>

                                                    <form action="{{ route('admin.extracurriculars.destroy', $e->id) }}"
                                                          method="POST"
                                                          class="d-inline m-0 p-0"
                                                          onsubmit="return confirm('Hapus ekstrakurikuler ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>

                                                    @if(method_exists($e, 'trashed') && $e->trashed() && \Illuminate\Support\Facades\Route::has('admin.extracurriculars.restore'))
                                                        <form action="{{ route('admin.extracurriculars.restore', $e->id) }}"
                                                              method="POST"
                                                              class="d-inline m-0 p-0"
                                                              onsubmit="return confirm('Restore item ini?')">
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
                                {{ $extracurriculars->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="row g-3">
                        @foreach($extracurriculars as $e)
                            @php
                                $badge = $badgeFor($e);
                                $statusText = $statusTextFor($e);
                            @endphp

                            <div class="col-12 col-sm-6 col-lg-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                            <div class="min-w-0">
                                                <div class="fw-semibold mb-0 text-truncate" title="{{ $e->name_id }}">
                                                    {{ $e->name_id }}
                                                </div>
                                                <div class="text-muted small">
                                                    ID: {{ $e->id }} • Sort: <strong>{{ $e->sort_order }}</strong>
                                                </div>
                                            </div>
                                            <span class="badge {{ $badge }}">{{ $statusText }}</span>
                                        </div>

                                        <div class="text-muted small mb-2">
                                            @if(filled($e->name_en))
                                                <span class="badge rounded-pill bg-secondary-subtle text-body me-1">EN</span>
                                                {{ \Illuminate\Support\Str::limit($e->name_en, 60) }}
                                            @endif
                                        </div>

                                        <div class="text-muted small mb-3">
                                            @if(filled($e->name_ar))
                                                <span class="badge rounded-pill bg-secondary-subtle text-body me-1">AR</span>
                                                {{ \Illuminate\Support\Str::limit($e->name_ar, 60) }}
                                            @endif
                                        </div>

                                        <div class="d-flex flex-wrap gap-2">
                                            <a href="{{ route('admin.extracurriculars.edit', $e->id) }}"
                                               class="btn btn-sm btn-outline-warning">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </a>

                                            <form action="{{ route('admin.extracurriculars.destroy', $e->id) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Hapus item ini?')">
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
                                            {{ $e->created_at?->format('d M Y H:i') ?? '—' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-3">
                        {{ $extracurriculars->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
                    </div>
                @endif

            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-collection display-6 text-muted d-block mb-2"></i>
                        <p class="text-muted mb-3">Belum ada data ekstrakurikuler.</p>
                        <a href="{{ route('admin.extracurriculars.create') }}" class="btn btn-primary">
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
                <i class="bi bi-funnel"></i> Filter Ekstrakurikuler
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <div class="offcanvas-body">
            <form method="GET" action="{{ route('admin.extracurriculars.index') }}">
                <input type="hidden" name="view" value="{{ $view }}">

                <div class="mb-3">
                    <label class="form-label">Kata kunci</label>
                    <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Cari nama…">
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
                        <option value="ordered"   {{ $sort === 'ordered' ? 'selected' : '' }}>Sort order</option>
                        <option value="latest"    {{ $sort === 'latest' ? 'selected' : '' }}>Terbaru dibuat</option>
                        <option value="oldest"    {{ $sort === 'oldest' ? 'selected' : '' }}>Terlama dibuat</option>
                        <option value="name_asc"  {{ $sort === 'name_asc' ? 'selected' : '' }}>Nama A–Z</option>
                        <option value="name_desc" {{ $sort === 'name_desc' ? 'selected' : '' }}>Nama Z–A</option>
                    </select>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Terapkan
                    </button>

                    <a href="{{ route('admin.extracurriculars.index', ['view' => $view]) }}"
                       class="btn btn-outline-secondary w-100">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-page.admin>
