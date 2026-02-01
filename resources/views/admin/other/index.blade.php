<x-page.admin>
    @php
        $view = (string) request('view', 'list');
        $qsBase = request()->except('page');

        $gridUrl = request()->url() . '?' . http_build_query(array_merge($qsBase, ['view' => 'grid']));
        $listUrl = request()->url() . '?' . http_build_query(array_merge($qsBase, ['view' => 'list']));

        $q    = trim((string) request('q', ''));
    @endphp

    <div class="page-heading">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
            <div>
                <h3 class="mb-1">Guru</h3>
                <p class="text-muted mb-0">Kelola pengguna. Aksi admin: hanya hapus untuk role <code>guru</code>.</p>
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
                        data-bs-target="#filterOffcanvasUser"
                        aria-controls="filterOffcanvasUser">
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
                        Menampilkan
                        <strong>{{ $users->count() }}</strong>
                        dari
                        <strong>{{ $users->total() }}</strong>
                        data.
                    </div>
                    <div class="small text-muted">
                        @if($users->total() > 0)
                            Range:
                            <strong>{{ $users->firstItem() }}</strong>–<strong>{{ $users->lastItem() }}</strong>
                            | Halaman:
                            <strong>{{ $users->currentPage() }}</strong>/<strong>{{ $users->lastPage() }}</strong>
                        @else
                            Tidak ada data
                        @endif
                    </div>
                </div>
            </div>

            {{-- Content --}}
            @if($users->count())
                @if($view === 'list')
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width:72px">No</th>
                                            <th>Info</th>
                                            <th style="width:180px">Role</th>
                                            <th style="width:160px" class="text-end">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($users as $i => $u)
                                            @php
                                                $uRole = $u->role ?? 'user';
                                                $badge = $uRole === 'user'
                                                    ? 'bg-light-primary text-primary'
                                                    : 'bg-light-secondary text-secondary';
                                            @endphp

                                            <tr>
                                                <td class="text-muted">{{ $users->firstItem() + $i }}</td>

                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="min-w-0">
                                                            <div class="fw-semibold text-truncate" title="{{ $u->name }}">
                                                                {{ $u->name }}
                                                                <span class="text-muted">#{{ $u->id }}</span>
                                                            </div>
                                                            <div class="text-muted small text-truncate" title="{{ $u->email }}">
                                                                <i class="bi bi-envelope me-1"></i>{{ $u->email }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td>
                                                    <span class="badge {{ $badge }}">
                                                        <i class="bi {{ $uRole === 'user' ? 'bi-person' : 'bi-person-badge' }} me-1"></i>
                                                        {{ $uRole }}
                                                    </span>
                                                </td>

                                                <td class="text-end">
                                                    <div class="d-flex justify-content-end gap-1">
                                                        @if($uRole === 'user')
                                                            <form action="{{ route('admin.users.destroy', $u->id) }}"
                                                                  method="POST"
                                                                  onsubmit="return confirm('Hapus user ini?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </form>
                                                        @else
                                                            <button class="btn btn-sm btn-outline-secondary" disabled title="Tidak diizinkan">
                                                                <i class="bi bi-lock"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                {{ $users->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="row g-3">
                        @foreach($users as $u)
                            @php
                                $uRole = $u->role ?? 'user';
                                $badge = $uRole === 'user'
                                    ? 'bg-light-primary text-primary'
                                    : 'bg-light-secondary text-secondary';
                            @endphp

                            <div class="col-12 col-sm-6 col-lg-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                            <div class="min-w-0">
                                                <div class="fw-semibold text-truncate" title="{{ $u->name }}">
                                                    {{ $u->name }}
                                                </div>
                                                <div class="text-muted small text-truncate" title="{{ $u->email }}">
                                                    <i class="bi bi-envelope me-1"></i>{{ $u->email }}
                                                </div>
                                            </div>

                                            <span class="badge {{ $badge }}">{{ $uRole }}</span>
                                        </div>

                                        <hr class="my-3">

                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="small text-muted">
                                                ID: <strong>#{{ $u->id }}</strong>
                                            </div>

                                            @if($uRole === 'user')
                                                <form action="{{ route('admin.users.destroy', $u->id) }}"
                                                      method="POST"
                                                      onsubmit="return confirm('Hapus user ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-trash"></i> Hapus
                                                    </button>
                                                </form>
                                            @else
                                                <button class="btn btn-sm btn-outline-secondary" disabled>
                                                    <i class="bi bi-lock"></i> Terkunci
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-3">
                        {{ $users->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-people display-6 text-muted d-block mb-2"></i>
                        <p class="text-muted mb-0">Belum ada user.</p>
                    </div>
                </div>
            @endif
        </section>
    </div>

    {{-- Offcanvas Filter (Right Side) --}}
    <div class="offcanvas offcanvas-end mazer-filter" tabindex="-1" id="filterOffcanvasUser" aria-labelledby="filterOffcanvasUserLabel">
      <div class="offcanvas-header">
          <h5 class="offcanvas-title" id="filterOffcanvasUserLabel">
              <i class="bi bi-funnel"></i> Filter User
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>

      <div class="offcanvas-body">
          <form method="GET" action="{{ route('admin.users.index') }}">
              <input type="hidden" name="view" value="{{ $view }}">

              <div class="mb-3">
                  <label class="form-label">Kata kunci</label>
                  <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Cari nama / email…">
              </div>

              <div class="d-flex gap-2">
                  <button type="submit" class="btn btn-primary w-100">
                      <i class="bi bi-search"></i> Terapkan
                  </button>

                  <a href="{{ route('admin.users.index', array_filter(['view' => $view])) }}"
                    class="btn btn-outline-secondary w-100">
                      Reset
                  </a>
              </div>
          </form>
      </div>
    </div>
</x-page.admin>
