<x-guest-layout>
    {{-- Wrapper --}}
    {{-- Wrapper ini bertugas untuk menempatkan kotak register secara terpusat di layar --}}
    <div style="display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: transparent; width: 100%; margin: 0; padding: 0; position: absolute; top: 0; left: 0;">
        
        {{-- Kotak register --}}
        {{-- Kotak register ini memiliki batasan lebar, padding, border dan background warna yang sudah ditentukan --}}
        <div style="width: 100%; max-width: 450px; padding: 20px; border: 1px solid #F58220; background-color: #FFF7F0; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">

            {{-- Logo --}}
            {{-- Bagian ini untuk menampilkan logo aplikasi di atas kotak register --}}
            <div style="display: flex; justify-content: center; margin-bottom: 24px;">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" style="width: 116px; height: 116px; object-fit: contain;">
            </div>

            {{-- Judul --}}
            {{-- Teks ini memberikan petunjuk kepada pengguna untuk melakukan registrasi --}}
            <div style="display: flex; justify-content: center; align-items: center; margin-bottom: 23px;">
                <span style="font-size: 18px; font-weight: 550; text-align: center;">Daftar Akun Baru</span>
            </div>

            {{-- Status sesi: menampilkan status jika ada --}}
            {{-- Jika ada status sesi (misalnya berhasil register), bagian ini akan menampilkannya --}}
            <x-auth-session-status class="mb-4" :status="session('status')" />

            {{-- Form register --}}
            <form method="POST" action="{{ route('register') }}">
                @csrf

                {{-- Input Nama --}}
                <div style="margin-bottom: 23px;">
                    <x-input-label for="name"/>
                    <x-text-input
                        id="name"
                        name="name"
                        type="text"
                        :value="old('name')"
                        required
                        autofocus
                        placeholder="Nama"
                        class="block mt-1 w-full"
                        style="border: 1px solid #F58220;"  
                    />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                {{-- Input Email --}}
                <div style="margin-bottom: 23px;">
                    <x-input-label for="email"/>
                    <x-text-input
                        id="email"
                        name="email"
                        type="email"
                        :value="old('email')"
                        required
                        placeholder="Email"
                        class="block mt-1 w-full"
                        style="border: 1px solid #F58220;"  
                    />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                {{-- Input Password --}}
                <div style="margin-bottom: 23px;">
                    <x-input-label for="password"/>
                    <x-text-input
                        id="password"
                        name="password"
                        type="password"
                        required
                        placeholder="Kata Sandi"
                        class="block mt-1 w-full"
                        style="border: 1px solid #F58220;"  
                    />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                {{-- Confirm Password --}}
                <div style="margin-bottom: 23px;">
                    <x-input-label for="password_confirmation"/>
                    <x-text-input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        required
                        placeholder="Konfirmasi Kata Sandi"
                        class="block mt-1 w-full"
                        style="border: 1px solid #F58220;"  
                    />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                {{-- Tombol Daftar --}}
                <div style="display: flex; justify-content: center; margin-top: 30px;">
                    <button
                        type="submit"
                        style="width: 350px; height: 50px; background-color: #BE1E2D; color: white; font-weight: 600; font-size: 16px; border-radius: 8px; display: flex; justify-content: center; align-items: center;"
                    >
                        {{ __('Daftar') }}
                    </button>
                </div>
            </form>

            {{-- Link sudah terdaftar --}}
            {{-- Memberikan opsi untuk login jika pengguna sudah memiliki akun --}}
            <div style="display: flex; justify-content: center; margin-top: 20px;">
                <a href="{{ route('login') }}" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md">
                    {{ __('Sudah punya akun? Masuk') }}
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>
