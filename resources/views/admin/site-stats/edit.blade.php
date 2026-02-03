<x-page.admin>
    @php
        $backUrl = route('admin.site-stats.index');

        $labelId = old('label_id', $siteStat->label_id);
        $labelEn = old('label_en', $siteStat->label_en);
        $labelAr = old('label_ar', $siteStat->label_ar);

        $descId = old('desc_id', $siteStat->desc_id);
        $descEn = old('desc_en', $siteStat->desc_en);
        $descAr = old('desc_ar', $siteStat->desc_ar);

        $value = old('value', $siteStat->value);
        $sortOrder = old('sort_order', $siteStat->sort_order);

        $isActiveChecked = (bool) old('is_active', $siteStat->is_active);

        $title = $siteStat->label_id ?: ('Slot #' . $siteStat->slot);
    @endphp

    <div class="page-heading">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
            <div>
                <h3 class="mb-1">Edit Statistik</h3>
                <p class="text-muted mb-0">
                    Slot <span class="fw-semibold">#{{ $siteStat->slot }}</span>
                    <span class="text-muted">• ID {{ $siteStat->id }}</span>
                    <span class="ms-2">({{ $title }})</span>
                </p>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ $backUrl }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <button form="siteStatEditForm" type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan
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

            <form id="siteStatEditForm"
                  method="POST"
                  action="{{ route('admin.site-stats.update', $siteStat->id) }}">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    {{-- LEFT --}}
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
                                            <label class="form-label">Label (ID)</label>
                                            <input type="text"
                                                   name="label_id"
                                                   class="form-control @error('label_id') is-invalid @enderror"
                                                   value="{{ $labelId }}"
                                                   placeholder="Contoh: Siswa / Jam / Program">
                                            @error('label_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="mb-0">
                                            <label class="form-label">Deskripsi (ID)</label>
                                            <textarea name="desc_id"
                                                      rows="4"
                                                      class="form-control @error('desc_id') is-invalid @enderror"
                                                      placeholder="Contoh: Yang telah lulus">{{ $descId }}</textarea>
                                            @error('desc_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    {{-- EN --}}
                                    <div class="tab-pane fade" id="pane-en" role="tabpanel" aria-labelledby="tab-en">
                                        <div class="mb-3">
                                            <label class="form-label">Label (EN)</label>
                                            <input type="text"
                                                   name="label_en"
                                                   class="form-control @error('label_en') is-invalid @enderror"
                                                   value="{{ $labelEn }}"
                                                   placeholder="Optional">
                                            @error('label_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="mb-0">
                                            <label class="form-label">Description (EN)</label>
                                            <textarea name="desc_en"
                                                      rows="4"
                                                      class="form-control @error('desc_en') is-invalid @enderror"
                                                      placeholder="Optional">{{ $descEn }}</textarea>
                                            @error('desc_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    {{-- AR --}}
                                    <div class="tab-pane fade" id="pane-ar" role="tabpanel" aria-labelledby="tab-ar">
                                        <div class="mb-3">
                                            <label class="form-label">العنوان (AR)</label>
                                            <input type="text"
                                                   name="label_ar"
                                                   dir="rtl"
                                                   class="form-control @error('label_ar') is-invalid @enderror"
                                                   value="{{ $labelAr }}"
                                                   placeholder="اختياري">
                                            @error('label_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="mb-0">
                                            <label class="form-label">الوصف (AR)</label>
                                            <textarea name="desc_ar"
                                                      rows="4"
                                                      dir="rtl"
                                                      class="form-control @error('desc_ar') is-invalid @enderror"
                                                      placeholder="اختياري">{{ $descAr }}</textarea>
                                            @error('desc_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- RIGHT --}}
                    <div class="col-12 col-lg-4">
                        <div class="card">
                            <div class="card-header fw-semibold">
                                <i class="bi bi-sliders me-1"></i> Pengaturan
                            </div>

                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Value (Angka) <span class="text-danger">*</span></label>
                                    <input type="number"
                                           min="0"
                                           name="value"
                                           class="form-control @error('value') is-invalid @enderror"
                                           value="{{ $value }}">
                                    @error('value') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <div class="form-text">Angka utama yang ditampilkan.</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Urutan (Sort Order)</label>
                                    <input type="number"
                                           min="0"
                                           max="255"
                                           name="sort_order"
                                           class="form-control @error('sort_order') is-invalid @enderror"
                                           value="{{ $sortOrder }}">
                                    @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <div class="form-text">Semakin kecil, semakin dulu tampil.</div>
                                </div>

                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           role="switch"
                                           id="isActive"
                                           name="is_active"
                                           value="1"
                                           {{ $isActiveChecked ? 'checked' : '' }}>
                                    <label class="form-check-label" for="isActive">Tampilkan di publik</label>
                                </div>

                                <div class="small text-muted mt-3">
                                    <div class="mb-1">
                                        <i class="bi bi-clock me-1"></i>
                                        Dibuat: <strong>{{ $siteStat->created_at?->format('d M Y H:i') ?? '—' }}</strong>
                                    </div>
                                    <div class="mb-0">
                                        <i class="bi bi-arrow-repeat me-1"></i>
                                        Update: <strong>{{ $siteStat->updated_at?->format('d M Y H:i') ?? '—' }}</strong>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </form>

        </section>
    </div>
</x-page.admin>
