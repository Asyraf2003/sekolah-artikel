<x-page.admin>
    @php
        $tab = $status ?: 'all';

        $allUrl = route('admin.comments.index', array_filter(['q'=>$q,'article_id'=>$articleId]));
        $tabUrl = fn($s) => route('admin.comments.index', array_filter(['status'=>$s,'q'=>$q,'article_id'=>$articleId]));

        $badge = fn($s) => $counts[$s] ?? 0;
        $countsAll = $countsAll ?? ($comments->total() ?? 0);

        $map = ['pending'=>'warning','approved'=>'success','rejected'=>'secondary','spam'=>'danger'];
        $tabStyles = ['pending'=>'warning','approved'=>'success','rejected'=>'secondary','spam'=>'danger'];
    @endphp

    <div class="page-heading">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
            <div>
                <h3 class="mb-1">Komentar</h3>
                <p class="text-muted mb-0">Moderasi komentar artikel (approve, reject, spam, hapus).</p>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <button class="btn btn-outline-secondary"
                        type="button"
                        data-bs-toggle="offcanvas"
                        data-bs-target="#filterOffcanvas"
                        aria-controls="filterOffcanvas">
                    <i class="bi bi-funnel"></i>
                    Filter
                </button>

                <a href="{{ route('admin.comments.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-counterclockwise"></i>
                    Reset
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
                        <strong>{{ $comments->count() }}</strong>
                        dari
                        <strong>{{ $comments->total() }}</strong>
                        data.
                    </div>
                    <div class="small text-muted">
                        @if($comments->total() > 0)
                            Range:
                            <strong>{{ $comments->firstItem() }}</strong>–<strong>{{ $comments->lastItem() }}</strong>
                            | Halaman:
                            <strong>{{ $comments->currentPage() }}</strong>/<strong>{{ $comments->lastPage() }}</strong>
                        @else
                            Tidak ada data
                        @endif
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <ul class="nav nav-pills">
                    <li class="nav-item">
                        <a class="nav-link {{ $tab==='all' ? 'active' : '' }}" href="{{ $allUrl }}">
                            Semua <span class="badge bg-secondary ms-1">{{ $countsAll }}</span>
                        </a>
                    </li>

                    @foreach($tabStyles as $s => $cls)
                        <li class="nav-item">
                            <a class="nav-link {{ $tab===$s ? 'active' : '' }}" href="{{ $tabUrl($s) }}">
                                {{ ucfirst($s) }}
                                <span class="badge bg-{{ $cls }} ms-1">{{ $badge($s) }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>

                <div class="small text-muted">
                    @if($q) <span class="me-2"><i class="bi bi-search"></i> <strong>{{ $q }}</strong></span> @endif
                    @if($articleId) <span><i class="bi bi-file-earmark-text"></i> Artikel: <strong>#{{ $articleId }}</strong></span> @endif
                </div>
            </div>

            {{-- BULK FORM (JANGAN NESTED) --}}
            <form id="bulk-form" method="POST" action="{{ route('admin.comments.bulk') }}">
                @csrf
                @method('PATCH')
            </form>

            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                            <tr>
                                <th style="width:36px">
                                    <input type="checkbox"
                                           class="form-check-input"
                                           id="chk-all">
                                </th>
                                <th>Isi</th>
                                <th>Penulis</th>
                                <th>Artikel</th>
                                <th style="width:120px">Status</th>
                                <th style="width:160px">Dikirim</th>
                                <th style="width:320px" class="text-end">Aksi</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($comments as $c)
                                <tr>
                                    <td>
                                        <input type="checkbox"
                                               class="form-check-input chk-row"
                                               name="ids[]"
                                               value="{{ $c->id }}"
                                               form="bulk-form">
                                    </td>

                                    <td>
                                        <div class="fw-semibold text-truncate" style="max-width: 420px;">
                                            {{ \Illuminate\Support\Str::limit($c->body, 140) }}
                                        </div>
                                        <div class="small text-muted">
                                            IP: {{ $c->ip ?? '-' }}
                                        </div>
                                    </td>

                                    <td>
                                        @if($c->user)
                                            <div class="fw-semibold">{{ $c->user->name }}</div>
                                            <div class="small text-muted">{{ $c->user->email }}</div>
                                        @else
                                            <div class="fw-semibold">{{ $c->guest_name ?? 'Tamu' }}</div>
                                            <div class="small text-muted">{{ $c->guest_email ?? '-' }}</div>
                                        @endif
                                    </td>

                                    <td class="text-truncate" style="max-width: 260px;">
                                        @if($c->article)
                                            <a href="{{ route('article', $c->article->slug) }}#comments" target="_blank" class="text-decoration-none">
                                                {{ $c->article->title_id }}
                                            </a>
                                            <div class="small text-muted">#{{ $c->article->id }}</div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    <td>
                                        <span class="badge bg-{{ $map[$c->status] ?? 'secondary' }}">
                                            {{ ucfirst($c->status) }}
                                        </span>
                                    </td>

                                    <td class="text-nowrap">
                                        <div>{{ $c->created_at->format('d M Y H:i') }}</div>
                                        <div class="small text-muted">{{ $c->created_at->diffForHumans() }}</div>
                                    </td>

                                    <td class="text-end text-nowrap">
                                        <div class="d-inline-flex flex-nowrap gap-1">
                                            <form method="POST" action="{{ route('admin.comments.update', $c->id) }}" class="d-inline-flex">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="approved">
                                                <button class="btn btn-sm btn-success">Approve</button>
                                            </form>

                                            <form method="POST" action="{{ route('admin.comments.update', $c->id) }}" class="d-inline-flex">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="rejected">
                                                <button class="btn btn-sm btn-outline-secondary">Reject</button>
                                            </form>

                                            <form method="POST" action="{{ route('admin.comments.update', $c->id) }}" class="d-inline-flex">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="spam">
                                                <button class="btn btn-sm btn-outline-danger">Spam</button>
                                            </form>

                                            <form method="POST"
                                                  action="{{ route('admin.comments.destroy', $c->id) }}"
                                                  class="d-inline-flex"
                                                  onsubmit="return confirm('Hapus komentar ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-light">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        Tidak ada komentar.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer d-flex flex-wrap gap-2 justify-content-between align-items-center">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <div class="text-muted small">
                            Action akan berlaku untuk checkbox yang dipilih.
                        </div>

                        <select name="action" class="form-select form-select-sm" style="width: 180px" form="bulk-form" required>
                            <option value="">Action</option>
                            <option value="approve">Approve</option>
                            <option value="reject">Reject</option>
                            <option value="spam">Spam</option>
                            <option value="delete">Delete</option>
                        </select>

                        <button type="submit" class="btn btn-sm btn-primary" form="bulk-form">
                            Jalankan
                        </button>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        {{ $comments->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>

        </section>
    </div>

    <div class="offcanvas offcanvas-end mazer-filter" tabindex="-1" id="filterOffcanvas" aria-labelledby="filterOffcanvasLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="filterOffcanvasLabel">
                <i class="bi bi-funnel"></i> Filter Komentar
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <div class="offcanvas-body">
            <form method="GET" action="{{ route('admin.comments.index') }}">
                <input type="hidden" name="status" value="{{ $status }}">

                <div class="mb-3">
                    <label class="form-label">Cari</label>
                    <input name="q"
                           value="{{ $q }}"
                           class="form-control"
                           placeholder="nama, email, isi komentar">
                </div>

                <div class="mb-3">
                    <label class="form-label">Artikel</label>
                    <select name="article_id" class="form-select">
                        <option value="">— Semua Artikel —</option>
                        @foreach($articles as $a)
                            <option value="{{ $a->id }}" {{ (string)$articleId === (string)$a->id ? 'selected' : '' }}>
                                {{ $a->title_id }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Terapkan
                    </button>

                    <a href="{{ route('admin.comments.index') }}"
                       class="btn btn-outline-secondary w-100">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const chkAll = document.getElementById('chk-all');
                const rows = Array.from(document.querySelectorAll('.chk-row'));
                if (!chkAll || rows.length === 0) return;

                const syncMaster = () => {
                    const checked = rows.filter(x => x.checked).length;
                    chkAll.checked = checked === rows.length;
                    chkAll.indeterminate = checked > 0 && checked < rows.length;
                };

                chkAll.addEventListener('change', () => {
                    rows.forEach(c => c.checked = chkAll.checked);
                    syncMaster();
                });

                rows.forEach(c => c.addEventListener('change', syncMaster));
                syncMaster();
            });
        </script>
    @endpush
</x-page.admin>
