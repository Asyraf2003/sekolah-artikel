<x-page.admin>
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
    <div>
      <h3 class="mb-0">PPDB</h3>
      <div class="text-muted small">Kelola pendaftaran, verifikasi, dan aktivasi akun (token sekali pakai)</div>
    </div>

    <div class="d-flex align-items-center justify-content-between mb-3">
      <div class="d-flex gap-2 flex-wrap">

        {{-- Search --}}
        <form method="GET" action="{{ route('admin.ppdb.index') }}" class="d-none d-md-flex">
          <div class="input-group">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                   placeholder="Cari nama / email / WA / kode…">

            {{-- Persist view/filter --}}
            <input type="hidden" name="view" value="{{ request('view','list') }}">
            <input type="hidden" name="status" value="{{ request('status') }}">
            <input type="hidden" name="per_page" value="{{ request('per_page', 15) }}">
            <input type="hidden" name="sort" value="{{ request('sort','latest') }}">

            <button class="btn btn-primary"><i class="bi bi-search"></i></button>

            @if(request('q'))
              <a href="{{ route('admin.ppdb.index', array_filter([
                'view' => request('view','list'),
                'status' => request('status'),
                'per_page' => request('per_page', 15),
                'sort' => request('sort','latest'),
              ])) }}" class="btn btn-outline-secondary">Reset</a>
            @endif
          </div>
        </form>

        {{-- Toggle layout (list / grid) --}}
        @php $viewMode = request('view','list'); @endphp
        <div class="btn-group" role="group" aria-label="Tampilan">
          <a href="{{ request()->fullUrlWithQuery(['view'=>'list']) }}"
             class="btn btn-primary {{ $viewMode==='list'?'active':'' }}">
            <i class="bi bi-list-ul"></i>
          </a>
          <a href="{{ request()->fullUrlWithQuery(['view'=>'grid']) }}"
             class="btn btn-primary {{ $viewMode==='grid'?'active':'' }}">
            <i class="bi bi-grid-3x3-gap"></i>
          </a>
        </div>

        {{-- Filter --}}
        <button class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#filterPpdbOffcanvas">
          <i class="bi bi-sliders"></i> Filter
        </button>

      </div>
    </div>
  </div>

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

  @if($ppdbs->count())
    @if($viewMode === 'grid')
      <div class="row gy-3 gy-lg-4">
        @foreach($ppdbs as $p)
          @php
            $badge = [
              'submitted' => 'bg-warning text-dark',
              'approved'  => 'bg-info text-dark',
              'activated' => 'bg-success',
              'rejected'  => 'bg-danger',
            ][$p->status] ?? 'bg-secondary';
          @endphp

          <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
            <div class="card h-100">
              <div class="card-body d-flex flex-column">
                <div class="d-flex justify-content-between align-items-start mb-2">
                  <div class="min-w-0">
                    <h6 class="card-title mb-1 text-truncate" title="{{ $p->full_name }}">
                      {{ $p->full_name }}
                    </h6>
                    <div class="small text-muted text-truncate" title="{{ $p->email }}">
                      <i class="bi bi-envelope me-1"></i>{{ $p->email }}
                    </div>
                    <div class="small text-muted text-truncate" title="{{ $p->whatsapp }}">
                      <i class="bi bi-whatsapp me-1"></i>{{ $p->whatsapp }}
                    </div>
                  </div>

                  <span class="badge {{ $badge }}">{{ ucfirst($p->status) }}</span>
                </div>

                <div class="small text-muted mb-3">
                  <div><i class="bi bi-hash me-1"></i><span class="badge bg-light text-dark">{{ $p->public_code }}</span></div>
                  <div class="mt-1">
                    <i class="bi bi-calendar2-week me-1"></i>{{ $p->created_at?->format('d M Y H:i') }}
                  </div>
                </div>

                <div class="mt-auto d-flex gap-2 flex-wrap">
                  <a href="{{ route('admin.ppdb.show', $p->id) }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-eye"></i> Lihat
                  </a>

                  @if($p->status === 'submitted')
                    <span class="badge bg-secondary align-self-center">Menunggu verifikasi</span>
                  @elseif($p->status === 'approved')
                    <span class="badge bg-secondary align-self-center">Siap aktivasi</span>
                  @elseif($p->status === 'activated')
                    <span class="badge bg-secondary align-self-center">Akun dibuat</span>
                  @elseif($p->status === 'rejected')
                    <span class="badge bg-secondary align-self-center">Butuh perbaikan</span>
                  @endif
                </div>
              </div>
            </div>
          </div>
        @endforeach
      </div>

      <div class="card border-0 shadow-sm mt-3">
        <div class="card-footer d-flex flex-wrap gap-2 justify-content-between align-items-center">
          <div class="text-body-secondary small">
            Menampilkan {{ $ppdbs->firstItem() }}–{{ $ppdbs->lastItem() }} dari {{ $ppdbs->total() }} data
          </div>
          <div>{{ $ppdbs->withQueryString()->links() }}</div>
        </div>
      </div>

    @else
      <div class="card border-0 shadow-sm">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="bg-body-tertiary">
              <tr>
                <th style="width:34%" class="text-body-secondary text-uppercase small">Pendaftar</th>
                <th class="text-body-secondary text-uppercase small">Kontak</th>
                <th class="text-body-secondary text-uppercase small">Kode</th>
                <th class="text-body-secondary text-uppercase small">Status</th>
                <th class="text-body-secondary text-uppercase small">Dibuat</th>
                <th class="text-end text-body-secondary text-uppercase small">Aksi</th>
              </tr>
            </thead>

            <tbody class="table-group-divider">
              @foreach($ppdbs as $p)
                @php
                  $badge = [
                    'submitted' => 'bg-warning text-dark',
                    'approved'  => 'bg-info text-dark',
                    'activated' => 'bg-success',
                    'rejected'  => 'bg-danger',
                  ][$p->status] ?? 'bg-secondary';
                @endphp

                <tr>
                  <td>
                    <div class="fw-semibold">{{ $p->full_name }}</div>
                    <div class="small text-body-secondary">
                      ID: {{ $p->id }}
                      @if($p->user_id)
                        <span class="badge rounded-pill bg-secondary-subtle text-body ms-1">User #{{ $p->user_id }}</span>
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
                    <span class="badge {{ $badge }}">{{ ucfirst($p->status) }}</span>
                  </td>

                  <td class="text-nowrap">
                    <i class="bi bi-calendar2-week me-1"></i>
                    {{ $p->created_at?->format('d M Y H:i') ?? '—' }}
                  </td>

                  <td class="text-end">
                    <div class="d-inline-flex gap-1">
                      <a href="{{ route('admin.ppdb.show', $p->id) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye me-1"></i> Lihat
                      </a>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="card-footer d-flex flex-wrap gap-2 justify-content-between align-items-center">
          <div class="text-body-secondary small">
            Menampilkan {{ $ppdbs->firstItem() }}–{{ $ppdbs->lastItem() }} dari {{ $ppdbs->total() }} data
          </div>
          <div>{{ $ppdbs->withQueryString()->links() }}</div>
        </div>
      </div>
    @endif
  @else
    <div class="card border-0 shadow-sm">
      <div class="card-body text-center py-5">
        <i class="bi bi-file-earmark-text display-6 text-muted d-block mb-2"></i>
        <p class="text-muted mb-3">Belum ada pendaftar PPDB.</p>
      </div>
    </div>
  @endif

  {{-- Filter Offcanvas --}}
  <div class="offcanvas offcanvas-end"
     tabindex="-1"
     id="filterPpdbOffcanvas"
     aria-labelledby="filterPpdbOffcanvasLabel">
    <div class="offcanvas-header border-bottom">
      <h5 class="offcanvas-title" id="filterPpdbOffcanvasLabel">Filter PPDB</h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body">
      <form method="GET" action="{{ route('admin.ppdb.index') }}" class="vstack gap-3">
        {{-- Persist query & view --}}
        <input type="hidden" name="q" value="{{ request('q') }}">
        <input type="hidden" name="view" value="{{ request('view','list') }}">

        <div>
          <label class="form-label">Status</label>
          <select name="status" class="form-select">
            <option value="" @selected(request('status', '')==='')>Semua</option>
            @foreach (['submitted','approved','activated','rejected'] as $st)
              <option value="{{ $st }}" @selected(request('status')===$st)>{{ ucfirst($st) }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="form-label">Per halaman</label>
          <select name="per_page" class="form-select">
            @foreach ([10,15,25,50] as $n)
              <option value="{{ $n }}" @selected((int)request('per_page',15)===$n)>{{ $n }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="form-label">Urutkan</label>
          <select name="sort" class="form-select">
            <option value="latest"  @selected(request('sort','latest')==='latest')>Terbaru</option>
            <option value="oldest"  @selected(request('sort')==='oldest')>Terlama</option>
            <option value="name_asc"  @selected(request('sort')==='name_asc')>Nama A–Z</option>
            <option value="name_desc" @selected(request('sort')==='name_desc')>Nama Z–A</option>
            <option value="status_asc" @selected(request('sort')==='status_asc')>Status A–Z</option>
            <option value="status_desc" @selected(request('sort')==='status_desc')>Status Z–A</option>
          </select>
          <div class="small text-muted mt-1">Urutan ini butuh controller support (kalau belum, aman diabaikan).</div>
        </div>

        <div class="d-grid gap-2 pt-2">
          <button class="btn btn-primary">
            <i class="bi bi-funnel me-1"></i> Terapkan
          </button>

          @php
            $hasFilters = request()->filled('status') || request()->filled('per_page') || request()->filled('sort');
          @endphp

          @if($hasFilters)
            <a href="{{ route('admin.ppdb.index', array_filter(['q'=>request('q'),'view'=>request('view','list')])) }}"
              class="btn btn-outline-secondary">
              Reset
            </a>
          @endif
        </div>
      </form>
    </div>
  </div>

  @push('scripts')
  <script>
    (function () {
      const offcanvasEl = document.getElementById('filterPpdbOffcanvas');
      if (!offcanvasEl) return;

      // Anggap dark mode pakai class "dark" di <html> (umum di Tailwind)
      const isDark = document.documentElement.classList.contains('dark');

      if (isDark) {
        offcanvasEl.setAttribute('data-bs-theme', 'dark');

        // Biar background/border/teks enak dilihat (tanpa ngerusak light mode)
        offcanvasEl.classList.add('bg-body', 'text-body');
        offcanvasEl.querySelector('.offcanvas-header')?.classList.add('border-secondary');

        // Form controls di dark mode kadang pucat, paksa lebih kontras
        offcanvasEl.querySelectorAll('.form-select, .form-control').forEach(el => {
          el.classList.add('bg-body', 'text-body', 'border-secondary');
        });

        // Small text muted kadang terlalu redup
        offcanvasEl.querySelectorAll('.text-muted').forEach(el => {
          el.classList.add('opacity-75');
        });
      }
    })();
  </script>
  @endpush
  @push('styles')
  <style>
    /* asumsi dark mode pakai class "dark" di <html> */
    html.dark #filterPpdbOffcanvas.offcanvas {
      background-color: #0b1220 !important;
      color: rgba(255,255,255,.88) !important;
      border-left: 1px solid rgba(255,255,255,.08) !important;
    }

    html.dark #filterPpdbOffcanvas .offcanvas-header {
      border-bottom-color: rgba(255,255,255,.10) !important;
    }

    html.dark #filterPpdbOffcanvas .offcanvas-title,
    html.dark #filterPpdbOffcanvas label {
      color: rgba(255,255,255,.88) !important;
    }

    html.dark #filterPpdbOffcanvas .text-muted {
      color: rgba(255,255,255,.60) !important;
    }

    /* form control/select biar konsisten */
    html.dark #filterPpdbOffcanvas .form-control,
    html.dark #filterPpdbOffcanvas .form-select {
      background-color: #0f172a !important;
      color: rgba(255,255,255,.88) !important;
      border-color: rgba(255,255,255,.12) !important;
    }

    html.dark #filterPpdbOffcanvas .form-control:focus,
    html.dark #filterPpdbOffcanvas .form-select:focus {
      box-shadow: 0 0 0 .2rem rgba(56,189,248,.25) !important; /* sky-ish */
      border-color: rgba(56,189,248,.45) !important;
    }

    /* tombol close di dark mode biasanya “hilang” */
    html.dark #filterPpdbOffcanvas .btn-close {
      filter: invert(1) grayscale(100%) opacity(.85);
    }

    /* tombol reset outline biar gak pudar */
    html.dark #filterPpdbOffcanvas .btn-outline-secondary {
      color: rgba(255,255,255,.80) !important;
      border-color: rgba(255,255,255,.20) !important;
    }
    html.dark #filterPpdbOffcanvas .btn-outline-secondary:hover {
      background-color: rgba(255,255,255,.06) !important;
    }
  </style>
  @endpush
</x-page.admin>
