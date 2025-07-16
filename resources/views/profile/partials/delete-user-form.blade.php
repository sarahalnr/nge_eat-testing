<header>
    {{-- Judul untuk Hapus Akun --}}
    <h2 class="text-lg font-medium text-gray-900">
        {{ __('Hapus Akun') }}  {{-- Menampilkan judul untuk bagian penghapusan akun --}}
    </h2>

    {{-- Penjelasan mengenai penghapusan akun --}}
    <p class="mt-2 text-sm text-gray-600">
        {{ __('Setelah akun Anda dihapus, semua data dan sumber daya terkait akan dihapus secara permanen. Sebelum menghapus akun, pastikan Anda sudah mengunduh data atau informasi yang ingin disimpan.') }}  {{-- Memberikan informasi mengenai konsekuensi penghapusan akun dan pentingnya mengunduh data sebelumnya --}}
    </p>
</header>

{{-- Tombol untuk membuka modal konfirmasi penghapusan akun --}}
<div class="flex justify-center mt-6">
    <x-danger-button
        x-data="{}"
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"  {{-- Membuka modal konfirmasi penghapusan akun saat tombol ditekan --}}
        class="w-full sm:w-auto"
    >
        {{ __('Hapus Akun') }}  {{-- Tombol untuk menghapus akun --}}
    </x-danger-button>
</div>

{{-- Modal untuk konfirmasi penghapusan akun --}}
<x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
    <form method="post" action="{{ route('profile.destroy') }}" class="space-y-6 p-6 bg-white rounded-lg shadow-md">
        @csrf  {{-- Menyertakan token CSRF untuk perlindungan terhadap serangan --}}
        @method('delete')  {{-- Menyertakan method DELETE untuk menghapus akun --}}

        {{-- Judul modal konfirmasi penghapusan akun --}}
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Apakah Anda yakin ingin menghapus akun?') }}  {{-- Menampilkan judul modal untuk konfirmasi penghapusan akun --}}
        </h2>

        {{-- Penjelasan dalam modal tentang penghapusan akun --}}
        <p class="mt-1 text-sm text-gray-600">
            {{ __('Setelah akun Anda dihapus, semua data dan sumber daya terkait akan dihapus secara permanen. Masukkan kata sandi Anda untuk konfirmasi.') }}  {{-- Memberikan informasi tambahan bahwa penghapusan akun bersifat permanen dan meminta kata sandi untuk konfirmasi --}}
        </p>

        {{-- Input untuk memasukkan kata sandi sebagai konfirmasi --}}
        <div class="mt-6">
            <x-input-label for="password" :value="__('Kata Sandi')" />  {{-- Label untuk input kata sandi --}}
            <x-text-input
                id="password"
                name="password"
                type="password"
                class="mt-1 block w-full"
                style="border: 1px solid #F58220; padding: 8px 12px; font-size: 15px;"  {{-- Gaya untuk input kata sandi --}}
                placeholder="Masukkan Kata Sandi"  {{-- Placeholder untuk input kata sandi --}}
                autocomplete="current-password"  {{-- Menyarankan untuk mengisi dengan kata sandi saat ini --}}
            />
            <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />  {{-- Menampilkan error jika ada kesalahan input kata sandi --}}
        </div>

        {{-- Tombol untuk membatalkan atau menghapus akun --}}
        <div class="mt-6 flex justify-end space-x-4">
            <x-secondary-button x-on:click="$dispatch('close')" class="w-full sm:w-auto">
                {{ __('Batal') }}  {{-- Tombol untuk membatalkan dan menutup modal --}}
            </x-secondary-button>

            <x-danger-button class="w-full sm:w-auto">
                {{ __('Hapus Akun') }}  {{-- Tombol untuk mengonfirmasi penghapusan akun --}}
            </x-danger-button>
        </div>
    </form>
</x-modal>
