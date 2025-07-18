<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Services\LaporanService; // Pastikan ini mengacu pada LaporanService Anda

class LaporanController extends Controller
{
    protected $laporanService;

    // Constructor Injection untuk LaporanService
    public function __construct(LaporanService $laporanService)
    {
        $this->laporanService = $laporanService;
    }

    public function index(Request $request): View
    {
        // Mengambil data statistik dari LaporanService
        $totalGoFood          = $this->laporanService->getTotalItem(1);
        $totalGrabFood        = $this->laporanService->getTotalItem(2);
        $totalShopeeFood      = $this->laporanService->getTotalItem(3);
        $totalAll             = $totalGoFood + $totalGrabFood + $totalShopeeFood;

        $percentageGoFood     = $totalAll > 0 ? round(($totalGoFood / $totalAll) * 100) : 0;
        $percentageGrabFood   = $totalAll > 0 ? round(($totalGrabFood / $totalAll) * 100) : 0;
        $percentageShopeeFood = $totalAll > 0 ? round(($totalShopeeFood / $totalAll) * 100) : 0;

        $totalTransaksi             = $this->laporanService->getTotalTransaksi();
        $totalPendapatan            = $this->laporanService->getTotalPendapatan();

        $totalTransaksiGoFood       = $this->laporanService->getTotalTransaksiPerPlatform(1);
        $totalTransaksiGrabFood     = $this->laporanService->getTotalTransaksiPerPlatform(2);
        $totalTransaksiShopeeFood   = $this->laporanService->getTotalTransaksiPerPlatform(3);

        $totalPendapatanGoFood      = $this->laporanService->getTotalPendapatanPerPlatform(1);
        $totalPendapatanGrabFood    = $this->laporanService->getTotalPendapatanPerPlatform(2);
        $totalPendapatanShopeeFood  = $this->laporanService->getTotalPendapatanPerPlatform(3);

        // Mengambil parameter filter dari request
        $platform = $request->query('platform');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        // Mengambil data transaksi yang difilter dan dipaginasi dari LaporanService
        $paginated = $this->laporanService->getFilteredTransactions($platform, $startDate, $endDate, 10);
        $totalFiltered = $paginated->totalFiltered ?? 0;

        return view('laporan.index', [
            'totalGoFood' => $totalGoFood,
            'totalGrabFood' => $totalGrabFood,
            'totalShopeeFood' => $totalShopeeFood,
            'totalAll' => $totalAll,
            'percentageGoFood' => $percentageGoFood,
            'percentageGrabFood' => $percentageGrabFood,
            'percentageShopeeFood' => $percentageShopeeFood,
            'totalTransaksi' => $totalTransaksi,
            'totalPendapatan' => $totalPendapatan,
            'totalTransaksiGoFood' => $totalTransaksiGoFood,
            'totalTransaksiGrabFood' => $totalTransaksiGrabFood,
            'totalTransaksiShopeeFood' => $totalTransaksiShopeeFood,
            'totalPendapatanGoFood' => $totalPendapatanGoFood,
            'totalPendapatanGrabFood' => $totalPendapatanGrabFood,
            'totalPendapatanShopeeFood' => $totalPendapatanShopeeFood,
            'transaksi' => $paginated,
            'totalFiltered' => $totalFiltered,
            // Penting: Anda juga meneruskan kembali filter ke view jika view menggunakannya
            'platform' => $platform,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }
}