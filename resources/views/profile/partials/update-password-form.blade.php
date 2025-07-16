<section style="padding: 16px;">
    {{-- Header untuk Form Perbarui Kata Sandi --}}
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Perbarui Kata Sandi') }}  {{-- Judul halaman --}}
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            {{ __('Pastikan akun Anda menggunakan kata sandi yang panjang dan acak.') }}  {{-- Penjelasan mengenai pentingnya kata sandi yang kuat --}}
        </p>
    </header>

    {{-- Form untuk mengubah kata sandi --}}
    <form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf  {{-- Menyertakan token CSRF untuk keamanan --}}
        @method('put')  {{-- Menyertakan method PUT untuk pembaruan data --}}

        <div style="display: grid; gap: 16px;">
            {{-- Input untuk Kata Sandi Saat Ini --}}
            <div>
                <x-input-label for="current_password" :value="__('Kata Sandi Saat Ini')" />  {{-- Label untuk input kata sandi saat ini --}}
                <x-text-input 
                    id="current_password" 
                    name="current_password" 
                    type="password" 
                    class="mt-1 block w-full"
                    autocomplete="current-password" 
                    style="border: 1px solid #F58220; padding: 8px 12px; font-size: 15px;" 
                    placeholder="Masukkan Kata Sandi Saat Ini"  {{-- Placeholder untuk input kata sandi saat ini --}}
                />
                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />  {{-- Menampilkan error jika ada kesalahan input --}}
            </div>

            {{-- Input untuk Kata Sandi Baru --}}
            <div>
                <x-input-label for="password" :value="__('Kata Sandi Baru')" />  {{-- Label untuk input kata sandi baru --}}
                <x-text-input 
                    id="password" 
                    name="password" 
                    type="password" 
                    class="mt-1 block w-full"
                    autocomplete="new-password" 
                    style="border: 1px solid #F58220; padding: 8px 12px; font-size: 15px;" 
                    placeholder="Masukkan Kata Sandi Baru"  {{-- Placeholder untuk input kata sandi baru --}}
                />
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />  {{-- Menampilkan error jika ada kesalahan input --}}
            </div>

            {{-- Input untuk Konfirmasi Kata Sandi Baru --}}
            <div>
                <x-input-label for="password_confirmation" :value="__('Konfirmasi Kata Sandi')" />  {{-- Label untuk input konfirmasi kata sandi baru --}}
                <x-text-input 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    type="password" 
                    class="mt-1 block w-full"
                    autocomplete="new-password" 
                    style="border: 1px solid #F58220; padding: 8px 12px; font-size: 15px;" 
                    placeholder="Konfirmasi Kata Sandi Baru"  {{-- Placeholder untuk input konfirmasi kata sandi --}}
                />
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />  {{-- Menampilkan error jika ada kesalahan input --}}
            </div>
        </div>

        {{-- Tombol Simpan --}}
        <div style="display: flex; justify-content: flex-start; margin-top: 24px;">
            <button
                type="submit"
                style="padding: 8px 16px; background-color: #F58220; color: white; font-weight: 600; font-size: 14px; border-radius: 8px;">
                {{ __('Simpan') }}  {{-- Tombol untuk submit form --}}
            </button>
        </div>

        {{-- Menampilkan pesan jika kata sandi berhasil diperbarui --}}
        @if (session('status') === 'password-updated')
            <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-gray-600 text-center mt-2">
                {{ __('Tersimpan.') }}  {{-- Pesan yang muncul setelah kata sandi berhasil diperbarui --}}
            </p>
        @endif
    </form>
</section>
