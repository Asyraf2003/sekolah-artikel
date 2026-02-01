<x-page.admin>
    @php
        $backUrl = route('admin.articles.index', request()->query());
        $nowLocal = now()->format('Y-m-d\TH:i');

        $title = $article->title_id ?: ($article->title_en ?: ($article->title_ar ?: '—'));

        $heroPath = $article->hero_image;
        $heroUrl = null;
        if ($heroPath) {
            if (\Illuminate\Support\Str::startsWith($heroPath, ['http://','https://'])) {
                $heroUrl = $heroPath;
            } elseif (\Illuminate\Support\Str::startsWith($heroPath, ['storage/', 'articles/', 'article/', 'gallery/', 'images/'])) {
                $heroUrl = \Illuminate\Support\Facades\Storage::url($heroPath);
            } else {
                $heroUrl = asset($heroPath);
            }
        }

        $isScheduled = $article->status === 'published' && $article->published_at && $article->published_at->gt(now());
        $statusUi = $article->status === 'archived'
            ? 'archived'
            : ($article->status === 'draft' ? 'draft' : 'published');

        $publishedAtOld = old('published_at');
        $publishedAtValue = $publishedAtOld !== null
            ? $publishedAtOld
            : ($article->published_at ? $article->published_at->format('Y-m-d\TH:i') : '');

        $pinnedOld = old('pinned_until');
        $pinnedValue = $pinnedOld !== null
            ? $pinnedOld
            : ($article->pinned_until ? $article->pinned_until->format('Y-m-d\TH:i') : '');

        $selectedCategoryIds = old('category_ids', $article->categories->pluck('id')->all());
        $selectedTagIds = old('tag_ids', $article->tags->pluck('id')->all());
    @endphp

    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">
    <style>
    #quillEditor .ql-editor { min-height: 320px; }
    </style>
    @endpush

    <div class="page-heading">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
            <div>
                <h3 class="mb-1">Edit Artikel</h3>
                <p class="text-muted mb-0">
                    Perbarui konten artikel. Slug otomatis tetap stabil: <code>{{ $article->slug }}</code>
                    @if($isScheduled)
                        <span class="badge bg-info text-dark ms-2">Scheduled</span>
                    @endif
                </p>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ $backUrl }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <button form="articleEditForm" type="submit" class="btn btn-primary">
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

            <form id="articleEditForm"
                  method="POST"
                  action="{{ route('admin.articles.update', $article->id) }}"
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')

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
                                                   value="{{ old('title_id', $article->title_id) }}"
                                                   placeholder="Contoh: Kegiatan Pelatihan">
                                            @error('title_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            <div class="form-text">
                                                Slug otomatis tetap: <code>{{ $article->slug }}</code>
                                                <span class="ms-2">Preview slug dari judul (bukan yang dipakai): <code id="slugPreview">—</code></span>
                                            </div>
                                        </div>

                                        <div class="mb-0">
                                            <label class="form-label">Ringkasan (ID)</label>
                                            <textarea name="excerpt_id"
                                                      rows="4"
                                                      class="form-control @error('excerpt_id') is-invalid @enderror"
                                                      placeholder="Ringkasan singkat…">{{ old('excerpt_id', $article->excerpt_id) }}</textarea>
                                            @error('excerpt_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="pane-en" role="tabpanel" aria-labelledby="tab-en">
                                        <div class="mb-3">
                                            <label class="form-label">Title (EN)</label>
                                            <input type="text"
                                                   name="title_en"
                                                   class="form-control @error('title_en') is-invalid @enderror"
                                                   value="{{ old('title_en', $article->title_en) }}"
                                                   placeholder="Optional English title">
                                            @error('title_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="mb-0">
                                            <label class="form-label">Excerpt (EN)</label>
                                            <textarea name="excerpt_en"
                                                      rows="4"
                                                      class="form-control @error('excerpt_en') is-invalid @enderror"
                                                      placeholder="Optional English excerpt…">{{ old('excerpt_en', $article->excerpt_en) }}</textarea>
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
                                                   value="{{ old('title_ar', $article->title_ar) }}"
                                                   placeholder="اختياري">
                                            @error('title_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="mb-0">
                                            <label class="form-label">ملخص (AR)</label>
                                            <textarea name="excerpt_ar"
                                                      rows="4"
                                                      dir="rtl"
                                                      class="form-control @error('excerpt_ar') is-invalid @enderror"
                                                      placeholder="اختياري...">{{ old('excerpt_ar', $article->excerpt_ar) }}</textarea>
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
                                <div class="small text-muted">Quill editor (ID / EN / AR).</div>
                            </div>

                            <div class="card-body">
                                <div id="quillEditor" class="bg-white"></div>

                                {{-- Hidden inputs per bahasa (WAJIB) --}}
                                <input type="hidden" name="content_delta_id" id="contentDeltaId"
                                    value="{{ old('content_delta_id', $article->content_delta_id ? json_encode($article->content_delta_id) : '') }}">
                                <input type="hidden" name="content_html_id" id="contentHtmlId"
                                    value="{{ old('content_html_id', $article->content_html_id ?? '') }}">

                                <input type="hidden" name="content_delta_en" id="contentDeltaEn"
                                    value="{{ old('content_delta_en', $article->content_delta_en ? json_encode($article->content_delta_en) : '') }}">
                                <input type="hidden" name="content_html_en" id="contentHtmlEn"
                                    value="{{ old('content_html_en', $article->content_html_en ?? '') }}">

                                <input type="hidden" name="content_delta_ar" id="contentDeltaAr"
                                    value="{{ old('content_delta_ar', $article->content_delta_ar ? json_encode($article->content_delta_ar) : '') }}">
                                <input type="hidden" name="content_html_ar" id="contentHtmlAr"
                                    value="{{ old('content_html_ar', $article->content_html_ar ?? '') }}">

                                @error('content_delta_id') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                                @error('content_html_id')  <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                                @error('content_delta_en') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                                @error('content_html_en')  <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                                @error('content_delta_ar') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                                @error('content_html_ar')  <div class="text-danger small mt-2">{{ $message }}</div> @enderror

                                <div class="form-text mt-2">
                                    Konten body mengikuti tab bahasa di atas (ID/EN/AR). Image upload via endpoint admin.quill.image.
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
                                         src="{{ $heroUrl ?: 'https://placehold.co/1200x675?text=Cover' }}"
                                         alt="Cover"
                                         style="object-fit: cover;">
                                </div>

                                <label class="form-label">Ganti cover (opsional)</label>
                                <input type="file"
                                       name="hero_image"
                                       accept="image/*"
                                       class="form-control @error('hero_image') is-invalid @enderror"
                                       onchange="previewHero(event)">
                                @error('hero_image') <div class="invalid-feedback">{{ $message }}</div> @enderror

                                <div class="form-text mt-2">Kalau tidak upload, cover lama dipakai.</div>
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
                                        <option value="draft" @selected(old('status', $statusUi)==='draft')>Draft</option>
                                        <option value="published" @selected(old('status', $statusUi)==='published')>Published</option>
                                        <option value="archived" @selected(old('status', $statusUi)==='archived')>Archived</option>
                                    </select>
                                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <div class="form-text">Scheduled = Published + waktu terbit future.</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Waktu terbit</label>
                                    <input type="datetime-local"
                                           id="publishedAt"
                                           name="published_at"
                                           class="form-control @error('published_at') is-invalid @enderror"
                                           value="{{ $publishedAtValue }}"
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
                                           {{ old('is_featured', $article->is_featured) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="isFeatured">Featured</label>
                                </div>

                                <div class="mb-0">
                                    <label class="form-label">Pinned sampai</label>
                                    <input type="datetime-local"
                                           name="pinned_until"
                                           class="form-control @error('pinned_until') is-invalid @enderror"
                                           value="{{ $pinnedValue }}">
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
                                                       @checked(in_array($c->id, $selectedCategoryIds))>
                                                <span class="form-check-label">{{ $c->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Tag (pilih)</label>
                                    <select name="tag_ids[]" class="form-select" multiple size="6">
                                        @foreach($tags ?? [] as $t)
                                            <option value="{{ $t->id }}" @selected(in_array($t->id, $selectedTagIds))>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
<script>
(function () {
    function previewHero(event) {
        const file = event.target.files && event.target.files[0];
        if (!file) return;
        const img = document.getElementById('heroPreview');
        if (img) img.src = URL.createObjectURL(file);
    }
    window.previewHero = previewHero;

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
        if (!st || !inp) return;

        const isPublished = st.value === 'published';
        inp.disabled = !isPublished;
        if (!isPublished) inp.value = '';
    }
    window.togglePublishTime = togglePublishTime;

    function syncNewTagsHidden() {
        const wrap = document.getElementById('tagNewHidden');
        const input = document.getElementById('tagNewInput');
        if (!wrap || !input) return;

        wrap.innerHTML = '';
        const raw = input.value || '';
        const items = raw.split(',').map(s => s.trim()).filter(Boolean);

        for (const t of items) {
            const el = document.createElement('input');
            el.type = 'hidden';
            el.name = 'tag_slugs[]';
            el.value = t;
            wrap.appendChild(el);
        }
    }

    const tabButtons = document.querySelectorAll('#langTabs [data-bs-toggle="tab"]');

    const elDelta = {
        id: document.getElementById('contentDeltaId'),
        en: document.getElementById('contentDeltaEn'),
        ar: document.getElementById('contentDeltaAr'),
    };
    const elHtml = {
        id: document.getElementById('contentHtmlId'),
        en: document.getElementById('contentHtmlEn'),
        ar: document.getElementById('contentHtmlAr'),
    };

    if (!document.getElementById('quillEditor') || !elDelta.id || !elHtml.id) {
        console.warn('Quill elements missing. Pastikan #quillEditor dan hidden inputs contentDelta*/contentHtml* ada.');
        return;
    }

    let activeLang = 'id';

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

    function setDir(lang) {
        const rtl = (lang === 'ar');
        quill.root.setAttribute('dir', rtl ? 'rtl' : 'ltr');
        quill.root.style.textAlign = rtl ? 'right' : 'left';
    }

    function saveLang(lang) {
        const delta = quill.getContents();
        const html = quill.root.innerHTML;

        if (elDelta[lang]) elDelta[lang].value = JSON.stringify(delta);
        if (elHtml[lang])  elHtml[lang].value  = html;
    }

    function loadLang(lang) {
        setDir(lang);

        const raw = (elDelta[lang] ? elDelta[lang].value : '') || '';
        if (raw.trim() !== '') {
            try {
                quill.setContents(JSON.parse(raw));
                return;
            } catch (e) {
                // fallback to html below
            }
        }

        const html = (elHtml[lang] ? elHtml[lang].value : '') || '';
        if (html.trim() !== '') {
            quill.root.innerHTML = html;
            return;
        }

        quill.setText('');
    }

    async function selectLocalImageAndUpload() {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = 'image/*';
        input.click();

        input.onchange = async () => {
            const file = input.files && input.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('image', file);

            let res;
            try {
                res = await fetch("{{ route('admin.quill.image') }}", {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                    body: formData
                });
            } catch (e) {
                alert('Gagal konek saat upload gambar.');
                return;
            }

            if (!res.ok) {
                alert('Upload gambar gagal.');
                return;
            }

            const data = await res.json().catch(() => null);
            if (!data || !data.url) {
                alert('Upload gambar gagal (response tidak valid).');
                return;
            }

            const range = quill.getSelection(true);
            quill.insertEmbed(range.index, 'image', data.url, 'user');
            quill.setSelection(range.index + 1);
        };
    }

    document.addEventListener('DOMContentLoaded', () => {
        togglePublishTime();

        const titleInput = document.getElementById('titleId');
        const slugPreview = document.getElementById('slugPreview');
        if (titleInput && slugPreview) {
            const updateSlugPreview = () => {
                const s = slugify(titleInput.value);
                slugPreview.textContent = s || '—';
            };
            titleInput.addEventListener('input', updateSlugPreview);
            updateSlugPreview();
        }

        const tagInput = document.getElementById('tagNewInput');
        if (tagInput) {
            tagInput.addEventListener('input', syncNewTagsHidden);
            syncNewTagsHidden();
        }

        // init editor with active lang (ID)
        loadLang(activeLang);

        // switch language: save current, load next
        tabButtons.forEach(btn => {
            btn.addEventListener('shown.bs.tab', (ev) => {
                const target = ev.target.getAttribute('data-bs-target');
                const nextLang = target === '#pane-en' ? 'en' : (target === '#pane-ar' ? 'ar' : 'id');

                saveLang(activeLang);
                activeLang = nextLang;
                loadLang(activeLang);
            });
        });

        const form = document.getElementById('articleEditForm');
        if (form) {
            form.addEventListener('submit', () => {
                saveLang(activeLang);
                syncNewTagsHidden();
            });
        }
    });
})();
</script>
@endpush
</x-page.admin>
