<x-page.admin>
    @php
        // Resolve existing image URL (tanpa "use")
        $path = (string) ($image->image_path ?? '');

        if ($path === '') {
            $existingUrl = 'https://placehold.co/800x800?text=Preview';
        } elseif (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://'])) {
            $existingUrl = $path;
        } elseif (\Illuminate\Support\Str::startsWith($path, ['storage/', 'gallery/'])) {
            $existingUrl = \Illuminate\Support\Facades\Storage::url($path);
        } else {
            $existingUrl = asset($path);
        }

        $publishedVal = (int) old('is_published', (int) $image->is_published);

        $publishedAtVal = old('published_at');
        if ($publishedAtVal === null) {
            $publishedAtVal = $image->published_at ? $image->published_at->format('Y-m-d\TH:i') : '';
        }
    @endphp

    <div class="page-heading">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
            <div>
                <h3 class="mb-1">Edit Galeri</h3>
                <p class="text-muted mb-0">Perbarui data gambar galeri sekolah.</p>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('admin.gallery.index', request()->query()) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>

                <button form="galleryEditForm" type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan Perubahan
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

            <form id="galleryEditForm"
                  action="{{ route('admin.gallery.update', $image->id) }}"
                  method="POST"
                  enctype="multipart/form-data">
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
                                            <label class="form-label">Judul (ID) <span class="text-danger">*</span></label>
                                            <input type="text"
                                                   name="title_id"
                                                   class="form-control @error('title_id') is-invalid @enderror"
                                                   value="{{ old('title_id', $image->title_id) }}"
                                                   required>
                                            @error('title_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="mb-0">
                                            <label class="form-label">Deskripsi (ID)</label>
                                            <textarea name="description_id"
                                                      rows="4"
                                                      class="form-control @error('description_id') is-invalid @enderror">{{ old('description_id', $image->description_id) }}</textarea>
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
                                                   value="{{ old('title_en', $image->title_en) }}">
                                            @error('title_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="mb-0">
                                            <label class="form-label">Description (EN)</label>
                                            <textarea name="description_en"
                                                      rows="4"
                                                      class="form-control @error('description_en') is-invalid @enderror">{{ old('description_en', $image->description_en) }}</textarea>
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
                                                   value="{{ old('title_ar', $image->title_ar) }}">
                                            @error('title_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="mb-0">
                                            <label class="form-label">الوصف (AR)</label>
                                            <textarea name="description_ar"
                                                      rows="4"
                                                      dir="rtl"
                                                      class="form-control @error('description_ar') is-invalid @enderror">{{ old('description_ar', $image->description_ar) }}</textarea>
                                            @error('description_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">Link (opsional)</label>
                                        <input type="url"
                                               name="link_url"
                                               class="form-control @error('link_url') is-invalid @enderror"
                                               value="{{ old('link_url', $image->link_url) }}"
                                               placeholder="https://...">
                                        @error('link_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- RIGHT --}}
                    <div class="col-12 col-lg-4">
                        <div class="card mb-3">
                            <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
                                <div><i class="bi bi-image me-1"></i> Gambar</div>
                                <span class="badge bg-light-secondary text-secondary">#{{ $image->id }}</span>
                            </div>

                            <div class="card-body">
                                <div class="ratio ratio-1x1 rounded overflow-hidden border mb-3 bg-body-tertiary">
                                    <img id="imgPreview"
                                         src="{{ $existingUrl }}"
                                         alt="{{ $image->title_id }}"
                                         style="object-fit: cover;">
                                </div>

                                <label class="form-label">Ganti Gambar (opsional)</label>
                                <input type="file"
                                       name="image_file"
                                       accept="image/*"
                                       class="form-control @error('image_file') is-invalid @enderror"
                                       onchange="previewImage(event)">
                                @error('image_file') <div class="invalid-feedback">{{ $message }}</div> @enderror

                                <div class="form-text mt-2">Kosongkan jika tidak ingin mengganti gambar.</div>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header fw-semibold">
                                <i class="bi bi-broadcast me-1"></i> Publikasi
                            </div>

                            <div class="card-body">
                                <label class="form-label d-block">Status Publikasi</label>

                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="is_published" id="status_draft" value="0" autocomplete="off"
                                           {{ $publishedVal === 0 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-secondary" for="status_draft">
                                        <i class="bi bi-file-earmark-text me-1"></i> Draft
                                    </label>

                                    <input type="radio" class="btn-check" name="is_published" id="status_publish" value="1" autocomplete="off"
                                           {{ $publishedVal === 1 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-success" for="status_publish">
                                        <i class="bi bi-upload me-1"></i> Publish
                                    </label>
                                </div>

                                @error('is_published')<div class="invalid-feedback d-block mt-2">{{ $message }}</div>@enderror

                                <div class="mt-3" id="publishAtGroup">
                                    <label class="form-label">Jadwal terbit (opsional)</label>
                                    <input type="datetime-local"
                                           name="published_at"
                                           id="publishedAt"
                                           class="form-control @error('published_at') is-invalid @enderror"
                                           value="{{ $publishedAtVal }}">
                                    @error('published_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header fw-semibold">
                                <i class="bi bi-sliders me-1"></i> Meta
                            </div>
                            <div class="card-body">
                                <label class="form-label">Sort Order</label>
                                <input type="number"
                                       name="sort_order"
                                       class="form-control @error('sort_order') is-invalid @enderror"
                                       value="{{ old('sort_order', $image->sort_order) }}"
                                       min="0">
                                @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="form-text">Semakin kecil → semakin atas.</div>
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
            document.getElementById('imgPreview').src = URL.createObjectURL(file);
        }

        document.addEventListener('DOMContentLoaded', () => {
            const radioPublish = document.getElementById('status_publish');
            const publishAtGroup = document.getElementById('publishAtGroup');
            const publishedAt = document.getElementById('publishedAt');

            function syncPublishAt() {
                const isPublish = radioPublish && radioPublish.checked;
                if (publishAtGroup) publishAtGroup.style.display = isPublish ? '' : 'none';
                if (!isPublish && publishedAt) publishedAt.value = '';
            }

            document.getElementById('status_draft')?.addEventListener('change', syncPublishAt);
            document.getElementById('status_publish')?.addEventListener('change', syncPublishAt);
            syncPublishAt();
        });
    </script>
</x-page.admin>
