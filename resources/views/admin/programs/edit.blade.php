<x-page.admin>
    @php
        $backUrl = route('admin.programs.index', request()->query());
        $isPublishedChecked = (bool) old('is_published', $program->is_published);
        $title = $program->title_id ?: ($program->title_en ?: ($program->title_ar ?: '—'));
        $isTrashed = method_exists($program, 'trashed') && $program->trashed();
    @endphp

    <div class="page-heading">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
            <div>
                <h3 class="mb-1">Edit Program</h3>
                <p class="text-muted mb-0">
                    Perbarui program: <span class="fw-semibold">{{ $title }}</span>
                    <span class="text-muted">#{{ $program->id }}</span>
                    @if($isTrashed)
                        <span class="badge bg-secondary ms-2">Deleted</span>
                    @endif
                </p>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ $backUrl }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>

                @if($isTrashed && \Illuminate\Support\Facades\Route::has('admin.programs.restore'))
                    <form action="{{ route('admin.programs.restore', $program->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-outline-success"
                                onclick="return confirm('Pulihkan program ini?')">
                            <i class="bi bi-arrow-counterclockwise"></i> Restore
                        </button>
                    </form>
                @endif

                <button form="programEditForm" type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan
                </button>
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

            <form id="programEditForm" method="POST" action="{{ route('admin.programs.update', $program->id) }}">
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
                                        <div class="mb-3">
                                            <label class="form-label">Judul (ID) <span class="text-danger">*</span></label>
                                            <input type="text"
                                                   name="title_id"
                                                   class="form-control @error('title_id') is-invalid @enderror"
                                                   value="{{ old('title_id', $program->title_id) }}">
                                            @error('title_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="mb-0">
                                            <label class="form-label">Deskripsi (ID)</label>
                                            <textarea name="desc_id"
                                                      rows="5"
                                                      class="form-control @error('desc_id') is-invalid @enderror">{{ old('desc_id', $program->desc_id) }}</textarea>
                                            @error('desc_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="pane-en" role="tabpanel" aria-labelledby="tab-en">
                                        <div class="mb-3">
                                            <label class="form-label">Title (EN)</label>
                                            <input type="text"
                                                   name="title_en"
                                                   class="form-control @error('title_en') is-invalid @enderror"
                                                   value="{{ old('title_en', $program->title_en) }}">
                                            @error('title_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="mb-0">
                                            <label class="form-label">Description (EN)</label>
                                            <textarea name="desc_en"
                                                      rows="5"
                                                      class="form-control @error('desc_en') is-invalid @enderror">{{ old('desc_en', $program->desc_en) }}</textarea>
                                            @error('desc_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="pane-ar" role="tabpanel" aria-labelledby="tab-ar">
                                        <div class="mb-3">
                                            <label class="form-label">العنوان (AR)</label>
                                            <input type="text"
                                                   name="title_ar"
                                                   dir="rtl"
                                                   class="form-control @error('title_ar') is-invalid @enderror"
                                                   value="{{ old('title_ar', $program->title_ar) }}">
                                            @error('title_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="mb-0">
                                            <label class="form-label">الوصف (AR)</label>
                                            <textarea name="desc_ar"
                                                      rows="5"
                                                      dir="rtl"
                                                      class="form-control @error('desc_ar') is-invalid @enderror">{{ old('desc_ar', $program->desc_ar) }}</textarea>
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
                                    <label class="form-label">Urutan (Sort Order)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-sort-numeric-down"></i></span>
                                        <input type="number"
                                               min="0"
                                               name="sort_order"
                                               class="form-control @error('sort_order') is-invalid @enderror"
                                               value="{{ old('sort_order', $program->sort_order) }}">
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

                                <div class="small text-muted mt-3">
                                    <div class="mb-1">
                                        <i class="bi bi-clock me-1"></i>
                                        Dibuat: <strong>{{ $program->created_at?->format('d M Y H:i') ?? '—' }}</strong>
                                    </div>
                                    <div class="mb-0">
                                        <i class="bi bi-arrow-repeat me-1"></i>
                                        Update: <strong>{{ $program->updated_at?->format('d M Y H:i') ?? '—' }}</strong>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </form>

            {{-- Danger zone --}}
            <div class="row g-3">
                <div class="col-12 col-lg-8">
                    <div class="card border-danger mt-3">
                        <div class="card-header fw-semibold text-danger">
                            <i class="bi bi-exclamation-octagon me-1"></i> Danger Zone
                        </div>

                        <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <div class="text-muted">
                                @if(!$isTrashed)
                                    Hapus program ini (soft delete).
                                @else
                                    Program ini sudah dihapus (soft delete).
                                @endif
                            </div>

                            <div class="d-flex gap-2 flex-wrap">
                                @if(!$isTrashed)
                                    <form action="{{ route('admin.programs.destroy', $program->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Hapus program ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>
                                @else
                                    @if(\Illuminate\Support\Facades\Route::has('admin.programs.restore'))
                                        <form action="{{ route('admin.programs.restore', $program->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Pulihkan program ini?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-success">
                                                <i class="bi bi-arrow-counterclockwise"></i> Restore
                                            </button>
                                        </form>
                                    @endif

                                    @if(\Illuminate\Support\Facades\Route::has('admin.programs.forceDestroy'))
                                        <form action="{{ route('admin.programs.forceDestroy', $program->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Hapus permanen program ini? Ini tidak bisa dibatalkan.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger">
                                                <i class="bi bi-x-octagon"></i> Hapus Permanen
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </section>
    </div>
</x-page.admin>
