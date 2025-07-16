@extends('layouts.navigation')
@vite(['resources/css/app.css', 'resources/js/app.js'])

@section('content')
<div class="flex min-h-screen bg-[#FAFAFA] text-sm">
    <main class="flex-1 px-8 py-6">

        <!-- Breadcrumb -->
        <div class="text-gray-500 mb-4 flex items-center space-x-1">
            <a href="/dashboard" class="text-black font-semibold hover:underline">Dashboard</a>
            <span class="text-[#888]">></span>
            <span class="text-[#888]">Item Terjual GrabFood</span>
        </div>

        <!-- Kalender Filter -->
        <form method="GET" class="flex justify-between items-center mb-6">
            <div class="flex items-center gap-4">
                <div>
                    <label for="startDateItemTerjual" class="text-sm block mb-1">Dari</label>
                    <input id="startDateItemTerjual" name="tanggal_awal" type="text" class="border px-2 py-1 rounded w-36" value="{{ request('tanggal_awal') }}">
                </div>
                <div>
                    <label for="endDateItemTerjual" class="text-sm block mb-1">Sampai</label>
                    <input id="endDateItemTerjual" name="tanggal_akhir" type="text" class="border px-2 py-1 rounded w-36" value="{{ request('tanggal_akhir') }}">
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                    Terapkan
                </button>
                <a href="{{ url()->current() }}" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm">
                    Reset
                </a>
            </div>
        </form>

        <!-- Tabel Item Terjual -->
        <div class="bg-white rounded-xl shadow-md overflow-x-auto">
            <table class="min-w-full text-sm text-center">
                <thead class="bg-[#ffd5ab] text-gray-700 font-semibold">
                    <tr>
                        <th class="px-6 py-3">Tanggal</th>
                        <th class="px-6 py-3">Kategori</th>
                        <th class="px-6 py-3">Nama Menu</th>
                        <th class="px-6 py-3">Harga</th>
                        <th class="px-6 py-3">Item Terjual</th>
                    </tr>
                </thead>
                <tbody class="bg-white text-gray-700">
                    @forelse ($items as $item)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-6 py-3">{{ $item->tanggal }}</td>
                            <td class="px-6 py-3">{{ $item->kategori }}</td>
                            <td class="px-6 py-3">{{ $item->nama_menu }}</td>
                            <td class="px-6 py-3">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                            <td class="px-6 py-3">{{ $item->item_terjual }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-gray-500 px-6 py-4">
                                @if(request('tanggal_awal') && request('tanggal_akhir'))
                                    Tidak ada item terjual pada tanggal {{ request('tanggal_awal') }} s/d {{ request('tanggal_akhir') }}.
                                @else
                                    Belum ada data item terjual.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                <!-- Pagination -->
                <tfoot>
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center">
                            {{ $items->appends(request()->except('page'))->links('vendor.pagination.custom') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

    </main>
</div>

<!-- Flatpickr -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    flatpickr("#startDateItemTerjual", { dateFormat: "Y-m-d" });
    flatpickr("#endDateItemTerjual", { dateFormat: "Y-m-d" });
</script>
@endsection
