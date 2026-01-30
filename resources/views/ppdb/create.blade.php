<x-page.index :title="'PPDB - Formulir Pendaftaran'">

    {{-- Gunakan bg-transparent agar mengikuti warna background dari <x-page.index> --}}
    <div class="min-h-[80vh] flex flex-col justify-center items-center py-10 px-4 sm:px-6 lg:px-8 transition-colors duration-500">
        
        {{-- KUNCI LEBAR DI SINI (biar tidak full 1 halaman) --}}
        <div class="w-full sm:w-11/12 md:w-2/3 lg:w-1/2 xl:w-5/12 2xl:w-1/3 mx-auto">
            {{-- Header: Meniru style heading landing page Anda --}}
            <div class="mb-8">
                <h2 class="text-3xl font-semibold text-gray-900 dark:text-gray-100">
                    Formulir Pendaftaran PPDB
                </h2>
                <p class="mt-4 text-gray-600 dark:text-gray-400">
                    Isi data di bawah ini. Pendaftaran akan diverifikasi oleh admin setelah dikirim.
                </p>
            </div>

            {{-- Card Form: Style identik dengan kotak Peta di landing page --}}
            <div class="rounded-2xl border border-gray-200 bg-white shadow-xl dark:border-gray-800 dark:bg-gray-900 overflow-hidden transition-all duration-300">
                
                <form action="{{ route('ppdb.store') }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-10 space-y-5">
                    @csrf
                    
                    {{-- Honeypot Field --}}
                    <div style="display: none;">
                        <input type="text" name="website" tabindex="-1" autocomplete="off">
                    </div>
                    {{-- Nama Lengkap --}}
                    <input type="text" name="full_name" value="{{ old('full_name') }}" placeholder="Nama Lengkap" required
                           class="w-full rounded-xl border border-gray-200 px-4 py-3 text-gray-900 outline-none transition-all
                                  focus:ring-2 focus:ring-sky-500 focus:border-sky-500
                                  dark:border-gray-800 dark:bg-gray-950 dark:text-gray-100 dark:placeholder-gray-600">

                    {{-- Email --}}
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" required
                           class="w-full rounded-xl border border-gray-200 px-4 py-3 text-gray-900 outline-none transition-all
                                  focus:ring-2 focus:ring-sky-500 focus:border-sky-500
                                  dark:border-gray-800 dark:bg-gray-950 dark:text-gray-100 dark:placeholder-gray-600">

                    {{-- WhatsApp --}}
                    <input type="text" name="whatsapp" value="{{ old('whatsapp') }}" placeholder="No. WhatsApp" required
                           class="w-full rounded-xl border border-gray-200 px-4 py-3 text-gray-900 outline-none transition-all
                                  focus:ring-2 focus:ring-sky-500 focus:border-sky-500
                                  dark:border-gray-800 dark:bg-gray-950 dark:text-gray-100 dark:placeholder-gray-600">

                    {{-- Upload Berkas --}}
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 ml-1">Bukti Pembayaran</label>
                        <div class="relative flex items-center justify-center rounded-xl border border-dashed border-gray-300 bg-gray-50 p-6 
                                    transition-all hover:bg-gray-100 
                                    dark:border-gray-800 dark:bg-gray-950 dark:hover:border-gray-700">
                            <input
                                type="file"
                                name="payment_proof"
                                required
                                accept=".jpg,.jpeg,.png,.pdf"
                                class="absolute inset-0 z-10 cursor-pointer opacity-0"
                            />
                            <div class="text-center">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Klik untuk pilih file (JPG, PNG, PDF)</p>
                            </div>
                        </div>
                    </div>

                    {{-- Button: Sky-500 sesuai landing page --}}
                    <div class="pt-4 flex flex-col sm:flex-row gap-3">
                        <button type="submit" class="flex-1 rounded-xl bg-sky-500 px-6 py-3 text-white font-medium hover:bg-sky-600 transition-all active:scale-95 shadow-lg shadow-sky-500/20">
                            Kirim Pendaftaran
                        </button>
                        <a href="{{ route('home') }}" class="flex-1 rounded-xl border border-gray-200 bg-white px-6 py-3 text-center text-gray-600 font-medium 
                                   hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800">
                            Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-page.index>
