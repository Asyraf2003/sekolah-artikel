<x-page.admin>
    @php
        // View mode (list/grid) dari query param
        $view = (string) request('view', 'list');

        // Base query string (untuk toggle view tanpa reset filter)
        $qsBase = request()->except('page');

        $gridUrl = request()->url() . '?' . http_build_query(array_merge($qsBase, ['view' => 'grid']));
        $listUrl = request()->url() . '?' . http_build_query(array_merge($qsBase, ['view' => 'list']));

        // Filter values (biar value tetap kebaca di UI)
        $q        = trim((string) request('q', ''));
        $status   = (string) request('status', '');   // '' | submitted | approved | rejected | activated
        $perPage  = (int) request('per_page', 15);
        $sort     = (string) request('sort', 'latest');

        $statusMeta = [
            'submitted' => ['badge' => 'bg-warning text-dark', 'text' => 'Submitted'],
            'approved'  => ['badge' => 'bg-info text-dark',    'text' => 'Approved'],
            'rejected'  => ['badge' => 'bg-danger',            'text' => 'Rejected'],
            'activated' => ['badge' => 'bg-success',           'text' => 'Activated'],
        ];
    @endphp

    <div class="page-heading">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
            <div>
                <h3 class="mb-1">PPDB</h3>
                <p class="text-muted mb-0">Kelola pendaftaran, verifikasi, dan aktivasi akun (token sekali pakai)</p>
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
                        Menampilkan <strong>{{ $ppdbs->count() }}</strong> dari <strong>{{ $ppdbs->total() }}</strong> data.
                    </div>
                    <div class="small text-muted">
                        @if($ppdbs->total() > 0)
                            Range:
                            <strong>{{ $ppdbs->firstItem() }}</strong>–<strong>{{ $ppdbs->lastItem() }}</strong>
                            | Halaman:
                            <strong>{{ $ppdbs->currentPage() }}</strong>/<strong>{{ $ppdbs->lastPage() }}</strong>
                        @else
                            Tidak ada data
                        @endif
                    </div>
                </div>
            </div>

            {{-- Content --}}
            @if($ppdbs->count())

                @if($view === 'list')
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Pendaftar</th>
                                            <th style="width: 240px;">Kontak</th>
                                            <th style="width: 140px;">Kode</th>
                                            <th style="width: 140px;">Status</th>
                                            <th style="width: 190px;">Dibuat</th>
                                            <th style="width: 140px;" class="text-end">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($ppdbs as $p)
                                            @php
                                                $statusKey = $p->status instanceof \App\Enums\PpdbStatus ? $p->status->value : (string) $p->status;
                                                $meta = $statusMeta[$statusKey] ?? ['badge' => 'bg-secondary', 'text' => $statusKey ?: '—'];
                                            @endphp

                                            <tr>
                                                <td>
                                                    <div class="fw-semibold">
                                                        {{ $p->full_name }}
                                                        <span class="text-muted">#{{ $p->id }}</span>
                                                    </div>

                                                    <div class="text-muted small">
                                                        @if($p->user_id)
                                                            <span class="badge rounded-pill bg-secondary-subtle text-body">
                                                                User #{{ $p->user_id }}
                                                            </span>
                                                        @else
                                                            <span class="badge rounded-pill bg-secondary-subtle text-body">
                                                                Belum jadi akun
                                                            </span>
                                                        @endif
                                                    </div>
                                                </td>

                                                <td class="small">
                                                    <div><i class="bi bi-envelope me-1"></i>{{ $p->email }}</div>
                                                    <div class="text-muted"><i class="bi bi-whatsapp me-1"></i>{{ $p->whatsapp }}</div>
                                                </td>

                                                <td class="text-nowrap">
                                                    <span class="badge bg-light text-dark">{{ $p->public_code }}</span>
                                                </td>

                                                <td>
                                                    <span class="badge {{ $meta['badge'] }}">{{ $meta['text'] }}</span>
                                                </td>

                                                <td class="text-nowrap">
                                                    <div class="text-muted">
                                                        <i class="bi bi-calendar2-week me-1"></i>
                                                        {{ $p->created_at?->format('d M Y H:i') ?? '—' }}
                                                    </div>
                                                </td>

                                                <td class="text-end">
                                                    <a href="{{ route('admin.ppdb.show', $p->id) }}"
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye me-1"></i> Lihat
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                {{ $ppdbs->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>

                @else
                    <div class="row g-3">
                        @foreach($ppdbs as $p)
                            @php
                                $statusKey = $p->status instanceof \App\Enums\PpdbStatus ? $p->status->value : (string) $p->status;
                                $meta = $statusMeta[$statusKey] ?? ['badge' => 'bg-secondary', 'text' => $statusKey ?: '—'];
                            @endphp

                            <div class="col-12 col-sm-6 col-lg-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                            <div class="min-w-0">
                                                <div class="fw-semibold mb-0 text-truncate" title="{{ $p->full_name }}">
                                                    {{ $p->full_name }}
                                                </div>
                                                <div class="text-muted small">
                                                    #{{ $p->id }} • {{ $p->created_at?->format('d M Y H:i') ?? '—' }}
                                                </div>
                                            </div>
                                            <span class="badge {{ $meta['badge'] }}">{{ $meta['text'] }}</span>
                                        </div>

                                        <div class="text-muted small mb-2">
                                            <div class="text-truncate"><i class="bi bi-envelope me-1"></i>{{ $p->email }}</div>
                                            <div class="text-truncate"><i class="bi bi-whatsapp me-1"></i>{{ $p->whatsapp }}</div>
                                        </div>

                                        <div class="small mb-3">
                                            <span class="badge bg-light text-dark">
                                                <i class="bi bi-hash me-1"></i>{{ $p->public_code }}
                                            </span>
                                            @if($p->user_id)
                                                <span class="badge rounded-pill bg-secondary-subtle text-body ms-1">
                                                    User #{{ $p->user_id }}
                                                </span>
                                            @endif
                                        </div>

                                        <div class="d-flex flex-wrap gap-2">
                                            <a href="{{ route('admin.ppdb.show', $p->id) }}"
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i> Lihat
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-3">
                        {{ $ppdbs->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
                    </div>
                @endif

            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-file-earmark-text display-6 text-muted d-block mb-2"></i>
                        <p class="text-muted mb-0">Belum ada pendaftar PPDB.</p>
                    </div>
                </div>
            @endif

        </section>
    </div>

    {{-- Offcanvas Filter (Right Side) --}}
    <div class="offcanvas offcanvas-end mazer-filter" tabindex="-1" id="filterOffcanvas" aria-labelledby="filterOffcanvasLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="filterOffcanvasLabel">
                <i class="bi bi-funnel"></i> Filter PPDB
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <div class="offcanvas-body">
            <form method="GET" action="{{ route('admin.ppdb.index') }}">
                <input type="hidden" name="view" value="{{ $view }}">

                <div class="mb-3">
                    <label class="form-label">Kata kunci</label>
                    <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Cari nama / email / WA / kode…">
                </div>

                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="" {{ $status === '' ? 'selected' : '' }}>Semua</option>
                        @foreach (['submitted','approved','rejected','activated'] as $st)
                            <option value="{{ $st }}" {{ $status === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Per halaman</label>
                    <select name="per_page" class="form-select">
                        @foreach ([10,15,25,50] as $n)
                            <option value="{{ $n }}" {{ $perPage === $n ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Urutkan</label>
                    <select name="sort" class="form-select">
                        <option value="latest"      {{ $sort === 'latest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="oldest"      {{ $sort === 'oldest' ? 'selected' : '' }}>Terlama</option>
                        <option value="name_asc"    {{ $sort === 'name_asc' ? 'selected' : '' }}>Nama A–Z</option>
                        <option value="name_desc"   {{ $sort === 'name_desc' ? 'selected' : '' }}>Nama Z–A</option>
                        <option value="status_asc"  {{ $sort === 'status_asc' ? 'selected' : '' }}>Status A–Z</option>
                        <option value="status_desc" {{ $sort === 'status_desc' ? 'selected' : '' }}>Status Z–A</option>
                    </select>
                    <div class="small text-muted mt-1">
                        (Kalau controller belum support sort, aman diabaikan.)
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Terapkan
                    </button>

                    <a href="{{ route('admin.ppdb.index', array_filter(['view' => $view])) }}"
                       class="btn btn-outline-secondary w-100">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-page.admin>
