<x-guest-layout>
    {{-- Wrapper --}}
    {{-- Wrapper ini bertugas untuk menempatkan kotak login secara terpusat di layar --}}
    <div style="display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: transparent; width: 100%; margin: 0; padding: 0; position: absolute; top: 0; left: 0;">
        
        {{-- Kotak login --}}
        {{-- Kotak login ini memiliki batasan lebar, padding, border dan background warna yang sudah ditentukan --}}
        <div style="width: 100%; max-width: 450px; padding: 20px; border: 1px solid #F58220; background-color: #FFF7F0; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">

            {{-- Logo --}}
            {{-- Bagian ini untuk menampilkan logo aplikasi di atas kotak login --}}
            <div style="display: flex; justify-content: center; margin-bottom: 24px;">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" style="width: 116px; height: 116px; object-fit: contain;">
            </div>

            {{-- Judul --}}
            {{-- Teks ini memberikan petunjuk kepada pengguna untuk melakukan login --}}
            <div style="display: flex; justify-content: center; align-items: center; margin-bottom: 23px;">
                <span style="font-size: 18px; font-weight: 550; text-align: center;">Masuk Akun Anda</span>
            </div>

            {{-- Status sesi: menampilkan status jika ada --}}
            {{-- Jika ada status sesi (misalnya berhasil login), bagian ini akan menampilkannya --}}
            <x-auth-session-status class="mb-4" :status="session('status')" />

            {{-- Form login dengan pengaturan form yang jelas --}}
            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Input Email --}}
                {{-- Pengguna diminta untuk memasukkan email mereka --}}
                <div style="margin-bottom: 23px;">
                    <x-input-label for="email"/>
                    <x-text-input
                        id="email"
                        name="email"
                        type="email"
                        :value="old('email')"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="Email"
                        class="block mt-1 w-full"
                        style="border: 1px solid #F58220;"  
                    />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                {{-- Input Password --}}
                {{-- Pengguna diminta untuk memasukkan kata sandi mereka --}}
                <div style="margin-bottom: 23px;">
                    <x-input-label for="password"/>
                    <x-text-input
                        id="password"
                        name="password"
                        type="password"
                        required
                        autocomplete="current-password"
                        placeholder="Kata Sandi"
                        class="block mt-1 w-full"
                        style="border: 1px solid #F58220;"  
                    />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                {{-- Remember Me --}}
                {{-- Pengguna diberi opsi untuk tetap login dengan mencentang kotak ini --}}
                <div style="margin-bottom: 23px;">
                    <label for="remember_me" style="display: inline-flex; align-items: center;">
                        <input
                            id="remember_me"
                            name="remember"
                            type="checkbox"
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                        />
                        <span style="margin-left: 8px; font-size: 14px; color: #4B5563; background-color: white; padding: 3px 8px;">
                            {{ __('Ingatlah saya selama 30 hari') }}
                        </span>
                    </label>
                </div>

                {{-- Link Lupa Kata Sandi --}}
                {{-- Menyediakan link bagi pengguna yang lupa kata sandi --}}
                @if (Route::has('password.request'))
                    <div style="display: flex; justify-content: flex-end; margin-bottom: 16px;">
                        <a
                            href="{{ route('password.request') }}"
                            class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            style="font-size: 14px;"
                        >
                            {{ __('Lupa Kata Sandi?') }}
                        </a>
                    </div>
                @endif

                {{-- Tombol Masuk --}}
                {{-- Tombol yang digunakan untuk melakukan login --}}
                <div style="display: flex; justify-content: center; margin-top: 30px;">
                    <button
                        type="submit"
                        style="width: 350px; height: 50px; background-color: #BE1E2D; color: white; font-weight: 600; font-size: 16px; border-radius: 8px; display: flex; justify-content: center; align-items: center;"
                    >
                        {{ __('Masuk') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
