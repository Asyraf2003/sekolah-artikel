<x-page.admin>
    @php
        $backUrl = route('admin.dashboard'); // ganti kalau kamu punya halaman index admin lain
    @endphp

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">
        <style>
            #visionEditor .ql-editor,
            #missionEditor .ql-editor { min-height: 220px; }
        </style>
    @endpush

    <div class="page-heading">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
            <div>
                <h3 class="mb-1">Edit About</h3>
                <p class="text-muted mb-0">
                    Halaman ini pasif dan jarang diubah. Tapi tetap kita buat editor biar rapi, bukan biar dramatis.
                </p>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ $backUrl }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <button form="aboutEditForm" type="submit" class="btn btn-primary">
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

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form id="aboutEditForm"
                  method="POST"
                  action="{{ route('admin.about.update', $about->id) }}">
                @csrf
                @method('PUT')

                <div class="card mb-3">
                    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="fw-semibold">
                            <i class="bi bi-translate me-1"></i> Bahasa
                        </div>
                        <div class="small text-muted">Edit Visi & Misi mengikuti tab bahasa.</div>
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

                        {{-- panes cuma buat nentuin activeLang, editornya tetap 2 quill di bawah --}}
                        <div class="tab-content pt-3">
                            <div class="tab-pane fade show active" id="pane-id" role="tabpanel" aria-labelledby="tab-id"></div>
                            <div class="tab-pane fade" id="pane-en" role="tabpanel" aria-labelledby="tab-en"></div>
                            <div class="tab-pane fade" id="pane-ar" role="tabpanel" aria-labelledby="tab-ar"></div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-12 col-lg-6">
                        <div class="card h-100">
                            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="fw-semibold">
                                    <i class="bi bi-eye me-1"></i> Visi
                                </div>
                                <div class="small text-muted">Quill (ID / EN / AR)</div>
                            </div>

                            <div class="card-body">
                                <div id="visionEditor" class="bg-white"></div>

                                {{-- Hidden inputs per bahasa --}}
                                <input type="hidden" name="vision_content_delta_id" id="visionDeltaId"
                                       value="{{ old('vision_content_delta_id', $about->vision_content_delta_id ? json_encode($about->vision_content_delta_id) : '') }}">
                                <input type="hidden" name="vision_content_html_id" id="visionHtmlId"
                                       value="{{ old('vision_content_html_id', $about->vision_content_html_id ?? '') }}">

                                <input type="hidden" name="vision_content_delta_en" id="visionDeltaEn"
                                       value="{{ old('vision_content_delta_en', $about->vision_content_delta_en ? json_encode($about->vision_content_delta_en) : '') }}">
                                <input type="hidden" name="vision_content_html_en" id="visionHtmlEn"
                                       value="{{ old('vision_content_html_en', $about->vision_content_html_en ?? '') }}">

                                <input type="hidden" name="vision_content_delta_ar" id="visionDeltaAr"
                                       value="{{ old('vision_content_delta_ar', $about->vision_content_delta_ar ? json_encode($about->vision_content_delta_ar) : '') }}">
                                <input type="hidden" name="vision_content_html_ar" id="visionHtmlAr"
                                       value="{{ old('vision_content_html_ar', $about->vision_content_html_ar ?? '') }}">

                                @error('vision_content_delta_id') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                                @error('vision_content_html_id')  <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                                @error('vision_content_delta_en') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                                @error('vision_content_html_en')  <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                                @error('vision_content_delta_ar') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                                @error('vision_content_html_ar')  <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6">
                        <div class="card h-100">
                            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="fw-semibold">
                                    <i class="bi bi-bullseye me-1"></i> Misi
                                </div>
                                <div class="small text-muted">Quill (ID / EN / AR)</div>
                            </div>

                            <div class="card-body">
                                <div id="missionEditor" class="bg-white"></div>

                                {{-- Hidden inputs per bahasa --}}
                                <input type="hidden" name="mission_content_delta_id" id="missionDeltaId"
                                       value="{{ old('mission_content_delta_id', $about->mission_content_delta_id ? json_encode($about->mission_content_delta_id) : '') }}">
                                <input type="hidden" name="mission_content_html_id" id="missionHtmlId"
                                       value="{{ old('mission_content_html_id', $about->mission_content_html_id ?? '') }}">

                                <input type="hidden" name="mission_content_delta_en" id="missionDeltaEn"
                                       value="{{ old('mission_content_delta_en', $about->mission_content_delta_en ? json_encode($about->mission_content_delta_en) : '') }}">
                                <input type="hidden" name="mission_content_html_en" id="missionHtmlEn"
                                       value="{{ old('mission_content_html_en', $about->mission_content_html_en ?? '') }}">

                                <input type="hidden" name="mission_content_delta_ar" id="missionDeltaAr"
                                       value="{{ old('mission_content_delta_ar', $about->mission_content_delta_ar ? json_encode($about->mission_content_delta_ar) : '') }}">
                                <input type="hidden" name="mission_content_html_ar" id="missionHtmlAr"
                                       value="{{ old('mission_content_html_ar', $about->mission_content_html_ar ?? '') }}">

                                @error('mission_content_delta_id') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                                @error('mission_content_html_id')  <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                                @error('mission_content_delta_en') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                                @error('mission_content_html_en')  <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                                @error('mission_content_delta_ar') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                                @error('mission_content_html_ar')  <div class="text-danger small mt-2">{{ $message }}</div> @enderror

                                <div class="form-text mt-2">
                                    Pakai heading + list di editor untuk title dan bullet. Image upload via endpoint admin.quill.image.
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
                const tabButtons = document.querySelectorAll('#langTabs [data-bs-toggle="tab"]');

                const visionDelta = {
                    id: document.getElementById('visionDeltaId'),
                    en: document.getElementById('visionDeltaEn'),
                    ar: document.getElementById('visionDeltaAr'),
                };
                const visionHtml = {
                    id: document.getElementById('visionHtmlId'),
                    en: document.getElementById('visionHtmlEn'),
                    ar: document.getElementById('visionHtmlAr'),
                };

                const missionDelta = {
                    id: document.getElementById('missionDeltaId'),
                    en: document.getElementById('missionDeltaEn'),
                    ar: document.getElementById('missionDeltaAr'),
                };
                const missionHtml = {
                    id: document.getElementById('missionHtmlId'),
                    en: document.getElementById('missionHtmlEn'),
                    ar: document.getElementById('missionHtmlAr'),
                };

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

                function makeQuill(editorId) {
                    return new Quill(editorId, {
                        theme: 'snow',
                        modules: {
                            toolbar: {
                                container: toolbarOptions,
                                handlers: {
                                    image: function () { selectLocalImageAndUpload(this.quill); }
                                }
                            }
                        }
                    });
                }

                const visionQuill = makeQuill('#visionEditor');
                const missionQuill = makeQuill('#missionEditor');

                let activeLang = 'id';

                function setDir(quill, lang) {
                    const rtl = (lang === 'ar');
                    quill.root.setAttribute('dir', rtl ? 'rtl' : 'ltr');
                    quill.root.style.textAlign = rtl ? 'right' : 'left';
                }

                function saveLang(lang) {
                    // vision
                    if (visionDelta[lang]) visionDelta[lang].value = JSON.stringify(visionQuill.getContents());
                    if (visionHtml[lang])  visionHtml[lang].value  = visionQuill.root.innerHTML;

                    // mission
                    if (missionDelta[lang]) missionDelta[lang].value = JSON.stringify(missionQuill.getContents());
                    if (missionHtml[lang])  missionHtml[lang].value  = missionQuill.root.innerHTML;
                }

                function loadOne(quill, deltaEl, htmlEl, lang) {
                    setDir(quill, lang);

                    const raw = (deltaEl && deltaEl.value ? deltaEl.value : '').trim();
                    if (raw) {
                        try {
                            quill.setContents(JSON.parse(raw));
                            return;
                        } catch (e) { /* fallback to html */ }
                    }

                    const html = (htmlEl && htmlEl.value ? htmlEl.value : '').trim();
                    if (html) {
                        quill.root.innerHTML = html;
                        return;
                    }

                    quill.setText('');
                }

                function loadLang(lang) {
                    loadOne(visionQuill, visionDelta[lang], visionHtml[lang], lang);
                    loadOne(missionQuill, missionDelta[lang], missionHtml[lang], lang);
                }

                async function selectLocalImageAndUpload(quill) {
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
                    loadLang(activeLang);

                    tabButtons.forEach(btn => {
                        btn.addEventListener('shown.bs.tab', (ev) => {
                            const target = ev.target.getAttribute('data-bs-target');
                            const nextLang = target === '#pane-en' ? 'en' : (target === '#pane-ar' ? 'ar' : 'id');

                            saveLang(activeLang);
                            activeLang = nextLang;
                            loadLang(activeLang);
                        });
                    });

                    const form = document.getElementById('aboutEditForm');
                    if (form) {
                        form.addEventListener('submit', () => {
                            saveLang(activeLang);
                        });
                    }
                });
            })();
        </script>
    @endpush
</x-page.admin>
