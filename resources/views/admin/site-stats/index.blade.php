<x-page.admin>
    @php
        $backUrl = route('admin.announcements.index'); // opsional: ganti kalau kamu punya dashboard route
    @endphp

    <div class="page-heading">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
            <div>
                <h3 class="mb-1">Statistik Website</h3>
                <p class="text-muted mb-0">Kelola kotak statistik di halaman utama (slot 1–4).</p>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ $backUrl }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
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

            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="fw-semibold">
                        <i class="bi bi-bar-chart-line me-1"></i> Daftar Statistik
                    </div>
                    <div class="small text-muted">
                        Total: <strong>{{ $stats->count() }}</strong> item
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                            <tr>
                                <th style="width: 80px;">Slot</th>
                                <th style="width: 120px;">Value</th>
                                <th>Konten (ID / EN / AR)</th>
                                <th style="width: 130px;">Status</th>
                                <th style="width: 120px;">Urutan</th>
                                <th style="width: 120px;" class="text-end">Aksi</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($stats as $s)
                                @php
                                    $badge = $s->is_active ? 'bg-success' : 'bg-secondary';
                                    $statusText = $s->is_active ? 'Active' : 'Hidden';

                                    $labelId = $s->label_id ?: '—';
                                    $labelEn = $s->label_en ?: '—';
                                    $labelAr = $s->label_ar ?: '—';

                                    $descId = $s->desc_id ?: '—';
                                    $descEn = $s->desc_en ?: '—';
                                    $descAr = $s->desc_ar ?: '—';
                                @endphp

                                <tr>
                                    <td class="text-nowrap">
                                        <span class="fw-semibold">#{{ $s->slot }}</span>
                                    </td>

                                    <td class="text-nowrap">
                                        <div class="fw-semibold">{{ $s->value }}</div>
                                        <div class="small text-muted">ID: {{ $s->id }}</div>
                                    </td>

                                    <td class="min-w-0">
                                        <div class="fw-semibold">
                                            {{ $labelId }}
                                            <span class="text-muted small">(ID)</span>
                                        </div>
                                        <div class="text-muted small">
                                            {{ \Illuminate\Support\Str::limit(strip_tags($descId), 90) }}
                                        </div>

                                        <div class="mt-2 small">
                                            <span class="badge rounded-pill bg-secondary-subtle text-body me-1">EN</span>
                                            <span class="text-muted">{{ \Illuminate\Support\Str::limit($labelEn, 40) }}</span>
                                        </div>
                                        <div class="text-muted small">
                                            {{ \Illuminate\Support\Str::limit(strip_tags($descEn), 80) }}
                                        </div>

                                        <div class="mt-2 small">
                                            <span class="badge rounded-pill bg-secondary-subtle text-body me-1">AR</span>
                                            <span class="text-muted">{{ \Illuminate\Support\Str::limit($labelAr, 40) }}</span>
                                        </div>
                                        <div class="text-muted small">
                                            {{ \Illuminate\Support\Str::limit(strip_tags($descAr), 80) }}
                                        </div>
                                    </td>

                                    <td>
                                        <span class="badge {{ $badge }}">{{ $statusText }}</span>
                                    </td>

                                    <td class="text-nowrap">
                                        <span class="fw-semibold">{{ $s->sort_order }}</span>
                                    </td>

                                    <td class="text-end">
                                        <a href="{{ route('admin.site-stats.edit', $s->id) }}"
                                           class="btn btn-sm btn-warning text-white" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        Tidak ada data. Jalankan seeder <code>SiteStatSeeder</code>.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3 small text-muted">
                        Catatan: Statistik ini bersifat slot tetap. Admin hanya bisa edit (tidak ada create).
                    </div>
                </div>
            </div>

        </section>
    </div>
</x-page.admin>
