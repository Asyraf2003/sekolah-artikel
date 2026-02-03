<x-page.admin>
    @php
        $view = (string) request('view', 'list');
        $qsBase = request()->except('page');

        $gridUrl = route('admin.events.index', array_merge($qsBase, ['view' => 'grid']));
        $listUrl = route('admin.events.index', array_merge($qsBase, ['view' => 'list']));

        $q         = trim((string) request('q', ''));
        $published = (string) request('published', ''); // '' | '1' | '0'
        $time      = (string) request('time', '');      // '' | 'upcoming' | 'past'
        $sort      = (string) request('sort', 'event_upcoming');

        $badgeFor = function ($e) {
            if (method_exists($e, 'trashed') && $e->trashed()) return 'bg-secondary';
            return $e->is_published ? 'bg-success' : 'bg-warning text-dark';
        };

        $statusTextFor = function ($e) {
            if (method_exists($e, 'trashed') && $e->trashed()) return 'Deleted';
            return $e->is_published ? 'Published' : 'Draft';
        };

        $isUpcoming = function ($e) {
            return $e->event_date && $e->event_date->gte(now());
        };
    @endphp

    <div class="page-heading">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
            <div>
                <h3 class="mb-1">Agenda / Events</h3>
                <p class="text-muted mb-0">Kelola agenda sekolah (multi-bahasa)</p>
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
                    <i class="bi bi-funnel"></i> Filter
                </button>

                <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Tambah Event
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
                        Menampilkan <strong>{{ $events->count() }}</strong> dari <strong>{{ $events->total() }}</strong> data.
                    </div>
                    <div class="small text-muted">
                        @if($events->total() > 0)
                            Range: <strong>{{ $events->firstItem() }}</strong>–<strong>{{ $events->lastItem() }}</strong>
                            | Halaman: <strong>{{ $events->currentPage() }}</strong>/<strong>{{ $events->lastPage() }}</strong>
                        @else
                            Tidak ada data
                        @endif
                    </div>
                </div>
            </div>

            @if($events->count())

                @if($view === 'list')
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                    <tr>
                                        <th>Info</th>
                                        <th style="width: 180px;">Waktu</th>
                                        <th style="width: 140px;">Status</th>
                                        <th style="width: 110px;">Sort</th>
                                        <th style="width: 230px;" class="text-end">Aksi</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($events as $e)
                                        @php
                                            $badge = $badgeFor($e);
                                            $statusText = $statusTextFor($e);

                                            $hasEn = filled($e->title_en) || filled($e->place_en);
                                            $hasAr = filled($e->title_ar) || filled($e->place_ar);

                                            $title = $e->title_id ?: '—';
                                            $place = $e->place_id ?: '';
                                            $when  = $e->event_date ? $e->event_date->format('d M Y H:i') : '—';

                                            $upcoming = $isUpcoming($e);
                                        @endphp

                                        <tr>
                                            <td class="min-w-0">
                                                <div class="fw-semibold">
                                                    {{ $title }}
                                                    <span class="text-muted">#{{ $e->id }}</span>
                                                    @if($upcoming)
                                                        <span class="badge bg-info text-dark ms-1">Upcoming</span>
                                                    @endif
                                                </div>

                                                <div class="text-muted small">
                                                    @if($place !== '')
                                                        <i class="bi bi-geo-alt me-1"></i>
                                                        {{ \Illuminate\Support\Str::limit($place, 60) }}
                                                    @else
                                                        <span class="text-muted">Lokasi: —</span>
                                                    @endif
                                                </div>

                                                <div class="text-muted small mt-1">
                                                    @if($hasEn)
                                                        <span class="badge rounded-pill bg-secondary-subtle text-body me-1">EN</span>
                                                    @endif
                                                    @if($hasAr)
                                                        <span class="badge rounded-pill bg-secondary-subtle text-body me-1">AR</span>
                                                    @endif
                                                </div>

                                                @if($e->link_url)
                                                    <div class="small mt-1">
                                                        <a href="{{ $e->link_url }}" target="_blank" rel="noopener" class="text-decoration-none">
                                                            <i class="bi bi-link-45deg"></i> {{ $e->link_url }}
                                                        </a>
                                                    </div>
                                                @endif
                                            </td>

                                            <td class="text-nowrap text-muted">
                                                <i class="bi bi-calendar-event me-1"></i>
                                                {{ $when }}
                                            </td>

                                            <td>
                                                <span class="badge {{ $badge }}">{{ $statusText }}</span>
                                            </td>

                                            <td class="text-nowrap text-muted">
                                                <strong>{{ $e->sort_order }}</strong>
                                            </td>

                                            <td class="text-end">
                                                <div class="d-flex justify-content-end gap-1 flex-wrap">
                                                    @if($e->is_published && $e->link_url)
                                                        <a href="{{ $e->link_url }}" target="_blank" rel="noopener"
                                                           class="btn btn-sm btn-primary" title="Kunjungi">
                                                            <i class="bi bi-box-arrow-up-right"></i>
                                                        </a>
                                                    @endif

                                                    <a href="{{ route('admin.events.edit', $e->id) }}"
                                                       class="btn btn-sm btn-warning text-white" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>

                                                    <form action="{{ route('admin.events.destroy', $e->id) }}"
                                                          method="POST"
                                                          class="d-inline m-0 p-0"
                                                          onsubmit="return confirm('Hapus event ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>

                                                    @if(method_exists($e, 'trashed') && $e->trashed() && \Illuminate\Support\Facades\Route::has('admin.events.restore'))
                                                        <form action="{{ route('admin.events.restore', $e->id) }}"
                                                              method="POST"
                                                              class="d-inline m-0 p-0"
                                                              onsubmit="return confirm('Restore event ini?')">
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
                                {{ $events->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="row g-3">
                        @foreach($events as $e)
                            @php
                                $badge = $badgeFor($e);
                                $statusText = $statusTextFor($e);

                                $title = $e->title_id ?: '—';
                                $place = $e->place_id ?: '';
                                $when  = $e->event_date ? $e->event_date->format('d M Y H:i') : '—';
                                $upcoming = $isUpcoming($e);
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
                                                    ID: {{ $e->id }} • Sort: <strong>{{ $e->sort_order }}</strong>
                                                    @if($upcoming)
                                                        <span class="badge bg-info text-dark ms-1">Upcoming</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <span class="badge {{ $badge }}">{{ $statusText }}</span>
                                        </div>

                                        <div class="text-muted small">
                                            <i class="bi bi-calendar-event me-1"></i>{{ $when }}
                                        </div>

                                        <div class="text-muted small mt-1">
                                            <i class="bi bi-geo-alt me-1"></i>{{ $place !== '' ? $place : '—' }}
                                        </div>

                                        @if($e->link_url)
                                            <div class="small mt-2">
                                                <a href="{{ $e->link_url }}" target="_blank" rel="noopener" class="text-decoration-none">
                                                    <i class="bi bi-link-45deg"></i> {{ $e->link_url }}
                                                </a>
                                            </div>
                                        @endif

                                        <hr class="my-3">

                                        <div class="d-flex flex-wrap gap-2">
                                            <a href="{{ route('admin.events.edit', $e->id) }}"
                                               class="btn btn-sm btn-outline-warning">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </a>

                                            <form action="{{ route('admin.events.destroy', $e->id) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Hapus event ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </button>
                                            </form>

                                            @if($e->is_published && $e->link_url)
                                                <a href="{{ $e->link_url }}" target="_blank" rel="noopener"
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-box-arrow-up-right"></i> Kunjungi
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-3">
                        {{ $events->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
                    </div>
                @endif

            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-calendar2-event display-6 text-muted d-block mb-2"></i>
                        <p class="text-muted mb-3">Belum ada event.</p>
                        <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
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
                <i class="bi bi-funnel"></i> Filter Event
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <div class="offcanvas-body">
            <form method="GET" action="{{ route('admin.events.index') }}">
                <input type="hidden" name="view" value="{{ $view }}">

                <div class="mb-3">
                    <label class="form-label">Kata kunci</label>
                    <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Cari judul / lokasi…">
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
                    <label class="form-label">Waktu</label>
                    <select name="time" class="form-select">
                        <option value=""         {{ $time === '' ? 'selected' : '' }}>Semua</option>
                        <option value="upcoming" {{ $time === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                        <option value="past"     {{ $time === 'past' ? 'selected' : '' }}>Past</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Urutkan</label>
                    <select name="sort" class="form-select">
                        <option value="event_upcoming" {{ $sort === 'event_upcoming' ? 'selected' : '' }}>Event terdekat</option>
                        <option value="event_past"     {{ $sort === 'event_past' ? 'selected' : '' }}>Event terbaru</option>
                        <option value="latest"         {{ $sort === 'latest' ? 'selected' : '' }}>Terbaru dibuat</option>
                        <option value="oldest"         {{ $sort === 'oldest' ? 'selected' : '' }}>Terlama dibuat</option>
                        <option value="title_asc"      {{ $sort === 'title_asc' ? 'selected' : '' }}>Judul A–Z</option>
                        <option value="title_desc"     {{ $sort === 'title_desc' ? 'selected' : '' }}>Judul Z–A</option>
                    </select>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Terapkan
                    </button>

                    <a href="{{ route('admin.events.index', ['view' => $view]) }}"
                       class="btn btn-outline-secondary w-100">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-page.admin>
