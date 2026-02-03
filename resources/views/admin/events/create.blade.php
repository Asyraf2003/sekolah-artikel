<x-page.admin>
    @php
        $isPublishedChecked = (bool) old('is_published', 1);
        $eventDateValue = old('event_date', now()->addHour()->format('Y-m-d\TH:i'));
    @endphp

    <div class="page-heading">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
            <div>
                <h3 class="mb-1">Tambah Event</h3>
                <p class="text-muted mb-0">Buat agenda baru (multi-bahasa).</p>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('admin.events.index', request()->query()) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <button form="eventCreateForm" type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan
                </button>
            </div>
        </div>
    </div>

    <div class="page-content">
        <section class="section">

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-1"></i>{{ $errors->first() }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form id="eventCreateForm" method="POST" action="{{ route('admin.events.store') }}">
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
                                <ul class="nav nav-tabs" id="langTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="tab-id" data-bs-toggle="tab" data-bs-target="#pane-id" type="button" role="tab">ID</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="tab-en" data-bs-toggle="tab" data-bs-target="#pane-en" type="button" role="tab">EN</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="tab-ar" data-bs-toggle="tab" data-bs-target="#pane-ar" type="button" role="tab">AR</button>
                                    </li>
                                </ul>

                                <div class="tab-content pt-3">
                                    <div class="tab-pane fade show active" id="pane-id" role="tabpanel" aria-labelledby="tab-id">
                                        <div class="mb-3">
                                            <label class="form-label">Judul (ID) <span class="text-danger">*</span></label>
                                            <input type="text" name="title_id"
                                                   class="form-control @error('title_id') is-invalid @enderror"
                                                   value="{{ old('title_id') }}">
                                            @error('title_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="mb-0">
                                            <label class="form-label">Lokasi (ID)</label>
                                            <input type="text" name="place_id"
                                                   class="form-control @error('place_id') is-invalid @enderror"
                                                   value="{{ old('place_id') }}">
                                            @error('place_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="pane-en" role="tabpanel" aria-labelledby="tab-en">
                                        <div class="mb-3">
                                            <label class="form-label">Title (EN)</label>
                                            <input type="text" name="title_en"
                                                   class="form-control @error('title_en') is-invalid @enderror"
                                                   value="{{ old('title_en') }}">
                                            @error('title_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="mb-0">
                                            <label class="form-label">Place (EN)</label>
                                            <input type="text" name="place_en"
                                                   class="form-control @error('place_en') is-invalid @enderror"
                                                   value="{{ old('place_en') }}">
                                            @error('place_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="pane-ar" role="tabpanel" aria-labelledby="tab-ar">
                                        <div class="mb-3">
                                            <label class="form-label">العنوان (AR)</label>
                                            <input type="text" name="title_ar" dir="rtl"
                                                   class="form-control @error('title_ar') is-invalid @enderror"
                                                   value="{{ old('title_ar') }}">
                                            @error('title_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="mb-0">
                                            <label class="form-label">المكان (AR)</label>
                                            <input type="text" name="place_ar" dir="rtl"
                                                   class="form-control @error('place_ar') is-invalid @enderror"
                                                   value="{{ old('place_ar') }}">
                                            @error('place_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <div class="row g-3">
                                    <div class="col-12 col-lg-6">
                                        <label class="form-label">Tanggal & Waktu <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                                            <input type="datetime-local" name="event_date"
                                                   class="form-control @error('event_date') is-invalid @enderror"
                                                   value="{{ $eventDateValue }}">
                                            @error('event_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="form-text">Gunakan waktu lokal server/app.</div>
                                    </div>

                                    <div class="col-12 col-lg-6">
                                        <label class="form-label">Urutan (Sort Order)</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-sort-numeric-down"></i></span>
                                            <input type="number" min="0" name="sort_order"
                                                   class="form-control @error('sort_order') is-invalid @enderror"
                                                   value="{{ old('sort_order', 0) }}">
                                            @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Link detail (opsional)</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
                                            <input type="url" name="link_url"
                                                   class="form-control @error('link_url') is-invalid @enderror"
                                                   value="{{ old('link_url') }}"
                                                   placeholder="https://...">
                                            @error('link_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="form-text">Dipakai untuk tombol “Kunjungi”.</div>
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
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                           id="isPublished" name="is_published" value="1"
                                           {{ $isPublishedChecked ? 'checked' : '' }}>
                                    <label class="form-check-label" for="isPublished">Published</label>
                                </div>
                                <div class="form-text">Kalau OFF, event tersimpan sebagai Draft.</div>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </section>
    </div>
</x-page.admin>
