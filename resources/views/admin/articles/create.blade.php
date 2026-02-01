<x-page.admin>
    @php
        $backUrl = route('admin.articles.index', request()->query());
        $nowLocal = now()->format('Y-m-d\TH:i');
    @endphp

    <link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">

    <div class="page-heading">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
            <div>
                <h3 class="mb-1">Tambah Artikel</h3>
                <p class="text-muted mb-0">Buat artikel baru dengan editor Quill.</p>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ $backUrl }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <button form="articleCreateForm" type="submit" class="btn btn-primary">
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

            <form id="articleCreateForm"
                  method="POST"
                  action="{{ route('admin.articles.store') }}"
                  enctype="multipart/form-data">
                @csrf

                <div class="row g-3">
                    <div class="col-12 col-lg-8">

                        <div class="card mb-3">
                            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="fw-semibold">
                                    <i class="bi bi-translate me-1"></i> Judul & Ringkasan Multi-bahasa
                                </div>
                                <div class="small text-muted">Minimal isi Bahasa Indonesia.</div>
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
                                            <input type="text"
                                                   name="title_id"
                                                   id="titleId"
                                                   class="form-control @error('title_id') is-invalid @enderror"
                                                   value="{{ old('title_id') }}"
                                                   placeholder="Contoh: Kegiatan Pelatihan">
                                            @error('title_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            <div class="form-text">
                                                Slug dibuat otomatis dari judul (dan dibuat unik).
                                                <span class="ms-2">Preview: <code id="slugPreview">—</code></span>
                                            </div>
                                        </div>

                                        <div class="mb-0">
                                            <label class="form-label">Ringkasan (ID)</label>
                                            <textarea name="excerpt_id"
                                                      rows="4"
                                                      class="form-control @error('excerpt_id') is-invalid @enderror"
                                                      placeholder="Ringkasan singkat…">{{ old('excerpt_id') }}</textarea>
                                            @error('excerpt_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

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
                                            <label class="form-label">Excerpt (EN)</label>
                                            <textarea name="excerpt_en"
                                                      rows="4"
                                                      class="form-control @error('excerpt_en') is-invalid @enderror"
                                                      placeholder="Optional English excerpt…">{{ old('excerpt_en') }}</textarea>
                                            @error('excerpt_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

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
                                            <label class="form-label">ملخص (AR)</label>
                                            <textarea name="excerpt_ar"
                                                      rows="4"
                                                      dir="rtl"
                                                      class="form-control @error('excerpt_ar') is-invalid @enderror"
                                                      placeholder="اختياري...">{{ old('excerpt_ar') }}</textarea>
                                            @error('excerpt_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="fw-semibold">
                                    <i class="bi bi-pencil-square me-1"></i> Isi Artikel
                                </div>
                                <div class="small text-muted">Quill: header, quote, bold/italic/underline/strike, warna, list, link, image.</div>
                            </div>

                            <div class="card-body">
                                <div id="quillToolbar" class="mb-2"></div>
                                <div id="quillEditor" style="min-height: 320px;" class="bg-white"></div>

                                <input type="hidden" name="content_delta" id="contentDelta">
                                <input type="hidden" name="content_html" id="contentHtml">

                                @error('content_delta') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                                @error('content_html') <div class="text-danger small mt-2">{{ $message }}</div> @enderror

                                <div class="form-text mt-2">
                                    Gambar bisa lewat tombol image. Blueprint ini pakai upload endpoint (lebih waras daripada base64).
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-4">

                        <div class="card mb-3">
                            <div class="card-header fw-semibold">
                                <i class="bi bi-image me-1"></i> Cover
                            </div>

                            <div class="card-body">
                                <div class="ratio ratio-16x9 rounded overflow-hidden border mb-3 bg-body-tertiary">
                                    <img id="heroPreview"
                                         src="https://placehold.co/1200x675?text=Cover"
                                         alt="Cover"
                                         style="object-fit: cover;">
                                </div>

                                <label class="form-label">Upload cover</label>
                                <input type="file"
                                       name="hero_image"
                                       accept="image/*"
                                       class="form-control @error('hero_image') is-invalid @enderror"
                                       onchange="previewHero(event)">
                                @error('hero_image') <div class="invalid-feedback">{{ $message }}</div> @enderror

                                <div class="form-text mt-2">JPG/PNG, ukuran wajar.</div>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header fw-semibold">
                                <i class="bi bi-broadcast me-1"></i> Publikasi
                            </div>

                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" id="statusSelect" class="form-select @error('status') is-invalid @enderror" onchange="togglePublishTime()">
                                        <option value="draft" @selected(old('status','draft')==='draft')>Draft</option>
                                        <option value="published" @selected(old('status')==='published')>Published</option>
                                        <option value="archived" @selected(old('status')==='archived')>Archived</option>
                                    </select>
                                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <div class="form-text">
                                        Scheduled itu otomatis: status Published + published_at di masa depan.
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Waktu terbit</label>
                                    <input type="datetime-local"
                                           id="publishedAt"
                                           name="published_at"
                                           class="form-control @error('published_at') is-invalid @enderror"
                                           value="{{ old('published_at') }}"
                                           min="{{ $nowLocal }}">
                                    @error('published_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <div class="form-text">Aktif kalau status Published. Kosongkan untuk “terbit sekarang”.</div>
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           role="switch"
                                           id="isFeatured"
                                           name="is_featured"
                                           value="1"
                                           {{ old('is_featured') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="isFeatured">Featured</label>
                                </div>

                                <div class="mb-0">
                                    <label class="form-label">Pinned sampai</label>
                                    <input type="datetime-local"
                                           name="pinned_until"
                                           class="form-control @error('pinned_until') is-invalid @enderror"
                                           value="{{ old('pinned_until') }}">
                                    @error('pinned_until') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <div class="form-text">Opsional. Kalau diisi dan masih future, dianggap pinned.</div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header fw-semibold">
                                <i class="bi bi-tags me-1"></i> Kategori & Tag
                            </div>

                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Kategori</label>
                                    <div class="vstack gap-2" style="max-height: 180px; overflow:auto;">
                                        @foreach($categories ?? [] as $c)
                                            <label class="form-check">
                                                <input class="form-check-input"
                                                       type="checkbox"
                                                       name="category_ids[]"
                                                       value="{{ $c->id }}"
                                                       @checked(in_array($c->id, old('category_ids', [])))>
                                                <span class="form-check-label">{{ $c->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    @error('category_ids') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Tag (pilih)</label>
                                    <select name="tag_ids[]" class="form-select" multiple size="6">
                                        @foreach($tags ?? [] as $t)
                                            <option value="{{ $t->id }}" @selected(in_array($t->id, old('tag_ids', [])))>
                                                {{ $t->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-0">
                                    <label class="form-label">Tag baru</label>
                                    <input type="text" id="tagNewInput" class="form-control" placeholder="contoh: kegiatan, ppdb, prestasi">
                                    <div class="form-text">Pisahkan dengan koma. Akan dibuat otomatis jika belum ada.</div>
                                    <div id="tagNewHidden"></div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </form>

        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>

    <script>
        function previewHero(event) {
            const file = event.target.files && event.target.files[0];
            if (!file) return;
            document.getElementById('heroPreview').src = URL.createObjectURL(file);
        }

        function slugify(str) {
            return (str || '')
                .toString()
                .toLowerCase()
                .trim()
                .replace(/[\s\_]+/g, '-')
                .replace(/[^\w\-]+/g, '')
                .replace(/\-\-+/g, '-')
                .replace(/^-+/, '')
                .replace(/-+$/, '');
        }

        function togglePublishTime() {
            const st = document.getElementById('statusSelect');
            const inp = document.getElementById('publishedAt');

            const isPublished = st.value === 'published';
            inp.disabled = !isPublished;

            if (!isPublished) {
                inp.value = '';
            }
        }

        function syncNewTagsHidden() {
            const wrap = document.getElementById('tagNewHidden');
            wrap.innerHTML = '';

            const raw = document.getElementById('tagNewInput').value || '';
            const items = raw.split(',').map(s => s.trim()).filter(Boolean);

            for (const t of items) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'tag_slugs[]';
                input.value = t;
                wrap.appendChild(input);
            }
        }

        const toolbarOptions = [
            [{ header: [1, 2, 3, false] }],
            [{ size: ['small', false, 'large', 'huge'] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ color: [] }, { background: [] }],
            [{ align: [] }],
            [{ list: 'ordered' }, { list: 'bullet' }],
            ['blockquote'],
            ['link', 'image'],
            ['clean']
        ];

        const quill = new Quill('#quillEditor', {
            theme: 'snow',
            modules: {
                toolbar: {
                    container: toolbarOptions,
                    handlers: {
                        image: function () {
                            selectLocalImageAndUpload();
                        }
                    }
                }
            }
        });

        async function selectLocalImageAndUpload() {
            const input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');
            input.click();

            input.onchange = async () => {
                const file = input.files && input.files[0];
                if (!file) return;

                const formData = new FormData();
                formData.append('image', file);

                const res = await fetch('{{ route('admin.quill.image') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                });

                if (!res.ok) {
                    alert('Upload gambar gagal.');
                    return;
                }

                const data = await res.json();
                if (!data || !data.url) {
                    alert('Upload gambar gagal (response).');
                    return;
                }

                const range = quill.getSelection(true);
                quill.insertEmbed(range.index, 'image', data.url, 'user');
                quill.setSelection(range.index + 1);
            };
        }

        function syncQuillToHidden() {
            document.getElementById('contentDelta').value = JSON.stringify(quill.getContents());
            document.getElementById('contentHtml').value = quill.root.innerHTML;
        }

        document.addEventListener('DOMContentLoaded', () => {
            togglePublishTime();

            const titleInput = document.getElementById('titleId');
            const slugPreview = document.getElementById('slugPreview');

            const updateSlugPreview = () => {
                const s = slugify(titleInput.value);
                slugPreview.textContent = s || '—';
            };

            titleInput.addEventListener('input', updateSlugPreview);
            updateSlugPreview();

            document.getElementById('tagNewInput').addEventListener('input', syncNewTagsHidden);
            syncNewTagsHidden();

            const oldDelta = @json(old('content_delta'));
            if (oldDelta && typeof oldDelta === 'object') {
                try { quill.setContents(oldDelta); } catch (e) {}
            } else if (typeof oldDelta === 'string' && oldDelta.trim() !== '') {
                try { quill.setContents(JSON.parse(oldDelta)); } catch (e) {}
            }

            quill.on('text-change', () => {
                syncQuillToHidden();
            });

            syncQuillToHidden();

            document.getElementById('articleCreateForm').addEventListener('submit', () => {
                syncNewTagsHidden();
                syncQuillToHidden();
            });
        });
    </script>
</x-page.admin>
