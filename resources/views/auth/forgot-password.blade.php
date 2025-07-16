<x-guest-layout>
    {{-- Wrapper --}}
    <div style="display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: transparent; width: 100%; margin: 0; padding: 0; position: absolute; top: 0; left: 0;">
        
        {{-- Kotak Forgot Password --}}
        <div style="width: 100%; max-width: 450px; padding: 20px; border: 1px solid #F58220; background-color: #FFF7F0; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">

            {{-- Logo --}}
            <div style="display: flex; justify-content: center; margin-bottom: 24px;">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" style="width: 116px; height: 116px; object-fit: contain;">
            </div>

            {{-- Judul --}}
            <div style="display: flex; justify-content: center; align-items: center; margin-bottom: 23px;">
                <span style="font-size: 18px; font-weight: 550; text-align: center;">Reset Kata Sandi Anda</span>
            </div>

            {{-- Penjelasan --}}
            <div style="margin-bottom: 20px; font-size: 14px; text-align: center; color: #4B5563;">
                {{ __('Lupa kata sandi Anda? Tidak masalah. Masukkan email Anda dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi.') }}
            </div>

            {{-- Status Sesi --}}
            <x-auth-session-status class="mb-4" :status="session('status')" />

            {{-- Form Kirim Email Reset --}}
            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                {{-- Input Email --}}
                <div style="margin-bottom: 23px;">
                    <x-input-label for="email" />
                    <x-text-input
                        id="email"
                        name="email"
                        type="email"
                        :value="old('email')"
                        required
                        autofocus
                        placeholder="Email"
                        class="block mt-1 w-full"
                        style="border: 1px solid #F58220;"
                    />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                {{-- Tombol Kirim Link --}}
                <div style="display: flex; justify-content: center; margin-top: 30px;">
                    <button
                        type="submit"
                        style="width: 350px; height: 50px; background-color: #BE1E2D; color: white; font-weight: 600; font-size: 16px; border-radius: 8px; display: flex; justify-content: center; align-items: center;"
                    >
                        {{ __('Kirim Link Reset') }}
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-guest-layout>
