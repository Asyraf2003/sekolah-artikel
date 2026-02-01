<x-page.admin>
  <x-slot name="header">
    <header class="mb-3 d-flex align-items-center justify-content-between">
      <a href="#" class="burger-btn d-block d-xl-none">
        <i class="bi bi-justify fs-3"></i>
      </a>
      <div class="d-none d-xl-flex align-items-center gap-2">
        <span class="badge bg-primary-subtle text-primary">TA 2025/2026</span>
        <span class="badge bg-info-subtle text-info">Dashboard Super Admin</span>
      </div>
    </header>
  </x-slot>

  <div class="page-heading">
    <h1 class="font-semibold text-xxl text-gray-800 leading-tight">
      Super Admin Dashboard
    </h1>
    <p class="text-muted mt-1">Ringkasan operasional, akademik, finansial, & kesehatan sistem</p>
  </div>

  <div class="page-content">
    <section class="row">

      {{-- ========= LEFT: MAIN CONTENT ========= --}}
      <div class="col-12 col-xxl-9">

        {{-- TOP KPIs --}}
        <div class="row">
    <div class="col-6 col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body px-4 py-4-5">
                <div class="row">
                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                        <div class="stats-icon purple mb-2">
                            <i class="bi bi-people-fill"></i>
                        </div>
                    </div>
                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                        <h6 class="text-muted font-semibold">Total Siswa</h6>
                        <h6 class="font-extrabold mb-0">{{ number_format($totalSiswa) }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body px-4 py-4-5">
                <div class="row">
                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                        <div class="stats-icon blue mb-2">
                            <i class="bi bi-person-badge-fill"></i>
                        </div>
                    </div>
                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                        <h6 class="text-muted font-semibold">Guru & Staff</h6>
                        <h6 class="font-extrabold mb-0">{{ number_format($totalGuru) }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body px-4 py-4-5">
                <div class="row">
                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                        <div class="stats-icon green mb-2">
                            <i class="bi bi-journal-text"></i>
                        </div>
                    </div>
                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                        <h6 class="text-muted font-semibold">Total Artikel</h6>
                        <h6 class="font-extrabold mb-0">{{ number_format($totalArtikel) }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body px-4 py-4-5">
                <div class="row">
                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                        <div class="stats-icon red mb-2">
                            <i class="bi bi-images"></i>
                        </div>
                    </div>
                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                        <h6 class="text-muted font-semibold">Jumlah Galeri</h6>
                        <h6 class="font-extrabold mb-0">{{ number_format($totalGaleri) }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


      </div>

      {{-- ========= RIGHT: SIDEBAR WIDGETS ========= --}}
      <div class="col-12 col-xxl-3">

        {{-- PROFILE CARD --}}
        <div class="card">
          <div class="card-body py-4 px-4">
            <div class="d-flex align-items-center">
              <div class="avatar avatar-xl">
                <img src="{{ asset('assets/compiled/jpg/1.jpg') }}" alt="Admin Avatar">
              </div>
              <div class="ms-3 name">
                <h5 class="font-bold">Super Admin</h5>
                <h6 class="text-muted mb-0">superadmin@sekolah.id</h6>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</x-page.admin>
