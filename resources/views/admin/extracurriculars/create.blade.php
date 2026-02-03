<x-page.admin>
    <div class="page-heading">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
            <div>
                <h3 class="mb-1">Tambah Ekstrakurikuler</h3>
                <p class="text-muted mb-0">Buat item ekstrakurikuler baru (multi-bahasa).</p>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('admin.extracurriculars.index', request()->query()) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <button form="ekstraCreateForm" type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan
                </button>
            </div>
        </div>
    </div>

    <div class="page-content">
        <section class="section">

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    {{ $errors->first() }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @php
                $isPublishedChecked = (bool) old('is_published', 1);
            @endphp

            <form id="ekstraCreateForm" method="POST" action="{{ route('admin.extracurriculars.store') }}">
                @csrf

                <div class="row g-3">
                    <div class="col-12 col-lg-8">
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="fw-semibold">
                                    <i class="bi bi-translate me-1"></i> Nama Multi-bahasa
                                </div>
                                <div class="small text-muted">Isi minimal Bahasa Indonesia.</div>
                            </div>

                            <div class="card-body">
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
                                    <div class="tab-pane fade show active" id="pane-id" role="tabpanel" aria-labelledby="tab-id">
                                        <div class="mb-0">
                                            <label class="form-label">Nama (ID) <span class="text-danger">*</span></label>
                                            <input type="text"
                                                   name="name_id"
                                                   class="form-control @error('name_id') is-invalid @enderror"
                                                   value="{{ old('name_id') }}"
                                                   placeholder="Contoh: Pramuka">
                                            @error('name_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="pane-en" role="tabpanel" aria-labelledby="tab-en">
                                        <div class="mb-0">
                                            <label class="form-label">Name (EN)</label>
                                            <input type="text"
                                                   name="name_en"
                                                   class="form-control @error('name_en') is-invalid @enderror"
                                                   value="{{ old('name_en') }}"
                                                   placeholder="Optional">
                                            @error('name_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="pane-ar" role="tabpanel" aria-labelledby="tab-ar">
                                        <div class="mb-0">
                                            <label class="form-label">الاسم (AR)</label>
                                            <input type="text"
                                                   name="name_ar"
                                                   dir="rtl"
                                                   class="form-control @error('name_ar') is-invalid @enderror"
                                                   value="{{ old('name_ar') }}"
                                                   placeholder="اختياري">
                                            @error('name_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-4">
                        <div class="card">
                            <div class="card-header fw-semibold">
                                <i class="bi bi-sliders me-1"></i> Pengaturan
                            </div>

                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Urutan (Sort Order)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-sort-numeric-down"></i></span>
                                        <input type="number"
                                               min="0"
                                               name="sort_order"
                                               class="form-control @error('sort_order') is-invalid @enderror"
                                               value="{{ old('sort_order', 0) }}">
                                        @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           role="switch"
                                           id="isPublished"
                                           name="is_published"
                                           value="1"
                                           {{ $isPublishedChecked ? 'checked' : '' }}>
                                    <label class="form-check-label" for="isPublished">Published</label>
                                </div>
                                <div class="form-text">Kalau OFF, tersimpan sebagai Draft.</div>
                            </div>
                        </div>
                    </div>
                </div>

            </form>

        </section>
    </div>
</x-page.admin>
