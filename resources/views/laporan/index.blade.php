{{-- filepath: d:\laravel\nge_eat\resources\views\laporan\index.blade.php --}}
@extends('layouts.navigation')
@vite(['resources/css/app.css', 'resources/js/app.js'])

@section('content')
<div class="flex min-h-screen bg-[#FAFAFA] text-sm">
    <main class="flex-1 px-8 py-6">

        <!-- Breadcrumb -->
        <nav class="text-gray-500 mb-4 flex items-center space-x-1" aria-label="Breadcrumb">
            <a href="/dashboard" class="text-black font-semibold hover:underline">Dashboard</a>
            <span class="text-[#888]">></span>
            <span class="text-[#888]">Laporan Transaksi</span>
        </nav>

        <!-- Info Cards -->
        <div class="flex gap-5 flex-wrap mb-4">
            <!-- Card 1: Item Terjual -->
            <div class="flex-1 min-w-[280px] max-w-[350px] relative">
                <div class="p-5 h-full rounded-lg bg-white border border-[#FCD9A3] shadow-sm">
                    <div class="bg-white shadow rounded-xl p-5">
                        <div class="text-[#1F2937] text-lg font-semibold mb-1 flex items-center justify-between">
                            <span>Item Terjual</span>
                            <!-- Tooltip Icon -->
                            <div class="relative group cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 hover:text-orange-500 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 18a6 6 0 110-12 6 6 0 010 12z"/>
                                </svg>
                                <div class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 w-48 bg-gray-800 text-white text-xs rounded px-3 py-2 opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity z-50">
                                    <strong>Rincian:</strong><br>
                                    • GoFood: {{ $totalGoFood }} item<br>
                                    • GrabFood: {{ $totalGrabFood }} item<br>
                                    • ShopeeFood: {{ $totalShopeeFood }} item
                                </div>
                            </div>
                        </div>
                        <div class="text-sm text-gray-500 mb-3">Bulan Ini</div>
                        <div class="h-[1px] bg-[#FCD9A3] mb-4"></div>
                        <div class="flex items-baseline text-[#1F2937] mb-2">
                            <div class="text-4xl font-extrabold leading-none">{{ $totalAll }}</div>
                            <div class="text-base ml-2">Item</div>
                        </div>
                        <!-- Progress Bar Gabungan -->
                        <div class="mb-2">
                            <div class="text-sm text-gray-600 mb-1 flex justify-between">
                                <span>Distribusi Pesanan</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden flex">
                                <div class="bg-orange-400 h-4" style="width: {{ $percentageGoFood }}%"></div>
                                <div class="bg-green-400 h-4" style="width: {{ $percentageGrabFood }}%"></div>
                                <div class="bg-pink-400 h-4" style="width: {{ $percentageShopeeFood }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
           <!-- Card 2: Jumlah Transaksi -->
            <div class="flex-1 min-w-[280px] max-w-[350px] relative">
                <div class="p-5 h-full rounded-lg bg-white border border-[#FCD9A3] shadow-sm">
                    <div class="bg-white shadow rounded-xl p-5">
                        <div class="text-[#1F2937] text-lg font-semibold mb-1 flex items-center justify-between">
                            <span>Jumlah Transaksi</span>
                            <!-- Tooltip Icon -->
                            <div class="relative group cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 hover:text-orange-500 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 18a6 6 0 110-12 6 6 0 010 12z"/>
                                </svg>
                                <div class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 w-48 bg-gray-800 text-white text-xs rounded px-3 py-2 opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity z-50">
                                    <strong>Rincian:</strong><br>
                                    • GoFood: {{ $totalTransaksiGoFood }} transaksi<br>
                                    • GrabFood: {{ $totalTransaksiGrabFood }} transaksi<br>
                                    • ShopeeFood: {{ $totalTransaksiShopeeFood }} transaksi
                                </div>
                            </div>
                        </div>
                        <div class="text-sm text-gray-500 mb-3">Bulan Ini</div>
                        <div class="h-[1px] bg-[#FCD9A3] mb-4"></div>
                        <div class="flex items-baseline text-[#1F2937] mb-2">
                            <div class="text-4xl font-extrabold leading-none">{{ $totalTransaksi }}</div>
                            <div class="text-base ml-2">Transaksi</div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Card 3: Total Keseluruhan -->
            <div class="flex-1 min-w-[280px] max-w-[350px] relative">
                <div class="p-5 h-full rounded-lg bg-white border border-[#FCD9A3] shadow-sm">
                    <div class="bg-white shadow rounded-xl p-5">
                        <div class="text-[#1F2937] text-lg font-semibold mb-1 flex items-center justify-between">
                            <span>Total Keseluruhan</span>
                            <!-- Tooltip Icon -->
                            <div class="relative group cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 hover:text-orange-500 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 18a6 6 0 110-12 6 6 0 010 12z"/>
                                </svg>
                                <div class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 w-52 bg-gray-800 text-white text-xs rounded px-3 py-2 opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity z-50">
                                    <strong>Rincian:</strong><br>
                                    • GoFood: Rp {{ number_format($totalPendapatanGoFood, 0, ',', '.') }}<br>
                                    • GrabFood: Rp {{ number_format($totalPendapatanGrabFood, 0, ',', '.') }}<br>
                                    • ShopeeFood: Rp {{ number_format($totalPendapatanShopeeFood, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                        <div class="text-sm text-gray-500 mb-3">Bulan Ini</div>
                        <div class="h-[1px] bg-[#FCD9A3] mb-4"></div>
                        <div class="flex items-baseline text-[#1F2937] mb-2">
                            <div class="text-2xl sm:text-4xl font-extrabold leading-none">
                                Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

       <!-- Laporan Transaksi Card -->
        <div class="p-4 bg-white rounded-2xl shadow-md mb-8 w-full">
            <div class="flex flex-col gap-4 mb-4 w-full">
                <!-- Judul -->
                <h5 class="text-[#1F2937] text-2xl font-bold whitespace-nowrap">
                    Laporan Transaksi
                </h5>

                <!-- Filter dan Tombol -->
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 w-full flex-wrap">
                    <!-- Kiri: Form Filter -->
                    <form method="GET" action="{{ url()->current() }}"
                        class="flex flex-wrap sm:flex-nowrap items-center gap-2 w-full lg:max-w-[600px]">
                        @include('components.kalender-laporan')

                        <button type="submit"
                            class="bg-orange-500 text-white px-4 h-[38px] rounded text-sm hover:bg-orange-600 transition whitespace-nowrap">
                            Filter
                        </button>

                        <a href="{{ route('laporan.index') }}"
                            class="bg-red-500 hover:bg-red-600 text-white px-4 h-[38px] rounded text-sm whitespace-nowrap flex items-center justify-center">
                            Reset
                        </a>

                        @if(request('platform'))
                            <input type="hidden" name="platform" value="{{ request('platform') }}">
                        @endif
                    </form>

                    <!-- Kanan: Unduh + Dropdown -->
                    <div class="flex items-center gap-2 flex-shrink-0 w-full lg:w-auto justify-end">
                        <button onclick="openModal()" style="border: 2px solid #F58220;"
                            class="h-[38px] flex items-center justify-center px-4 rounded hover:bg-orange-50 transition whitespace-nowrap text-sm">
                            <i class="fas fa-file-download mr-2 text-orange-500"></i> Unduh Laporan
                        </button>

                        <form method="GET" action="{{ url()->current() }}" class="m-0 p-0">
                            <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                            <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                            <select name="platform" onchange="this.form.submit()"
                                class="appearance-none border-2 border-orange-400 px-4 h-[38px] rounded hover:bg-orange-50 transition cursor-pointer bg-white pr-8 text-sm leading-none whitespace-nowrap">
                                <option value="">Filter</option>
                                <option value="gofood" {{ request('platform') === 'gofood' ? 'selected' : '' }}>GoFood</option>
                                <option value="grabfood" {{ request('platform') === 'grabfood' ? 'selected' : '' }}>GrabFood</option>
                                <option value="shopeefood" {{ request('platform') === 'shopeefood' ? 'selected' : '' }}>ShopeeFood</option>
                            </select>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Tabel Transaksi -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <table class="min-w-full text-sm text-gray-700">
                    <thead class="bg-[#ffd5ab] text-center font-semibold select-none">
                        <tr>
                            <th class="px-4 py-3">Kategori</th>
                            <th class="px-4 py-3">ID Pesanan</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Waktu</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Metode Pembayaran</th>
                            <th class="px-4 py-3">Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse ($transaksi as $t)
                            <tr class="border-t hover:bg-gray-50 text-center" data-tanggal="{{ $t->tanggal }}">
                                <td class="px-4 py-3">{{ $t->kategori }}</td>
                                <td class="px-4 py-3">{{ $t->id_pesanan }}</td>
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($t->tanggal)->format('d-m-Y') }}</td>
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($t->waktu)->format('H:i') }}</td>
                                <td class="px-4 py-3 text-green-600 font-medium">{{ $t->status ? 'Sukses' : 'Gagal' }}</td>
                                <td class="px-4 py-3">{{ $t->metode_pembayaran }}</td>
                                <td class="px-4 py-3 font-semibold">Rp {{ number_format($t->total, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-6 text-gray-400 italic text-center">
                                    Tidak ada data transaksi.
                                </td>
                            </tr>
                        @endforelse
                        @if ($transaksi->count())
                            <tr class="font-semibold bg-[#fef6e4] border-t-2 border-[#fcd9a3]">
                                <td colspan="6" class="text-right px-4 py-3">Total</td>
                                <td class="px-4 py-3 text-green-700">Rp {{ number_format($totalFiltered, 0, ',', '.') }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4">
                {{ $transaksi->appends(request()->query())->links('vendor.pagination.custom') }}
            </div>
        </div>
    </main>
</div>

@include('components.download-modal')

<script>
    function openModal() {
        document.getElementById('DownloadModal').classList.remove('hidden');
    }
    function closeModal() {
        document.getElementById('DownloadModal').classList.add('hidden');
    }
    document.getElementById('closeDownloadModal').addEventListener('click', closeModal);

    function openTransactionModal() {
        document.getElementById('transactionDetailModal').classList.remove('hidden');
    }
    function closeTransactionModal() {
        document.getElementById('transactionDetailModal').classList.add('hidden');
    }
    window.addEventListener('click', function (e) {
        const modal = document.getElementById('transactionDetailModal');
        if (e.target === modal) {
            closeTransactionModal();
        }
        const hapusModal = document.getElementById('openHapusModal');
        if (e.target === hapusModal) {
            closeHapusModal();
        }
    });
    document.querySelectorAll('.btn-detail').forEach(button => {
        button.addEventListener('click', openTransactionModal);
    });
    document.querySelectorAll('.btn-hapus').forEach(button => {
        button.addEventListener('click', openHapusModal);
    });
    function openHapusModal() {
        document.getElementById('openHapusModal').classList.remove('hidden');
    }
    function closeHapusModal() {
        document.getElementById('openHapusModal').classList.add('hidden');
    }
</script>
@endsection
