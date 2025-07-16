@extends('layouts.navigation')
@vite(['resources/css/app.css', 'resources/js/app.js'])

@section('content')
<main class="flex-1 px-8 py-6">

    <!-- Breadcrumb -->
    <div class="text-gray-500 mb-4 flex items-center space-x-1">
        <a href="/dashboard" class="text-black font-semibold hover:underline">Dashboard</a>
        <span class="text-[#888]">></span>
        <span class="text-[#888]">Daftar Kategori</span>
    </div>

    <!-- Tombol Tambah -->
    <div class="mb-6">
        <button
            type="button"
            class="flex items-center gap-2 px-3 py-1 text-white font-medium rounded-lg"
            style="background-color: #F58220;"
            onclick="openModal()">
            <span class="text-lg">+</span>
            <span>Tambah Kategori</span>
        </button>
    </div>

    <!-- Tabel Daftar Kategori -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto">
                <thead class="bg-[#ffd5ab] text-gray-700 text-center text-sm font-semibold select-none">
                    <tr>
                        <th class="px-6 py-3">Nama Kategori</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white text-gray-700 text-sm text-center">
                    @foreach($categories as $category)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-6 py-3">{{ $category->name }}</td>
                        <td class="px-6 py-3">
                            <div class="flex justify-center space-x-4">
                                <a href="javascript:void(0)" onclick="openEditModal({{ $category->id }})" class="text-blue-600 hover:text-blue-800" title="Edit">
                                    <i class="fas fa-pen-to-square text-lg"></i>
                                </a>
                                <form action="{{ route('kategori.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus kategori ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Hapus">
                                        <i class="fas fa-trash-alt text-lg"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4">
            {{ $categories->links('vendor.pagination.custom') }}
        </div>
    </div>
</main>

<!-- Modal Tambah Kategori -->
<div id="modalTambahKategori" class="fixed inset-0 bg-black bg-opacity-40 hidden justify-center items-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
        <h2 class="text-xl font-semibold mb-4">Tambah Kategori Baru</h2>
        <form action="{{ route('kategori.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="nama_kategori" class="block text-sm font-medium text-gray-700">Nama Kategori</label>
                <input
                type="text"
                id="nama_kategori"
                name="name"
                class="mt-1 block w-full border border-[#F58220] rounded-md shadow-sm px-3 py-2 focus:ring-[#F58220] focus:border-[#F58220] text-sm"
                required>
            </div>

            <div class="flex justify-end">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded text-sm mr-2">Batal</button>
                <button type="submit" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded text-sm">Simpan</button>
            </div>
        </form>
        <button onclick="closeModal()" class="absolute top-3 right-3 text-gray-600 hover:text-gray-900 text-xl font-bold">&times;</button>
    </div>
</div>

<!-- Modal Edit Kategori -->
<div id="modalEditKategori" class="fixed inset-0 bg-black bg-opacity-40 hidden justify-center items-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
        <h2 class="text-xl font-semibold mb-4">Edit Kategori</h2>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="edit_nama_kategori" class="block text-sm font-medium text-gray-700">Nama Kategori</label>
                <input
                    type="text"
                    id="edit_nama_kategori"
                    name="name"
                    class="mt-1 block w-full border border-[#F58220] rounded-md shadow-sm px-3 py-2 focus:ring-[#F58220] focus:border-[#F58220] text-sm"
                    required>
            </div>

            <div class="flex justify-end">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded text-sm mr-2">Batal</button>
                <button type="submit" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded text-sm">Update</button>
            </div>
        </form>
        <button onclick="closeEditModal()" class="absolute top-3 right-3 text-gray-600 hover:text-gray-900 text-xl font-bold">&times;</button>
    </div>
</div>

<script>
    function openModal() {
        document.getElementById('modalTambahKategori').classList.remove('hidden');
        document.getElementById('modalTambahKategori').classList.add('flex');
    }

    function closeModal() {
        document.getElementById('modalTambahKategori').classList.remove('flex');
        document.getElementById('modalTambahKategori').classList.add('hidden');
    }

    function openEditModal(kategoriId) {
        fetch(`/kategori/${kategoriId}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('edit_nama_kategori').value = data.name;
            const page = new URLSearchParams(window.location.search).get('page') || 1;
            document.getElementById('editForm').action = `/kategori/${kategoriId}?page=${page}`;
            document.getElementById('modalEditKategori').classList.remove('hidden');
            document.getElementById('modalEditKategori').classList.add('flex');
        });
    }

    function closeEditModal() {
        document.getElementById('modalEditKategori').classList.remove('flex');
        document.getElementById('modalEditKategori').classList.add('hidden');
    }
</script>
@endsection
