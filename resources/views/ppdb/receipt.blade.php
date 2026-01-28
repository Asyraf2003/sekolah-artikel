{{-- resources/views/ppdb/receipt.blade.php --}}
<x-page.index :title="'PPDB - Bukti Pendaftaran'">

    <div class="min-h-[80vh] flex flex-col justify-center items-center py-10 px-4 sm:px-6 lg:px-8 transition-colors duration-500">

        <div class="w-full sm:w-11/12 md:w-2/3 lg:w-1/2 xl:w-5/12 2xl:w-1/3 mx-auto">

            {{-- Header --}}
            <div class="mb-8">
                <h2 class="text-3xl font-semibold text-gray-900 dark:text-gray-100">
                    Bukti Pendaftaran PPDB
                </h2>
                <p class="mt-4 text-gray-600 dark:text-gray-400">
                    Simpan kode pendaftaran kamu. Status akan berubah setelah admin melakukan verifikasi.
                </p>
            </div>

            {{-- Card --}}
            <div class="rounded-2xl border border-gray-200 bg-white shadow-xl dark:border-gray-800 dark:bg-gray-900 overflow-hidden transition-all duration-300">

                <div class="p-6 sm:p-10 space-y-6">

                    {{-- Flash success --}}
                    @if (session('success'))
                        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800
                                    dark:border-emerald-900/40 dark:bg-emerald-950/40 dark:text-emerald-200">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- Kode pendaftaran --}}
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-5
                                dark:border-gray-800 dark:bg-gray-950">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Kode Pendaftaran</p>
                                <p class="mt-1 text-2xl font-semibold tracking-wider text-gray-900 dark:text-gray-100">
                                    {{ $app->public_code ?? '-' }}
                                </p>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                    Gunakan kode ini untuk referensi saat menghubungi admin.
                                </p>
                            </div>

                            {{-- Badge status --}}
                            @php
                                $status = $app->status ?? 'submitted';

                                $badge = match ($status) {
                                    'submitted'   => ['Menunggu Verifikasi', 'bg-amber-50 text-amber-800 border-amber-200 dark:bg-amber-950/40 dark:text-amber-200 dark:border-amber-900/40'],
                                    'resubmitted' => ['Dikirim Ulang', 'bg-sky-50 text-sky-800 border-sky-200 dark:bg-sky-950/40 dark:text-sky-200 dark:border-sky-900/40'],
                                    'approved'    => ['Disetujui', 'bg-emerald-50 text-emerald-800 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-200 dark:border-emerald-900/40'],
                                    'rejected'    => ['Ditolak', 'bg-rose-50 text-rose-800 border-rose-200 dark:bg-rose-950/40 dark:text-rose-200 dark:border-rose-900/40'],
                                    'activated'   => ['Sudah Aktif', 'bg-gray-50 text-gray-800 border-gray-200 dark:bg-gray-950/40 dark:text-gray-200 dark:border-gray-800'],
                                    default       => [ucfirst($status), 'bg-gray-50 text-gray-800 border-gray-200 dark:bg-gray-950/40 dark:text-gray-200 dark:border-gray-800'],
                                };
                            @endphp

                            <span class="shrink-0 inline-flex items-center rounded-full border px-3 py-1 text-sm font-medium {{ $badge[1] }}">
                                {{ $badge[0] }}
                            </span>
                        </div>
                    </div>

                    {{-- Data pendaftaran --}}
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 gap-4">
                            <div class="rounded-xl border border-gray-200 px-4 py-3 dark:border-gray-800">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Nama Lengkap</p>
                                <p class="mt-1 text-gray-900 dark:text-gray-100 font-medium">
                                    {{ $app->full_name }}
                                </p>
                            </div>

                            <div class="rounded-xl border border-gray-200 px-4 py-3 dark:border-gray-800">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Email</p>
                                <p class="mt-1 text-gray-900 dark:text-gray-100 font-medium">
                                    {{ $app->email }}
                                </p>
                            </div>

                            <div class="rounded-xl border border-gray-200 px-4 py-3 dark:border-gray-800">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">No. WhatsApp</p>
                                <p class="mt-1 text-gray-900 dark:text-gray-100 font-medium">
                                    {{ $app->whatsapp }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Alasan penolakan --}}
                    @if (($app->status ?? null) === 'rejected')
                        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800
                                    dark:border-rose-900/40 dark:bg-rose-950/40 dark:text-rose-200">
                            <p class="text-sm font-semibold">Pendaftaran Ditolak</p>
                            <p class="mt-1 text-sm">
                                {{ $app->rejected_reason ?: 'Tidak ada alasan yang dicantumkan.' }}
                            </p>
                            <p class="mt-2 text-xs text-rose-700/80 dark:text-rose-200/80">
                                Jika admin memberikan link edit, gunakan link tersebut untuk memperbaiki data.
                            </p>
                        </div>
                    @endif

                    {{-- Info verifikasi --}}
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        <p>
                            Status saat ini:
                            <span class="font-medium text-gray-900 dark:text-gray-100">{{ $badge[0] }}</span>
                        </p>
                        <p class="mt-1">
                            Jika sudah disetujui, admin akan mengirimkan link aktivasi untuk membuat password dan masuk dashboard.
                        </p>
                    </div>

                    {{-- Buttons --}}
                    <div class="pt-2 flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('ppdb.create') }}"
                           class="flex-1 rounded-xl bg-sky-500 px-6 py-3 text-center text-white font-medium hover:bg-sky-600 transition-all active:scale-95 shadow-lg shadow-sky-500/20">
                            Buat Pendaftaran Baru
                        </a>

                        <a href="{{ route('home') }}"
                           class="flex-1 rounded-xl border border-gray-200 bg-white px-6 py-3 text-center text-gray-600 font-medium
                                  hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800">
                            Kembali ke Home
                        </a>
                    </div>

                    <div class="text-xs text-gray-500 dark:text-gray-500">
                        Catatan: Bukti pembayaran tidak ditampilkan di halaman ini demi keamanan.
                    </div>

                </div>
            </div>

        </div>
    </div>

</x-page.index>
 