<x-page.admin>
    @php
        // status enum -> string key
        $statusKey = $ppdb->status instanceof \App\Enums\PpdbStatus
            ? $ppdb->status->value
            : (string) $ppdb->status;

        $statusMeta = [
            'submitted' => ['badge' => 'bg-warning text-dark', 'text' => 'Submitted', 'hint' => 'Menunggu verifikasi admin'],
            'approved'  => ['badge' => 'bg-info text-dark',    'text' => 'Approved',  'hint' => 'Siap aktivasi (buat link aktivasi)'],
            'rejected'  => ['badge' => 'bg-danger',            'text' => 'Rejected',  'hint' => 'Butuh perbaikan (buat link edit)'],
            'activated' => ['badge' => 'bg-success',           'text' => 'Activated', 'hint' => 'Akun user sudah dibuat'],
        ];

        $meta = $statusMeta[$statusKey] ?? ['badge' => 'bg-secondary', 'text' => $statusKey ?: '—', 'hint' => ''];

        // helper cek status (enum / string)
        $isSubmitted = $statusKey === 'submitted';
        $isApproved  = $statusKey === 'approved';
        $isRejected  = $statusKey === 'rejected';
        $isActivated = $statusKey === 'activated';
    @endphp

    {{-- PAGE HEADING (ngikut gaya Create Gallery) --}}
    <div class="page-heading">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
            <div>
                <h3 class="mb-1">Detail PPDB</h3>
                <p class="text-muted mb-0">
                    {{ $ppdb->full_name }} •
                    <span class="badge bg-light text-dark">{{ $ppdb->public_code }}</span>
                </p>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.ppdb.index', request()->query()) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>

                {{-- Aksi utama di header: muncul sesuai status --}}
                @if($isSubmitted)
                    <form action="{{ route('admin.ppdb.approve', $ppdb->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle"></i> Approve
                        </button>
                    </form>

                    <button class="btn btn-danger" type="button" data-bs-toggle="modal" data-bs-target="#modalReject">
                        <i class="bi bi-x-circle"></i> Reject
                    </button>
                @elseif($isApproved)
                    <form action="{{ route('admin.ppdb.approve', $ppdb->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-outline-success">
                            <i class="bi bi-arrow-repeat"></i> Regenerate Link Aktivasi
                        </button>
                    </form>
                @elseif($isRejected)
                    <form action="{{ route('admin.ppdb.reject', $ppdb->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="reason" value="{{ $ppdb->rejected_reason ?: 'Perbaiki data dan kirim ulang.' }}">
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="bi bi-arrow-repeat"></i> Regenerate Link Edit
                        </button>
                    </form>
                @endif
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

            {{-- Activation link alert --}}
            @if (session('activation_link'))
                <div class="alert alert-info">
                    <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center">
                        <div class="fw-semibold">
                            <i class="bi bi-link-45deg me-1"></i> Activation link dibuat (sekali pakai)
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-dark" id="btnCopyActivationLink">
                                <i class="bi bi-clipboard"></i> Copy
                            </button>
                            <a class="btn btn-sm btn-primary" href="{{ session('activation_link') }}" target="_blank" rel="noopener">
                                <i class="bi bi-box-arrow-up-right"></i> Buka
                            </a>
                        </div>
                    </div>
                    <div class="small text-muted mt-2">
                        Kirim link ini ke user via email/WA. Setelah dipakai, token otomatis tidak berlaku.
                    </div>

                    <div class="mt-2">
                        <code class="d-block p-2 bg-light rounded" id="activationLinkText">{{ session('activation_link') }}</code>
                    </div>
                </div>
            @endif

            {{-- Edit link alert --}}
            @if (session('edit_link'))
                <div class="alert alert-warning">
                    <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center">
                        <div class="fw-semibold">
                            <i class="bi bi-link-45deg me-1"></i> Edit link dibuat (sekali pakai)
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-dark" id="btnCopyEditLink">
                                <i class="bi bi-clipboard"></i> Copy
                            </button>
                            <a class="btn btn-sm btn-dark" href="{{ session('edit_link') }}" target="_blank" rel="noopener">
                                <i class="bi bi-box-arrow-up-right"></i> Buka
                            </a>
                        </div>
                    </div>
                    <div class="small text-muted mt-2">
                        Kirim link ini ke user agar bisa memperbaiki data. Setelah dipakai, token otomatis tidak berlaku.
                    </div>

                    <div class="mt-2">
                        <code class="d-block p-2 bg-light rounded" id="editLinkText">{{ session('edit_link') }}</code>
                    </div>
                </div>
            @endif

            <div class="row g-3">
                {{-- LEFT: data utama --}}
                <div class="col-12 col-lg-8">
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="fw-semibold">
                                <i class="bi bi-person-vcard me-1"></i> Data Pendaftar
                            </div>
                            <div class="small text-muted">
                                ID: #{{ $ppdb->id }}
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label text-muted small mb-1">Nama Lengkap</label>
                                    <div class="fw-semibold">{{ $ppdb->full_name }}</div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label text-muted small mb-1">Kode Publik</label>
                                    <div>
                                        <span class="badge bg-light text-dark">{{ $ppdb->public_code }}</span>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label text-muted small mb-1">Email</label>
                                    <div>{{ $ppdb->email }}</div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label text-muted small mb-1">WhatsApp</label>
                                    <div>{{ $ppdb->whatsapp }}</div>
                                </div>
                            </div>

                            <hr class="my-4">

                            {{-- Informasi status --}}
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <div class="text-muted small">Status:</div>
                                <span class="badge {{ $meta['badge'] }}">{{ $meta['text'] }}</span>
                                @if(!empty($meta['hint']))
                                    <span class="text-muted small">• {{ $meta['hint'] }}</span>
                                @endif
                            </div>

                            @if($isRejected && $ppdb->rejected_reason)
                                <div class="alert alert-danger mt-3 mb-0">
                                    <div class="fw-semibold mb-1">
                                        <i class="bi bi-exclamation-circle me-1"></i> Alasan Reject
                                    </div>
                                    <div class="small">{{ $ppdb->rejected_reason }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- RIGHT: status + berkas (ngikut pola kanan create gallery) --}}
                <div class="col-12 col-lg-4">
                    {{-- Status card --}}
                    <div class="card mb-3">
                        <div class="card-header fw-semibold">
                            <i class="bi bi-broadcast me-1"></i> Status & Audit
                        </div>

                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="text-muted small">Status</div>
                                <span class="badge {{ $meta['badge'] }}">{{ $meta['text'] }}</span>
                            </div>

                            <div class="text-muted small mb-2">
                                <i class="bi bi-calendar2-week me-1"></i>
                                Dibuat: {{ $ppdb->created_at?->format('d M Y H:i') ?? '—' }}
                            </div>

                            <div class="text-muted small">
                                <i class="bi bi-shield-check me-1"></i>
                                Diverifikasi:
                                {{ $ppdb->verified_at?->format('d M Y H:i') ?? '—' }}
                                @if($ppdb->verified_by)
                                    (by #{{ $ppdb->verified_by }})
                                @endif
                            </div>

                            @if($ppdb->user_id)
                                <div class="text-muted small mt-2">
                                    <i class="bi bi-person-check me-1"></i>
                                    User ID: <span class="fw-semibold">{{ $ppdb->user_id }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- Footer aksi kecil (opsional) --}}
                        <div class="card-footer bg-body-tertiary d-flex flex-wrap gap-2">
                            @if($isSubmitted)
                                <form action="{{ route('admin.ppdb.approve', $ppdb->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="bi bi-check-circle"></i> Approve
                                    </button>
                                </form>

                                <button class="btn btn-sm btn-danger" type="button" data-bs-toggle="modal" data-bs-target="#modalReject">
                                    <i class="bi bi-x-circle"></i> Reject
                                </button>
                            @elseif($isApproved)
                                <form action="{{ route('admin.ppdb.approve', $ppdb->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-arrow-repeat"></i> Regen Link
                                    </button>
                                </form>
                            @elseif($isRejected)
                                <form action="{{ route('admin.ppdb.reject', $ppdb->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="reason" value="{{ $ppdb->rejected_reason ?: 'Perbaiki data dan kirim ulang.' }}">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-arrow-repeat"></i> Regen Link Edit
                                    </button>
                                </form>
                            @elseif($isActivated)
                                <span class="badge bg-success-subtle text-success align-self-center">
                                    <i class="bi bi-check2-circle me-1"></i> Sudah aktivasi
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Berkas card --}}
                    <div class="card">
                        <div class="card-header fw-semibold">
                            <i class="bi bi-file-earmark-text me-1"></i> Berkas
                        </div>

                        <div class="card-body">
                            <div class="list-group">
                                @foreach ($files as $label => $url)
                                    @if($url)
                                        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                                           href="javascript:void(0)"
                                           data-bs-toggle="modal" data-bs-target="#modalPreview"
                                           data-filelabel="{{ $label }}" data-fileurl="{{ $url }}">
                                            <span><i class="bi bi-file-earmark-text me-2"></i>{{ $label }}</span>
                                            <span class="badge bg-primary">Lihat</span>
                                        </a>
                                    @else
                                        <div class="list-group-item d-flex justify-content-between align-items-center disabled">
                                            <span><i class="bi bi-file-earmark-text me-2"></i>{{ $label }}</span>
                                            <span class="badge bg-secondary">Kosong</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            <div class="form-text mt-2">
                                Gambar bisa dipratinjau langsung; PDF via viewer.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal Reject --}}
            <div class="modal fade" id="modalReject" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form action="{{ route('admin.ppdb.reject', $ppdb->id) }}" method="POST">
                            @csrf
                            @method('PATCH')

                            <div class="modal-header">
                                <h5 class="modal-title">Reject PPDB</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                            </div>

                            <div class="modal-body">
                                <label class="form-label mb-1">Alasan penolakan</label>
                                <textarea name="reason"
                                          class="form-control"
                                          rows="4"
                                          maxlength="2000"
                                          required
                                          placeholder="Jelaskan alasan penolakan (mis: bukti pembayaran tidak valid, data tidak lengkap, dll)">{{ old('reason') }}</textarea>

                                <div class="small text-muted mt-2">
                                    Status akan menjadi <b>rejected</b> dan alasan disimpan.
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-x-circle"></i> Reject
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Modal Preview Berkas --}}
            <div class="modal fade" id="modalPreview" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalPreviewTitle">Preview Berkas</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            <div id="filePreviewContainer" class="text-center">
                                <div class="text-muted">Memuat berkas…</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a id="modalOpenNewTab" href="#" target="_blank" rel="noopener" class="btn btn-outline-secondary">
                                <i class="bi bi-box-arrow-up-right"></i> Buka di Tab Baru
                            </a>
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>

        </section>
    </div>

    @push('scripts')
        <script>
            // copy activation link
            document.getElementById('btnCopyActivationLink')?.addEventListener('click', async () => {
                const el = document.getElementById('activationLinkText');
                const text = el?.innerText?.trim() || '';
                if (!text) return;

                try {
                    await navigator.clipboard.writeText(text);
                } catch (e) {
                    const ta = document.createElement('textarea');
                    ta.value = text;
                    document.body.appendChild(ta);
                    ta.select();
                    document.execCommand('copy');
                    document.body.removeChild(ta);
                }
            });

            // copy edit link
            document.getElementById('btnCopyEditLink')?.addEventListener('click', async () => {
                const el = document.getElementById('editLinkText');
                const text = el?.innerText?.trim() || '';
                if (!text) return;

                try {
                    await navigator.clipboard.writeText(text);
                } catch (e) {
                    const ta = document.createElement('textarea');
                    ta.value = text;
                    document.body.appendChild(ta);
                    ta.select();
                    document.execCommand('copy');
                    document.body.removeChild(ta);
                }
            });

            // preview modal
            document.getElementById('modalPreview')?.addEventListener('show.bs.modal', function (ev) {
                const trigger = ev.relatedTarget;
                const url   = trigger?.getAttribute('data-fileurl');
                const label = trigger?.getAttribute('data-filelabel');

                const titleEl = document.getElementById('modalPreviewTitle');
                const cont    = document.getElementById('filePreviewContainer');
                const openBtn = document.getElementById('modalOpenNewTab');

                titleEl.textContent = 'Preview: ' + (label || 'Berkas');
                openBtn.href = url || '#';

                if (!url) {
                    cont.innerHTML = '<div class="text-muted">Berkas tidak tersedia.</div>';
                    return;
                }

                const isImage = /\.(png|jpe?g|gif|webp|bmp)$/i.test(url);
                const isPDF   = /\.pdf(\?.*)?$/i.test(url);

                if (isImage) {
                    cont.innerHTML = `<img src="${url}" alt="${label || 'Berkas'}" class="img-fluid rounded">`;
                } else if (isPDF) {
                    cont.innerHTML = `<iframe src="${url}" style="width:100%;height:75vh;border:0;" title="PDF Preview"></iframe>`;
                } else {
                    cont.innerHTML = `
                        <div class="alert alert-info mb-0">
                            Format tidak dikenali untuk preview. Gunakan tombol "Buka di Tab Baru".
                        </div>`;
                }
            });
        </script>
    @endpush
</x-page.admin>
