<x-page.admin>
    @php
        $backUrl = route('admin.announcements.index', request()->query());

        $isPublishedChecked = (bool) old('is_published', 1);

        // datetime-local expects: YYYY-MM-DDTHH:MM
        $publishedAtOld = old('published_at');
        $publishedAtValue = $publishedAtOld !== null ? $publishedAtOld : '';

        $eventDateValue = old('event_date', now()->toDateString());
        $sortOrderValue = old('sort_order', 0);
    @endphp

    <div class="page-heading">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
            <div>
                <h3 class="mb-1">Tambah Pengumuman</h3>
                <p class="text-muted mb-0">Buat pengumuman baru dengan judul/deskripsi multi-bahasa.</p>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ $backUrl }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <button form="announcementCreateForm" type="submit" class="btn btn-primary">
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

            <form id="announcementCreateForm" method="POST" action="{{ route('admin.announcements.store') }}">
                @csrf

                <div class="row g-3">
                    {{-- LEFT --}}
                    <div class="col-12 col-lg-8">
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="fw-semibold">
                                    <i class="bi bi-translate me-1"></i> Konten Multi-bahasa
                                </div>
                                <div class="small text-muted">Isi minimal Bahasa Indonesia.</div>
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
                                                   placeholder="Contoh: Pengumuman Libur Sekolah">
                                            @error('title_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="mb-0">
                                            <label class="form-label">Deskripsi (ID)</label>
                                            <textarea name="desc_id"
                                                      rows="5"
                                                      class="form-control @error('desc_id') is-invalid @enderror"
                                                      placeholder="Tulis deskripsi pengumuman...">{{ old('desc_id') }}</textarea>
                                            @error('desc_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                                            <textarea name="desc_en"
                                                      rows="5"
                                                      class="form-control @error('desc_en') is-invalid @enderror"
                                                      placeholder="Optional English description...">{{ old('desc_en') }}</textarea>
                                            @error('desc_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                                            <textarea name="desc_ar"
                                                      rows="5"
                                                      dir="rtl"
                                                      class="form-control @error('desc_ar') is-invalid @enderror"
                                                      placeholder="اختياري...">{{ old('desc_ar') }}</textarea>
                                            @error('desc_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <div class="row g-3">
                                    <div class="col-12 col-lg-6">
                                        <label class="form-label">Tanggal Pengumuman <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                                            <input type="date"
                                                   name="event_date"
                                                   class="form-control @error('event_date') is-invalid @enderror"
                                                   value="{{ $eventDateValue }}">
                                            @error('event_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="form-text">Tanggal efektif / hari H pengumuman.</div>
                                    </div>

                                    <div class="col-12 col-lg-6">
                                        <label class="form-label">Urutan (Sort Order)</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-sort-numeric-down"></i></span>
                                            <input type="number"
                                                   min="0"
                                                   name="sort_order"
                                                   class="form-control @error('sort_order') is-invalid @enderror"
                                                   value="{{ $sortOrderValue }}"
                                                   placeholder="0">
                                            @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="form-text">Semakin kecil, semakin di atas (jika tanggal sama).</div>
                                    </div>

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

                    {{-- RIGHT --}}
                    <div class="col-12 col-lg-4">
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
                                           {{ $isPublishedChecked ? 'checked' : '' }}
                                           onchange="togglePublishedAt()">
                                    <label class="form-check-label" for="isPublished">
                                        Published
                                    </label>
                                    <div class="form-text">Kalau OFF, pengumuman tersimpan sebagai Draft.</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Jadwal terbit (opsional)</label>
                                    <input type="datetime-local"
                                           id="publishedAt"
                                           name="published_at"
                                           class="form-control @error('published_at') is-invalid @enderror"
                                           value="{{ $publishedAtValue }}"
                                           {{ $isPublishedChecked ? '' : 'disabled' }}>
                                    @error('published_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <div class="form-text">Aktif jika Published ON. Kosongkan kalau terbit sekarang.</div>
                                </div>

                                <div class="small text-muted">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Jika Published ON dan jadwal kosong, sistem akan set <code>published_at</code> ke sekarang.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </form>

        </section>
    </div>

    <script>
        function togglePublishedAt() {
            const isPublished = document.getElementById('isPublished');
            const publishedAt = document.getElementById('publishedAt');
            if (!isPublished || !publishedAt) return;

            publishedAt.disabled = !isPublished.checked;

            if (!isPublished.checked) {
                publishedAt.value = '';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            togglePublishedAt();
        });
    </script>
</x-page.admin>
