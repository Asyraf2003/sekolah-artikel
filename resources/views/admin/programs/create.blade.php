<x-page.admin>
    <div class="page-heading">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
            <div>
                <h3 class="mb-1">Tambah Gambar</h3>
                <p class="text-muted mb-0">Buat item galeri baru dengan judul/deskripsi multi-bahasa.</p>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('admin.gallery.index', request()->query()) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <button form="galleryCreateForm" type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan
                </button>
            </div>
        </div>
    </div>

    <div class="page-content">
        <section class="section">

            {{-- Alerts --}}
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    {{ $errors->first() }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form id="galleryCreateForm" method="POST" action="{{ route('admin.gallery.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="row g-3">
                    {{-- LEFT: Main content --}}
                    <div class="col-12 col-lg-8">
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="fw-semibold">
                                    <i class="bi bi-translate me-1"></i> Konten Multi-bahasa
                                </div>
                                <div class="small text-muted">
                                    Isi minimal Bahasa Indonesia.
                                </div>
                            </div>

                            <div class="card-body">
                                {{-- Tabs --}}
                                <ul class="nav nav-tabs" id="langTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="tab-id" data-bs-toggle="tab" data-bs-target="#pane-id" type="button" role="tab">
                                            ID
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="tab-en" data-bs-toggle="tab" data-bs-target="#pane-en" type="button" role="tab">
                                            EN
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="tab-ar" data-bs-toggle="tab" data-bs-target="#pane-ar" type="button" role="tab">
                                            AR
                                        </button>
                                    </li>
                                </ul>

                                <div class="tab-content pt-3">
                                    {{-- ID --}}
                                    <div class="tab-pane fade show active" id="pane-id" role="tabpanel" aria-labelledby="tab-id">
                                        <div class="mb-3">
                                            <label class="form-label">Judul (ID) <span class="text-danger">*</span></label>
                                            <input type="text"
                                                   name="title_id"
                                                   class="form-control @error('title_id') is-invalid @enderror"
                                                   value="{{ old('title_id') }}"
                                                   placeholder="Contoh: Kegiatan Pelatihan">
                                            @error('title_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="mb-0">
                                            <label class="form-label">Deskripsi (ID)</label>
                                            <textarea name="description_id"
                                                      rows="4"
                                                      class="form-control @error('description_id') is-invalid @enderror"
                                                      placeholder="Tulis deskripsi singkat...">{{ old('description_id') }}</textarea>
                                            @error('description_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    {{-- EN --}}
                                    <div class="tab-pane fade" id="pane-en" role="tabpanel" aria-labelledby="tab-en">
                                        <div class="mb-3">
                                            <label class="form-label">Title (EN)</label>
                                            <input type="text"
                                                   name="title_en"
                                                   class="form-control @error('title_en') is-invalid @enderror"
                                                   value="{{ old('title_en') }}"
                                                   placeholder="Optional English title">
                                            @error('title_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="mb-0">
                                            <label class="form-label">Description (EN)</label>
                                            <textarea name="description_en"
                                                      rows="4"
                                                      class="form-control @error('description_en') is-invalid @enderror"
                                                      placeholder="Optional English description...">{{ old('description_en') }}</textarea>
                                            @error('description_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    {{-- AR --}}
                                    <div class="tab-pane fade" id="pane-ar" role="tabpanel" aria-labelledby="tab-ar">
                                        <div class="mb-3">
                                            <label class="form-label">العنوان (AR)</label>
                                            <input type="text"
                                                   name="title_ar"
                                                   dir="rtl"
                                                   class="form-control @error('title_ar') is-invalid @enderror"
                                                   value="{{ old('title_ar') }}"
                                                   placeholder="اختياري">
                                            @error('title_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="mb-0">
                                            <label class="form-label">الوصف (AR)</label>
                                            <textarea name="description_ar"
                                                      rows="4"
                                                      dir="rtl"
                                                      class="form-control @error('description_ar') is-invalid @enderror"
                                                      placeholder="اختياري...">{{ old('description_ar') }}</textarea>
                                            @error('description_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">Link (opsional)</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
                                            <input type="url"
                                                   name="link_url"
                                                   class="form-control @error('link_url') is-invalid @enderror"
                                                   value="{{ old('link_url') }}"
                                                   placeholder="https://...">
                                            @error('link_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="form-text">Link tujuan saat tombol “Kunjungi” ditekan.</div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- RIGHT: Media & Publish settings --}}
                    <div class="col-12 col-lg-4">
                        {{-- Image card --}}
                        <div class="card mb-3">
                            <div class="card-header fw-semibold">
                                <i class="bi bi-image me-1"></i> Gambar
                            </div>

                            <div class="card-body">
                                <div class="ratio ratio-1x1 rounded overflow-hidden border mb-3 bg-body-tertiary">
                                    <img id="imgPreview"
                                         src="https://placehold.co/800x800?text=Preview"
                                         alt="Preview"
                                         style="object-fit: cover;">
                                </div>

                                <label class="form-label">Upload file <span class="text-danger">*</span></label>
                                <input type="file" name="image_file" accept="image/*"
                                      class="form-control @error('image_file') is-invalid @enderror"
                                      onchange="previewImage(event)">
                                @error('image_file') <div class="invalid-feedback">{{ $message }}</div> @enderror

                                <div class="form-text mt-2">
                                    Rekomendasi: JPG/PNG, ukuran wajar (tidak perlu 40MB ya).
                                </div>
                            </div>
                        </div>

                        {{-- Publish card --}}
                        <div class="card">
                            <div class="card-header fw-semibold">
                                <i class="bi bi-broadcast me-1"></i> Publikasi
                            </div>

                            <div class="card-body">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           role="switch"
                                           id="isPublished"
                                           name="is_published"
                                           value="1"
                                           {{ old('is_published') ? 'checked' : '' }}
                                           onchange="togglePublishedAt()">
                                    <label class="form-check-label" for="isPublished">
                                        Published
                                    </label>
                                    <div class="form-text">Kalau off, item tersimpan sebagai Draft.</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Jadwal terbit (opsional)</label>
                                    <input type="datetime-local"
                                           id="publishedAt"
                                           name="published_at"
                                           class="form-control @error('published_at') is-invalid @enderror"
                                           value="{{ old('published_at') }}"
                                           disabled>
                                    @error('published_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <div class="form-text">Aktif jika Published ON. Kosongkan kalau terbit sekarang.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </form>

        </section>
    </div>

    <script>
        function previewImage(event) {
            const file = event.target.files && event.target.files[0];
            if (!file) return;

            const img = document.getElementById('imgPreview');
            img.src = URL.createObjectURL(file);
        }

        function togglePublishedAt() {
            const isPublished = document.getElementById('isPublished');
            const publishedAt = document.getElementById('publishedAt');

            publishedAt.disabled = !isPublished.checked;

            // kalau user matiin publish, kosongin jadwal biar ga ke-submit value nyangkut
            if (!isPublished.checked) {
                publishedAt.value = '';
            }
        }

        // init state on load
        document.addEventListener('DOMContentLoaded', () => {
            togglePublishedAt();
        });
    </script>
</x-page.admin>
