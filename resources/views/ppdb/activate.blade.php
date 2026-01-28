<x-page.index :title="'PPDB - Aktivasi Akun'">

    <div class="min-h-[80vh] flex flex-col justify-center items-center py-10 px-4 sm:px-6 lg:px-8 transition-colors duration-500">
        
        <div class="w-full sm:w-11/12 md:w-2/3 lg:w-1/2 xl:w-5/12 2xl:w-1/3 mx-auto">
            {{-- Header --}}
            <div class="mb-8">
                <h2 class="text-3xl font-semibold text-gray-900 dark:text-gray-100">
                    Aktivasi Akun PPDB
                </h2>
                <p class="mt-4 text-gray-600 dark:text-gray-400">
                    Buat password untuk mengaktifkan akun. Setelah aktivasi, kamu bisa masuk ke dashboard.
                </p>
            </div>

            {{-- Card --}}
            <div class="rounded-2xl border border-gray-200 bg-white shadow-xl dark:border-gray-800 dark:bg-gray-900 overflow-hidden transition-all duration-300">
                
                <form action="{{ route('ppdb.activate', ['token' => $token]) }}" method="POST" class="p-6 sm:p-10 space-y-5">
                    @csrf

                    {{-- Flash success/info --}}
                    @if (session('success'))
                        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-900/40 dark:bg-green-900/20 dark:text-green-200">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- Errors --}}
                    @if ($errors->any())
                        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-900/40 dark:bg-red-900/20 dark:text-red-200">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Info pendaftar (read-only) --}}
                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-950">
                        <div class="space-y-2">
                            <div class="text-sm text-gray-600 dark:text-gray-400">Data pendaftar</div>

                            <div class="grid grid-cols-1 gap-3">
                                <div>
                                    <div class="text-xs font-medium text-gray-500 dark:text-gray-500">Nama</div>
                                    <div class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        {{ $app->full_name }}
                                    </div>
                                </div>

                                <div>
                                    <div class="text-xs font-medium text-gray-500 dark:text-gray-500">Email</div>
                                    <div class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        {{ $app->email }}
                                    </div>
                                </div>

                                <div>
                                    <div class="text-xs font-medium text-gray-500 dark:text-gray-500">WhatsApp</div>
                                    <div class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        {{ $app->whatsapp }}
                                    </div>
                                </div>
                            </div>

                            <div class="pt-2 text-xs text-gray-500 dark:text-gray-500">
                                Pastikan data di atas benar. Jika tidak sesuai, hubungi admin.
                            </div>
                        </div>
                    </div>

                    {{-- Password --}}
                    <input type="password" name="password" placeholder="Password baru" required
                           class="w-full rounded-xl border border-gray-200 px-4 py-3 text-gray-900 outline-none transition-all
                                  focus:ring-2 focus:ring-sky-500 focus:border-sky-500
                                  dark:border-gray-800 dark:bg-gray-950 dark:text-gray-100 dark:placeholder-gray-600">

                    {{-- Konfirmasi Password --}}
                    <input type="password" name="password_confirmation" placeholder="Konfirmasi password" required
                           class="w-full rounded-xl border border-gray-200 px-4 py-3 text-gray-900 outline-none transition-all
                                  focus:ring-2 focus:ring-sky-500 focus:border-sky-500
                                  dark:border-gray-800 dark:bg-gray-950 dark:text-gray-100 dark:placeholder-gray-600">

                    {{-- Button --}}
                    <div class="pt-4 flex flex-col sm:flex-row gap-3">
                        <button type="submit"
                                class="flex-1 rounded-xl bg-sky-500 px-6 py-3 text-white font-medium hover:bg-sky-600 transition-all active:scale-95 shadow-lg shadow-sky-500/20">
                            Aktivasi Akun
                        </button>

                        <a href="{{ route('login') }}"
                           class="flex-1 rounded-xl border border-gray-200 bg-white px-6 py-3 text-center text-gray-600 font-medium 
                                  hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800">
                            Ke Login
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-page.index>
