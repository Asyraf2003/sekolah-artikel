<x-page.index :title="'PPDB - Edit Data Pendaftaran'">

  <div class="min-h-[80vh] flex flex-col justify-center items-center py-10 px-4 sm:px-6 lg:px-8 transition-colors duration-500">
    <div class="w-full sm:w-11/12 md:w-2/3 lg:w-1/2 xl:w-5/12 2xl:w-1/3 mx-auto">

      {{-- Header --}}
      <div class="mb-8">
        <h2 class="text-3xl font-semibold text-gray-900 dark:text-gray-100">
          Edit Data PPDB
        </h2>
        <p class="mt-4 text-gray-600 dark:text-gray-400">
          Perbaiki data yang salah, lalu kirim ulang. Setelah disimpan, data akan kembali ke antrian verifikasi admin.
        </p>
      </div>

      {{-- Alerts --}}
      @if (session('success'))
        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800
                    dark:border-emerald-900/50 dark:bg-emerald-950/40 dark:text-emerald-200">
          {{ session('success') }}
        </div>
      @endif

      @if ($errors->any())
        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800
                    dark:border-red-900/50 dark:bg-red-950/40 dark:text-red-200">
          <div class="font-medium mb-2">Ada yang perlu dibetulkan:</div>
          <ul class="list-disc ps-5 space-y-1 text-sm">
            @foreach ($errors->all() as $e)
              <li>{{ $e }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      {{-- Card Form --}}
      <div class="rounded-2xl border border-gray-200 bg-white shadow-xl dark:border-gray-800 dark:bg-gray-900 overflow-hidden transition-all duration-300">
        <form action="{{ route('ppdb.edit.update', ['token' => $token]) }}"
              method="POST"
              enctype="multipart/form-data"
              class="p-6 sm:p-10 space-y-5">
          @csrf

          {{-- Nama Lengkap --}}
          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300 ml-1">Nama Lengkap</label>
            <input
              type="text"
              name="full_name"
              value="{{ old('full_name', $app->full_name) }}"
              placeholder="Nama Lengkap"
              required
              class="w-full rounded-xl border border-gray-200 px-4 py-3 text-gray-900 outline-none transition-all
                     focus:ring-2 focus:ring-sky-500 focus:border-sky-500
                     dark:border-gray-800 dark:bg-gray-950 dark:text-gray-100 dark:placeholder-gray-600"
            />
          </div>

          {{-- Email --}}
          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300 ml-1">Email</label>
            <input
              type="email"
              name="email"
              value="{{ old('email', $app->email) }}"
              placeholder="Email"
              required
              class="w-full rounded-xl border border-gray-200 px-4 py-3 text-gray-900 outline-none transition-all
                     focus:ring-2 focus:ring-sky-500 focus:border-sky-500
                     dark:border-gray-800 dark:bg-gray-950 dark:text-gray-100 dark:placeholder-gray-600"
            />
            <p class="text-xs text-gray-500 dark:text-gray-400 ml-1">
              Pastikan email aktif. Link aktivasi akan dikirim lewat admin.
            </p>
          </div>

          {{-- WhatsApp --}}
          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300 ml-1">No. WhatsApp</label>
            <input
              type="text"
              name="whatsapp"
              value="{{ old('whatsapp', $app->whatsapp) }}"
              placeholder="No. WhatsApp"
              required
              class="w-full rounded-xl border border-gray-200 px-4 py-3 text-gray-900 outline-none transition-all
                     focus:ring-2 focus:ring-sky-500 focus:border-sky-500
                     dark:border-gray-800 dark:bg-gray-950 dark:text-gray-100 dark:placeholder-gray-600"
            />
            <p class="text-xs text-gray-500 dark:text-gray-400 ml-1">
              Gunakan nomor yang benar karena admin bisa mengirim link lewat WA.
            </p>
          </div>

          {{-- Upload Berkas (opsional) --}}
          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300 ml-1">
              Bukti Pembayaran <span class="text-xs text-gray-500 dark:text-gray-400">(opsional jika ingin ganti)</span>
            </label>

            <div class="relative flex flex-col gap-2 rounded-xl border border-dashed border-gray-300 bg-gray-50 p-6
                        transition-all hover:bg-gray-100
                        dark:border-gray-800 dark:bg-gray-950 dark:hover:border-gray-700">

              <input
                type="file"
                name="payment_proof"
                accept=".jpg,.jpeg,.png,.pdf"
                class="absolute inset-0 z-10 cursor-pointer opacity-0"
              />

              <div class="text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                  Klik untuk pilih file baru (JPG, PNG, PDF)
                </p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                  Kalau tidak upload ulang, file lama tetap dipakai.
                </p>
              </div>

              @if (!empty($app->payment_proof_path))
                <div class="mt-3 rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs text-gray-600
                            dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                  File saat ini tersimpan. (Admin bisa lihat di panel admin)
                </div>
              @endif
            </div>
          </div>

          {{-- Button --}}
          <div class="pt-4 flex flex-col sm:flex-row gap-3">
            <button
              type="submit"
              class="flex-1 rounded-xl bg-sky-500 px-6 py-3 text-white font-medium hover:bg-sky-600 transition-all active:scale-95 shadow-lg shadow-sky-500/20">
              Simpan Perubahan
            </button>

            <a
              href="{{ route('ppdb.receipt', $app->public_code) }}"
              class="flex-1 rounded-xl border border-gray-200 bg-white px-6 py-3 text-center text-gray-600 font-medium
                     hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800">
              Kembali
            </a>
          </div>

          <div class="pt-2 text-xs text-gray-500 dark:text-gray-400">
            Catatan: Link edit ini hanya bisa dipakai sekali dan bisa kedaluwarsa.
          </div>
        </form>
      </div>

    </div>
  </div>

</x-page.index>
