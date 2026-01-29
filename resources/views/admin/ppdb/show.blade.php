<x-page.admin :title="__('Detail PPDB')">
  {{-- Breadcrumb --}}
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="{{ route('admin.ppdb.index') }}">PPDB</a></li>
      <li class="breadcrumb-item active" aria-current="page">{{ $ppdb->full_name }}</li>
    </ol>
  </nav>

  @if (session('success'))
    <div class="alert alert-success d-flex justify-content-between align-items-center">
      <div><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
    </div>
  @endif

  @if (session('activation_link'))
    <div class="alert alert-info">
      <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center">
        <div class="fw-semibold">
          <i class="bi bi-link-45deg me-1"></i> Activation link berhasil dibuat (sekali pakai)
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
      <div class="small text-muted mt-2">Kirim link ini ke user via email/WA. Setelah dipakai, token otomatis tidak berlaku.</div>

      <div class="mt-2">
        <code class="d-block p-2 bg-light rounded" id="activationLinkText">{{ session('activation_link') }}</code>
      </div>
    </div>

    @push('scripts')
      <script>
        document.getElementById('btnCopyActivationLink')?.addEventListener('click', async () => {
          const el = document.getElementById('activationLinkText');
          const text = el?.innerText?.trim() || '';
          if (!text) return;
          try {
            await navigator.clipboard.writeText(text);
          } catch (e) {
            // fallback (masih manusia 2026 yang browsernya aneh)
            const ta = document.createElement('textarea');
            ta.value = text;
            document.body.appendChild(ta);
            ta.select();
            document.execCommand('copy');
            document.body.removeChild(ta);
          }
        });
      </script>
    @endpush
  @endif

  @php
    $badge = [
      'submitted' => 'bg-warning text-dark',
      'approved'  => 'bg-info text-dark',
      'activated' => 'bg-success',
      'rejected'  => 'bg-danger',
    ][$ppdb->status] ?? 'bg-secondary';
  @endphp

  {{-- Header + Actions --}}
  <div class="card mb-3">
    <div class="card-body d-flex flex-wrap gap-3 justify-content-between align-items-center">
      <div>
        <h5 class="mb-1">{{ $ppdb->full_name }}</h5>
        <div class="small text-muted">
          Email: {{ $ppdb->email }} • WA: {{ $ppdb->whatsapp }} • Kode: <span class="badge bg-light text-dark">{{ $ppdb->public_code }}</span>
        </div>
        <div class="small text-muted">
          Dibuat: {{ optional($ppdb->created_at)->format('Y-m-d H:i') }}
          @if($ppdb->verified_at)
            • Diverifikasi: {{ optional($ppdb->verified_at)->format('Y-m-d H:i') }} (by #{{ $ppdb->verified_by }})
          @endif
        </div>
      </div>

      <div class="d-flex flex-wrap align-items-center gap-2">
        <span class="me-1">Status:</span>
        <span class="badge {{ $badge }}">{{ ucfirst($ppdb->status) }}</span>

        {{-- Manual change status (optional) --}}
        <form action="{{ route('admin.ppdb.updateStatus', $ppdb->id) }}" method="POST" class="d-flex gap-2 ms-2">
          @csrf
          @method('PATCH')
          <select name="status" class="form-select form-select-sm" style="min-width: 160px;">
            @foreach (['submitted','approved','activated','rejected'] as $st)
              <option value="{{ $st }}" @selected($ppdb->status===$st)>{{ ucfirst($st) }}</option>
            @endforeach
          </select>
          <button class="btn btn-sm btn-primary">
            <i class="bi bi-check2-circle"></i> Simpan
          </button>
        </form>
      </div>
    </div>
  </div>

  <div class="row g-3">
    {{-- Data Ringkas --}}
    <div class="col-lg-7">
      <div class="card h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h6 class="mb-0">Data Pendaftar</h6>
          <span class="small text-muted">PPDB Application</span>
        </div>
        <div class="card-body">
          <div class="row g-2">
            <div class="col-md-6">
              <div class="border rounded p-2 h-100">
                <div class="small text-muted">Nama Lengkap</div>
                <div class="fw-semibold">{{ $ppdb->full_name }}</div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="border rounded p-2 h-100">
                <div class="small text-muted">Email</div>
                <div>{{ $ppdb->email }}</div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="border rounded p-2 h-100">
                <div class="small text-muted">WhatsApp</div>
                <div>{{ $ppdb->whatsapp }}</div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="border rounded p-2 h-100">
                <div class="small text-muted">Kode Publik</div>
                <div><span class="badge bg-light text-dark">{{ $ppdb->public_code }}</span></div>
              </div>
            </div>
            <div class="col-md-12">
              <div class="border rounded p-2 h-100">
                <div class="small text-muted">Status</div>
                <div><span class="badge {{ $badge }}">{{ ucfirst($ppdb->status) }}</span></div>
              </div>
            </div>
          </div>

          <hr class="my-3">

          {{-- Approve / Reject actions --}}
          <div class="d-flex flex-wrap gap-2">
            @if(in_array($ppdb->status, ['submitted','rejected'], true))
              <form action="{{ route('admin.ppdb.approve', $ppdb->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <button class="btn btn-success">
                  <i class="bi bi-check-circle"></i> Approve & Buat Link Aktivasi
                </button>
              </form>

              <button class="btn btn-danger" type="button" data-bs-toggle="modal" data-bs-target="#modalReject">
                <i class="bi bi-x-circle"></i> Reject
              </button>
            @elseif($ppdb->status === 'approved')
              <form action="{{ route('admin.ppdb.approve', $ppdb->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <button class="btn btn-outline-success">
                  <i class="bi bi-arrow-repeat"></i> Regenerate Link Aktivasi
                </button>
              </form>
              <span class="small text-muted align-self-center">Gunakan kalau link lama hilang. Token lama otomatis di-nonaktifkan.</span>
            @elseif($ppdb->status === 'activated')
              <span class="badge bg-success align-self-center">Sudah aktivasi (akun user sudah dibuat)</span>
              @if($ppdb->user_id)
                <span class="small text-muted align-self-center">User ID: {{ $ppdb->user_id }}</span>
              @endif
            @endif
          </div>

          <div class="small text-muted mt-2">
            Approve akan menghasilkan link aktivasi sekali pakai (token hashed di DB). Kirim manual ke user via email/WA sesuai SOP kamu.
          </div>
        </div>
      </div>
    </div>

    {{-- Berkas + Preview --}}
    <div class="col-lg-5">
      <div class="card h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h6 class="mb-0">Berkas</h6>
          <span class="small text-muted">Klik untuk pratinjau</span>
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
          <div class="small text-muted mt-2">Gambar dipratinjau langsung; PDF via viewer.</div>
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
            <textarea name="reason" class="form-control" rows="4" maxlength="2000" required
              placeholder="Jelaskan alasan penolakan (mis: bukti pembayaran tidak valid, data tidak lengkap, dll)"></textarea>

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

  @push('scripts')
  <script>
    document.getElementById('modalPreview')?.addEventListener('show.bs.modal', function (ev) {
      const trigger = ev.relatedTarget;
      const url   = trigger?.getAttribute('data-fileurl');
      const label = trigger?.getAttribute('data-filelabel');

      const titleEl = document.getElementById('modalPreviewTitle');
      const cont    = document.getElementById('filePreviewContainer');
      const openBtn = document.getElementById('modalOpenNewTab');

      titleEl.textContent = 'Preview: ' + (label || 'Berkas');
      openBtn.href = url || '#';

      if (!url) { cont.innerHTML = '<div class="text-muted">Berkas tidak tersedia.</div>'; return; }

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
