<x-page.admin>
    @php
        // View mode (list/grid) dari query param
        $view = (string) request('view', 'list');

        // Base query string (untuk toggle view tanpa reset filter)
        $qsBase = request()->except('page');

        $gridUrl = request()->url() . '?' . http_build_query(array_merge($qsBase, ['view' => 'grid']));
        $listUrl = request()->url() . '?' . http_build_query(array_merge($qsBase, ['view' => 'list']));

        // Untuk form filter (biar value tetap kebaca di UI)
        $q         = trim((string) request('q', ''));
        $published = (string) request('published', ''); // '' | '1' | '0'
        $sort      = (string) request('sort', 'latest'); // latest|oldest|title_asc|title_desc
    @endphp
    <div class="page-heading">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
            <div>
                <h3 class="mb-1">Galeri</h3>
                <p class="text-muted mb-0">Kelola koleksi gambar dan deskripsi multi-bahasa</p>
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

                <a href="{{ route('admin.gallery.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i>
                    Tambah Gambar
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
                        <strong>{{ $images->count() }}</strong>
                        dari
                        <strong>{{ $images->total() }}</strong>
                        data.
                    </div>
                    <div class="small text-muted">
                        @if($images->total() > 0)
                            Range:
                            <strong>{{ $images->firstItem() }}</strong>–<strong>{{ $images->lastItem() }}</strong>
                            | Halaman:
                            <strong>{{ $images->currentPage() }}</strong>/<strong>{{ $images->lastPage() }}</strong>
                        @else
                            Tidak ada data
                        @endif
                    </div>
                </div>
            </div>

            {{-- Content --}}
            @if($images->count())

                @if($view === 'list')
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                    <tr>
                                        <th style="width: 72px;">Foto</th>
                                        <th>Info</th>
                                        <th style="width: 180px;">Terbit</th>
                                        <th style="width: 130px;">Status</th>
                                        <th style="width: 230px;" class="text-end">Aksi</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($images as $img)
                                        @php
                                            $path = $img->image_path;

                                            if (\Illuminate\Support\Str::startsWith($path, ['http://','https://'])) {
                                                $imgUrl = $path;
                                            } elseif (\Illuminate\Support\Str::startsWith($path, ['storage/', 'gallery/'])) {
                                                $imgUrl = \Illuminate\Support\Facades\Storage::url($path);
                                            } else {
                                                $imgUrl = asset($path);
                                            }

                                            $badge = $img->is_published ? 'bg-success' : 'bg-warning text-dark';
                                            $statusText = $img->is_published ? 'Published' : 'Draft';
                                        @endphp

                                        <tr>
                                            <td>
                                                @if($img->image_path)
                                                    <div class="ratio ratio-1x1 rounded overflow-hidden" style="width: 56px;">
                                                        <img src="{{ $imgUrl }}" alt="{{ $img->title_id }}" style="object-fit: cover;">
                                                    </div>
                                                @else
                                                    <div class="rounded d-flex align-items-center justify-content-center border"
                                                         style="width:56px;height:56px">
                                                        <i class="bi bi-image text-body-secondary"></i>
                                                    </div>
                                                @endif
                                            </td>

                                            <td>
                                                <div class="fw-semibold">
                                                    {{ $img->title_id }}
                                                    <span class="text-muted">#{{ $img->id }}</span>
                                                </div>

                                                <div class="text-muted small">
                                                    ID: {{ \Illuminate\Support\Str::limit($img->title_id, 25) }}
                                                    @if($img->title_en)
                                                        <span class="badge rounded-pill bg-secondary-subtle text-body ms-1">EN</span>
                                                    @endif
                                                    @if($img->title_ar)
                                                        <span class="badge rounded-pill bg-secondary-subtle text-body ms-1">AR</span>
                                                    @endif
                                                </div>

                                                <div class="text-muted small">
                                                    {{ \Illuminate\Support\Str::limit($img->description_id, 80) ?: '—' }}
                                                </div>

                                                @if($img->link_url)
                                                    <div class="small mt-1">
                                                        <a href="{{ $img->link_url }}" target="_blank" rel="noopener" class="text-decoration-none">
                                                            {{ $img->link_url }}
                                                        </a>
                                                    </div>
                                                @endif
                                            </td>

                                            <td class="text-nowrap">
                                                <div class="text-muted">
                                                    <i class="bi bi-calendar2-week me-1"></i>
                                                    {{ $img->published_at?->format('d M Y H:i') ?? '—' }}
                                                </div>
                                            </td>

                                            <td>
                                                <span class="badge {{ $badge }}">{{ $statusText }}</span>
                                            </td>

                                            <td class="text-end">
                                                <div class="d-flex justify-content-end gap-1">
                                                    @if($img->is_published && $img->link_url)
                                                        <a href="{{ $img->link_url }}" target="_blank" rel="noopener"
                                                        class="btn btn-sm btn-primary" title="Kunjungi">
                                                            <i class="bi bi-box-arrow-up-right"></i>
                                                        </a>
                                                    @endif

                                                    <a href="{{ route('admin.gallery.edit', $img->id) }}"
                                                    class="btn btn-sm btn-warning text-white" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>

                                                    <form action="{{ route('admin.gallery.destroy', $img->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Hapus item ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
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
                                {{ $images->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="row g-3">
                        @forelse($images as $img)
                            @php
                                $path = $img->image_path;

                                if (\Illuminate\Support\Str::startsWith($path, ['http://','https://'])) {
                                    $imgUrl = $path;
                                } elseif (\Illuminate\Support\Str::startsWith($path, ['storage/', 'gallery/'])) {
                                    $imgUrl = \Illuminate\Support\Facades\Storage::url($path);
                                } else {
                                    $imgUrl = asset($path);
                                }

                                $badge = $img->is_published ? 'bg-success' : 'bg-warning text-dark';
                                $statusText = $img->is_published ? 'Published' : 'Draft';
                            @endphp

                            <div class="col-12 col-sm-6 col-lg-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                            <div class="min-w-0">
                                                <div class="fw-semibold mb-0 text-truncate" title="{{ $img->title_id }}">
                                                    {{ $img->title_id }}
                                                </div>
                                                <div class="text-muted small">
                                                    ID: {{ $img->id }}
                                                    • {{ $img->published_at?->format('d M Y H:i') ?? '—' }}
                                                </div>
                                            </div>
                                            <span class="badge {{ $badge }}">{{ $statusText }}</span>
                                        </div>

                                        <div class="ratio ratio-1x1 rounded overflow-hidden mb-3">
                                            <img src="{{ $imgUrl }}" alt="{{ $img->title_id }}" style="object-fit: cover;">
                                        </div>

                                        <div class="text-muted small mb-2">
                                            {{ \Illuminate\Support\Str::limit($img->description_id, 120) ?: '—' }}
                                        </div>

                                        @if($img->link_url)
                                            <div class="small mb-3">
                                                <a href="{{ $img->link_url }}" target="_blank" rel="noopener" class="text-decoration-none">
                                                    {{ $img->link_url }}
                                                </a>
                                            </div>
                                        @endif

                                        <div class="d-flex flex-wrap gap-2">
                                            @if($img->link_url)
                                                <a href="{{ $img->link_url }}" target="_blank" rel="noopener"
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-box-arrow-up-right"></i> Kunjungi
                                                </a>
                                            @endif

                                            <a href="{{ route('admin.gallery.edit', $img->id) }}"
                                               class="btn btn-sm btn-outline-warning">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </a>

                                            <form action="{{ route('admin.gallery.destroy', $img->id) }}"
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
                                            @if($img->title_en)
                                                <span class="badge rounded-pill bg-secondary-subtle text-body me-1">EN</span>
                                            @endif
                                            @if($img->title_ar)
                                                <span class="badge rounded-pill bg-secondary-subtle text-body me-1">AR</span>
                                            @endif
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
                        {{ $images->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
                    </div>
                @endif

            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-file-earmark-text display-6 text-muted d-block mb-2"></i>
                        <p class="text-muted mb-3">Belum ada galeri.</p>
                        <a href="{{ route('admin.gallery.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-lg me-1"></i> Buat yang pertama
                        </a>
                    </div>
                </div>
            @endif

        </section>
    </div>

    {{-- Offcanvas Filter (Right Side) --}}
    <div class="offcanvas offcanvas-end mazer-filter" tabindex="-1" id="filterOffcanvas" aria-labelledby="filterOffcanvasLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="filterOffcanvasLabel">
                <i class="bi bi-funnel"></i> Filter Galeri
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <div class="offcanvas-body">
            <form method="GET" action="{{ route('admin.gallery.index') }}">
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
                        <option value="latest"     {{ $sort === 'latest' ? 'selected' : '' }}>Terbaru terbit</option>
                        <option value="oldest"     {{ $sort === 'oldest' ? 'selected' : '' }}>Terlama terbit</option>
                        <option value="title_asc"  {{ $sort === 'title_asc' ? 'selected' : '' }}>Judul A–Z</option>
                        <option value="title_desc" {{ $sort === 'title_desc' ? 'selected' : '' }}>Judul Z–A</option>
                    </select>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Terapkan
                    </button>

                    <a href="{{ route('admin.gallery.index', array_filter(['view' => $view])) }}"
                       class="btn btn-outline-secondary w-100">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-page.admin>
