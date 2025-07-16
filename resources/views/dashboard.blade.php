@extends('layouts.navigation')
@vite(['resources/css/app.css', 'resources/js/app.js'])

@section('content')
<div class="p-6 space-y-6 text-sm bg-[#FAFAFA] min-h-screen max-w-screen-xl mx-auto">
    <h1 class="text-xl font-semibold mb-4">Dashboard</h1>

    <!-- Kartu Navigasi -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
        @php
            $cards = [
                ['label' => 'Transaksi GoFood', 'route' => 'gofood.index'],
                ['label' => 'Transaksi GrabFood', 'route' => 'grabfood.index'],
                ['label' => 'Transaksi ShopeeFood', 'route' => 'shopeefood.index'],
                ['label' => 'Laporan Transaksi', 'route' => 'laporan.index', 'icon' => 'laporan.png'],
            ];
        @endphp

        @foreach ($cards as $card)
            <a href="{{ route($card['route']) }}"
                class="bg-white border border-[#F58220] rounded-lg p-4 flex flex-col items-center justify-center space-y-3 shadow-sm hover:shadow-md transition">
                <span class="font-medium text-center">{{ $card['label'] }}</span>
                <img src="{{ asset('images/' . ($card['icon'] ?? 'transaksi.png')) }}" alt="{{ $card['label'] }}" class="w-12 h-12">
                <span class="bg-orange-500 hover:bg-orange-600 text-white text-xs px-4 py-1.5 rounded-full inline-flex items-center justify-between gap-2 shadow transition font-semibold cursor-pointer">
                    <span>Selengkapnya</span>
                    <span class="bg-white rounded-full p-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-orange-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </span>
                </span>
            </a>
        @endforeach
    </div>

    <!-- Grafik Pendapatan -->
    <div class="bg-white border border-[#F58220] rounded-lg p-6 shadow-sm mt-6">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h2 class="text-lg font-bold">Pendapatan</h2>
                <p class="text-sm text-gray-600">Pendapatan store kuliner anda!</p>
            </div>
        </div>

        <div class="w-full overflow-x-auto mb-4">
            <canvas id="pendapatanChart" style="max-width: 1200px; height: 300px; width: 100%;"></canvas>
        </div>

        <p class="text-sm font-semibold">
            Total Pendapatan <span class="text-black text-base">Rp.{{ number_format($totalPendapatan, 0, ',', '.') }}</span>
        </p>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('pendapatanChart').getContext('2d');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: @json($pendapatanBulanan),
                    borderColor: '#F58220',
                    backgroundColor: 'rgba(245, 130, 32, 0.3)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: value => 'Rp ' + value.toLocaleString('id-ID')
                        }
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            color: '#333',
                            font: { size: 14 }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: ctx => 'Rp ' + ctx.parsed.y.toLocaleString('id-ID')
                        }
                    }
                }
            }
        });
    });
</script>

<!-- Responsive Tweaks -->
<style>
    /* Container biar gak lebar kelewatan di desktop, sesuai sama navbar */
    .min-h-screen {
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
        padding-left: 1rem;
        padding-right: 1rem;
    }

    /* Grid kartu responsive */
    @media (max-width: 640px) {
        .inline-flex.items-center.justify-between.gap-2 {
            flex-direction: column;
            gap: 0.25rem;
        }
    }

    /* Chart responsive */
    @media (max-width: 768px) {
        #pendapatanChart {
            width: 100% !important;
            height: 300px !important;
        }
    }

    /* Padding kartu dan ukuran gambar di HP */
    @media (max-width: 480px) {
        .grid > a {
            padding: 1rem !important;
        }

        .grid > a img {
            width: 2.5rem;
            height: 2.5rem;
        }
    }
</style>
@endsection
