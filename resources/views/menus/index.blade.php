@extends('layouts.navigation')
@vite(['resources/css/app.css', 'resources/js/app.js'])

@section('content') 

<main class="flex-1 px-4 md:px-8 py-6 max-w-screen-xl mx-auto">
    <!-- Breadcrumb -->
    <div class="text-gray-500 mb-4 flex items-center space-x-1">
        <a href="/dashboard" class="text-black font-semibold hover:underline">Dashboard</a>
        <span class="text-[#888]">></span>
        <span class="text-[#888]">Daftar Menu</span>
    </div>

    <!-- Tombol dan Filter -->
    <div
        class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <button
            type="button"
            class="flex items-center gap-2 px-4 py-2 text-white font-medium rounded-lg text-sm md:text-base"
            style="background-color: #F58220;"
            onclick="openModal()">
            <span class="text-lg">+</span>
            <span>Tambah Menu</span>
        </button>

        <!-- Filter Kategori -->
        <form
            method="GET"
            action="{{ route('menus.index') }}"
            class="flex items-center m-0 p-0">
            <select
                name="category_id"
                onchange="this.form.submit()"
                class="appearance-none border-2 border-orange-400 px-4 py-1.5 rounded hover:bg-orange-50 transition cursor-pointer bg-white pr-8 min-h-[38px] leading-none">
                <option value="">Semua Kategori</option>
                @foreach ($categories as $category)
                <option
                    value="{{ $category->id }}"
                    {{ request('category_id') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
                @endforeach
            </select>
        </form>

    </div>

    <!-- Tabel Daftar Menu -->
    <div class="bg-white rounded-xl shadow-md overflow-x-auto">
        <table class="min-w-full table-auto whitespace-nowrap">
            <thead
                class="bg-[#ffd5ab] text-gray-700 text-center text-sm font-semibold select-none">
                <tr>
                    <th class="px-6 py-3">Nama Menu</th>
                    <th class="px-6 py-3">Kategori</th>
                    <th class="px-6 py-3">Harga GoFood</th>
                    <th class="px-6 py-3">Harga GrabFood</th>
                    <th class="px-6 py-3">Harga ShopeeFood</th>
                    <th class="px-6 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white text-gray-700 text-sm text-center">
                @forelse ($menus as $menu)
                <tr class="border-t hover:bg-gray-50">
                    <td class="px-6 py-3 truncate max-w-xs">{{ $menu->name }}</td>
                    <td class="px-6 py-3">{{ $menu->category?->name ?? '-' }}</td>

                    @php $gofood = $menu->prices->firstWhere('platform.name', 'GoFood'); $grabfood =
                    $menu->prices->firstWhere('platform.name', 'GrabFood'); $shopeefood =
                    $menu->prices->firstWhere('platform.name', 'ShopeeFood'); @endphp

                    <td class="px-6 py-3">{{ $gofood ? 'Rp ' . number_format($gofood->price, 0, ',', '.') : '-' }}</td>
                    <td class="px-6 py-3">{{ $grabfood ? 'Rp ' . number_format($grabfood->price, 0, ',', '.') : '-' }}</td>
                    <td class="px-6 py-3">{{ $shopeefood ? 'Rp ' . number_format($shopeefood->price, 0, ',', '.') : '-' }}</td>

                    <td class="px-6 py-3">
                        <div class="flex justify-center space-x-4">
                            <a
                                href="javascript:void(0)"
                                onclick="openEditModal({{ $menu->id }})"
                                class="text-blue-600 hover:text-blue-800"
                                title="Edit">
                                <i class="fas fa-pen-to-square text-lg"></i>
                            </a>
                            <form
                                action="{{ route('menus.destroy', $menu->id) }}"
                                method="POST"
                                onsubmit="return confirm('Yakin ingin menghapus menu ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800" title="Hapus">
                                    <i class="fas fa-trash-alt text-lg"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-6 text-center text-gray-500">Belum ada data menu.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="text-xs text-gray-400 italic px-4 pt-2 sm:hidden">
            Geser ke kanan untuk melihat semua kolom →
        </div>

        <div class="p-4">
            {{ $menus->withQueryString()->links('vendor.pagination.custom') }}
        </div>
    </div>
</main>
</div>

<!-- Modal Tambah Menu -->
<div
id="modalTambahMenu"
class="fixed inset-0 bg-black bg-opacity-40 hidden justify-center items-center z-50">
<div
    class="bg-white rounded-lg shadow-lg w-11/12 sm:w-full sm:max-w-lg p-6 relative">
    <h2 class="text-xl font-semibold mb-4">Tambah Menu Baru</h2>
    <x-menu-form :categories="$categories" :platforms="$platforms"/>
    <button
        onclick="closeModal()"
        class="absolute top-3 right-3 text-gray-600 hover:text-gray-900 text-xl font-bold">&times;</button>
</div>
</div>

<!-- Modal Edit Menu -->
<div
id="modalEditMenu"
class="fixed inset-0 bg-black bg-opacity-40 hidden justify-center items-center z-50">
<div
    id="modalEditMenuContent"
    class="bg-white rounded-lg shadow-lg w-11/12 sm:w-full sm:max-w-lg p-6 relative">
    <button
        onclick="closeEditModal()"
        class="absolute top-3 right-3 text-gray-600 hover:text-gray-900 text-xl font-bold">&times;</button>
</div>
</div>

<script>
function openModal() {
    document
        .getElementById('modalTambahMenu')
        .classList
        .remove('hidden');
    document
        .getElementById('modalTambahMenu')
        .classList
        .add('flex');
}

function closeModal() {
    document
        .getElementById('modalTambahMenu')
        .classList
        .remove('flex');
    document
        .getElementById('modalTambahMenu')
        .classList
        .add('hidden');
}

function openEditModal(menuId) {
    const page = new URLSearchParams(window.location.search).get('page') || 1;
    fetch(`/menus/${menuId}/edit-modal?page=${page}`)
        .then(
            response => response.text()
        )
        .then(html => {
            document
                .getElementById('modalEditMenuContent')
                .innerHTML = html;
            document
                .getElementById('modalEditMenu')
                .classList
                .remove('hidden');
            document
                .getElementById('modalEditMenu')
                .classList
                .add('flex');
        })
        .catch(err => {
            alert('Gagal memuat form edit.');
            console.error(err);
        });
}

function closeEditModal() {
    document
        .getElementById('modalEditMenu')
        .classList
        .remove('flex');
    document
        .getElementById('modalEditMenu')
        .classList
        .add('hidden');
}
</script>
@endsection