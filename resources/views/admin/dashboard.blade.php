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
        <div class="row g-3">
          <div class="col-6 col-lg-3 col-md-6">
            <div class="card h-100">
              <div class="card-body px-4 py-4-5">
                <div class="row">
                  <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                    <div class="stats-icon purple mb-2"><i class="bi bi-people-fill"></i></div>
                  </div>
                  <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                    <h6 class="text-muted font-semibold">Total Siswa</h6>
                    <h6 class="font-extrabold mb-0">1.236</h6>
                    <div class="small text-success">+3.1% bln ini</div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-6 col-lg-3 col-md-6">
            <div class="card h-100">
              <div class="card-body px-4 py-4-5">
                <div class="row">
                  <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                    <div class="stats-icon blue mb-2"><i class="bi bi-person-badge-fill"></i></div>
                  </div>
                  <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                    <h6 class="text-muted font-semibold">Guru & Staff</h6>
                    <h6 class="font-extrabold mb-0">87</h6>
                    <div class="small text-muted">Aktif: 82 • Cuti: 5</div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-6 col-lg-3 col-md-6">
            <div class="card h-100">
              <div class="card-body px-4 py-4-5">
                <div class="row">
                  <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                    <div class="stats-icon green mb-2"><i class="bi bi-grid-fill"></i></div>
                  </div>
                  <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                    <h6 class="text-muted font-semibold">Rombel/Kelas</h6>
                    <h6 class="font-extrabold mb-0">36</h6>
                    <div class="small text-muted">Rasio siswa/kelas: 34.3</div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-6 col-lg-3 col-md-6">
            <div class="card h-100">
              <div class="card-body px-4 py-4-5">
                <div class="row">
                  <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                    <div class="stats-icon red mb-2"><i class="bi bi-journal-text"></i></div>
                  </div>
                  <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                    <h6 class="text-muted font-semibold">PPDB (Minggu Ini)</h6>
                    <h6 class="font-extrabold mb-0">58</h6>
                    <div class="small text-success">+12 dibanding minggu lalu</div>
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

        {{-- QUICK ACTIONS --}}
        <div class="card">
          <div class="card-header">
            <h4 class="mb-0"><i class="bi bi-lightning-charge me-2"></i>Quick Actions</h4>
          </div>
          <div class="card-body">
            <div class="d-grid gap-2">
              <a href="#" class="btn btn-outline-primary btn-sm"><i class="bi bi-person-plus me-1"></i> Tambah Akun</a>
              <a href="#" class="btn btn-outline-success btn-sm"><i class="bi bi-gear me-1"></i> Konfigurasi Sistem</a>
              <a href="#" class="btn btn-outline-secondary btn-sm"><i class="bi bi-shield-lock me-1"></i> Roles & Permission</a>
              <a href="#" class="btn btn-outline-info btn-sm"><i class="bi bi-cloud-arrow-up me-1"></i> Jalankan Backup</a>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</x-page.admin>
