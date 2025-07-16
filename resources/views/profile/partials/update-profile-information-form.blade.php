<section style="padding: 16px;">
    <header>
        {{-- Judul untuk Pembaruan Profil --}}
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Perbarui Profil') }}  {{-- Menampilkan judul untuk bagian pembaruan profil akun --}}
        </h2>
        {{-- Penjelasan mengenai pembaruan profil akun --}}
        <p class="mt-1 text-sm text-gray-600">
            {{ __('Perbarui informasi profil akun Anda.') }}  {{-- Memberikan penjelasan tentang tujuan halaman ini --}}
        </p>
    </header>

    {{-- Form untuk memperbarui informasi profil --}}
    <form method="POST" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf  {{-- Menyertakan token CSRF untuk perlindungan terhadap serangan --}}
        @method('patch')  {{-- Menyertakan method PATCH untuk pembaruan data profil --}}

        <div style="display: grid; gap: 16px;">
            {{-- Input untuk Nama Pengguna --}}
            <div>
                <x-input-label for="name" :value="__('Nama')" />  {{-- Label untuk input nama --}}
                <x-text-input 
                    id="name" 
                    name="name" 
                    type="text" 
                    class="mt-1 block w-full"
                    value="{{ old('name', auth()->user()->name) }}"  {{-- Menampilkan nilai lama atau nama pengguna saat ini --}}
                    style="border: 1px solid #F58220; padding: 8px 12px; font-size: 15px;"  {{-- Gaya input untuk nama --}}
                />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />  {{-- Menampilkan error jika ada masalah dengan input nama --}}
            </div>

            {{-- Input untuk Email Pengguna --}}
            <div>
                <x-input-label for="email" :value="__('Email')" />  {{-- Label untuk input email --}}
                <x-text-input 
                    id="email" 
                    name="email" 
                    type="email" 
                    class="mt-1 block w-full"
                    value="{{ old('email', auth()->user()->email) }}"  {{-- Menampilkan nilai lama atau email pengguna saat ini --}}
                    style="border: 1px solid #F58220; padding: 8px 12px; font-size: 15px;"  {{-- Gaya input untuk email --}}
                />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />  {{-- Menampilkan error jika ada masalah dengan input email --}}
            </div>
        </div>

        {{-- Tombol Simpan untuk menyimpan perubahan --}}
        <div style="display: flex; justify-content: flex-start; margin-top: 24px;">
            <button
                type="submit"
                style="padding: 8px 16px; background-color: #F58220; color: white; font-weight: 600; font-size: 14px; border-radius: 8px;">
                {{ __('Simpan') }}  {{-- Tombol untuk menyimpan perubahan profil --}}
            </button>
        </div>

        {{-- Menampilkan pesan bahwa profil berhasil diperbarui --}}
        @if (session('status') === 'profile-updated')
            <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-gray-600 text-center mt-2">
                {{ __('Tersimpan.') }}  {{-- Menampilkan pesan sukses setelah profil berhasil diperbarui --}}
            </p>
        @endif
    </form>
</section>
