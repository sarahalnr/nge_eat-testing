<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LaporanController extends Controller
{
    public function index(Request $request): View
    {
        $totalGoFood     = $this->getTotalItem(1);
        $totalGrabFood   = $this->getTotalItem(2);
        $totalShopeeFood = $this->getTotalItem(3);
        $totalAll        = $totalGoFood + $totalGrabFood + $totalShopeeFood;

        $percentageGoFood     = $totalAll > 0 ? round(($totalGoFood / $totalAll) * 100) : 0;
        $percentageGrabFood   = $totalAll > 0 ? round(($totalGrabFood / $totalAll) * 100) : 0;
        $percentageShopeeFood = $totalAll > 0 ? round(($totalShopeeFood / $totalAll) * 100) : 0;

        $totalTransaksi  = $this->getTotalTransaksi();
        $totalPendapatan = $this->getTotalPendapatan();

        $totalTransaksiGoFood     = $this->getTotalTransaksiPerPlatform(1);
        $totalTransaksiGrabFood   = $this->getTotalTransaksiPerPlatform(2);
        $totalTransaksiShopeeFood = $this->getTotalTransaksiPerPlatform(3);

        $totalPendapatanGoFood     = $this->getTotalPendapatanPerPlatform(1);
        $totalPendapatanGrabFood   = $this->getTotalPendapatanPerPlatform(2);
        $totalPendapatanShopeeFood = $this->getTotalPendapatanPerPlatform(3);

        $platform     = $request->query('platform');
        $startDate    = $request->query('start_date');
        $endDate      = $request->query('end_date');
        $query        = null;

        $selectColumns = [
            'tf.id_pesanan',
            DB::raw("DATE_FORMAT(tf.tanggal, '%Y-%m-%d') as tanggal"),
            DB::raw("DATE_FORMAT(tf.waktu, '%H:%i:%s') as waktu"),
            'tf.status',
            'tf.metode_pembayaran',
            'tf.total',
            DB::raw('GROUP_CONCAT(DISTINCT c.name SEPARATOR ", ") as kategori')
        ];

        $groupColumns = [
            'tf.id_pesanan',
            'tf.tanggal',
            'tf.waktu',
            'tf.status',
            'tf.metode_pembayaran',
            'tf.total'
        ];

        $applyDateFilter = function ($q) use ($startDate, $endDate) {
            if ($startDate) $q->where('tf.tanggal', '>=', $startDate);
            if ($endDate)   $q->where('tf.tanggal', '<=', $endDate);
        };

        if ($platform === 'gofood') {
            $query = DB::table('transaksi_go_food as tf')
                ->join('transaksi_go_food_items as t', 'tf.id', '=', 't.transaksi_id')
                ->join('menus as m', 't.menu_id', '=', 'm.id')
                ->join('categories as c', 'm.category_id', '=', 'c.id')
                ->select($selectColumns)
                ->groupBy($groupColumns);
            $applyDateFilter($query);
        } elseif ($platform === 'grabfood') {
            $query = DB::table('transaksi_grab_food as tf')
                ->join('transaksi_grab_food_items as t', 'tf.id', '=', 't.transaksi_id')
                ->join('menus as m', 't.menu_id', '=', 'm.id')
                ->join('categories as c', 'm.category_id', '=', 'c.id')
                ->select($selectColumns)
                ->groupBy($groupColumns);
            $applyDateFilter($query);
        } elseif ($platform === 'shopeefood') {
            $query = DB::table('transaksi_shopee_food as tf')
                ->join('transaksi_shopee_food_items as t', 'tf.id', '=', 't.transaksi_id')
                ->join('menus as m', 't.menu_id', '=', 'm.id')
                ->join('categories as c', 'm.category_id', '=', 'c.id')
                ->select($selectColumns)
                ->groupBy($groupColumns);
            $applyDateFilter($query);
        } else {
            $gofood = DB::table('transaksi_go_food as tf')
                ->join('transaksi_go_food_items as t', 'tf.id', '=', 't.transaksi_id')
                ->join('menus as m', 't.menu_id', '=', 'm.id')
                ->join('categories as c', 'm.category_id', '=', 'c.id')
                ->select(array_merge($selectColumns, [DB::raw("'gofood' as platform")]))
                ->when($startDate, fn($q) => $q->where('tf.tanggal', '>=', $startDate))
                ->when($endDate, fn($q) => $q->where('tf.tanggal', '<=', $endDate))
                ->groupBy($groupColumns);

            $grabfood = DB::table('transaksi_grab_food as tf')
                ->join('transaksi_grab_food_items as t', 'tf.id', '=', 't.transaksi_id')
                ->join('menus as m', 't.menu_id', '=', 'm.id')
                ->join('categories as c', 'm.category_id', '=', 'c.id')
                ->select(array_merge($selectColumns, [DB::raw("'grabfood' as platform")]))
                ->when($startDate, fn($q) => $q->where('tf.tanggal', '>=', $startDate))
                ->when($endDate, fn($q) => $q->where('tf.tanggal', '<=', $endDate))
                ->groupBy($groupColumns);

            $shopeefood = DB::table('transaksi_shopee_food as tf')
                ->join('transaksi_shopee_food_items as t', 'tf.id', '=', 't.transaksi_id')
                ->join('menus as m', 't.menu_id', '=', 'm.id')
                ->join('categories as c', 'm.category_id', '=', 'c.id')
                ->select(array_merge($selectColumns, [DB::raw("'shopeefood' as platform")]))
                ->when($startDate, fn($q) => $q->where('tf.tanggal', '>=', $startDate))
                ->when($endDate, fn($q) => $q->where('tf.tanggal', '<=', $endDate))
                ->groupBy($groupColumns);

            $union = $gofood->unionAll($grabfood)->unionAll($shopeefood);

            $query = DB::query()
                ->fromSub($union, 'sub')
                ->orderByDesc(DB::raw("STR_TO_DATE(CONCAT(sub.tanggal, ' ', sub.waktu), '%Y-%m-%d %H:%i:%s')"));
        }

        $totalFiltered = $query->clone()->get()->sum('total');
        $paginated = $query->paginate(10)->appends($request->query());

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
        ]);
    }

    private function getTotalItem(int $platformId): int
    {
        $table = match ($platformId) {
            1 => 'transaksi_go_food_items',
            2 => 'transaksi_grab_food_items',
            3 => 'transaksi_shopee_food_items',
        };

        return DB::table($table)->where('platform_id', $platformId)->sum('jumlah');
    }

    private function getTotalTransaksi(): int
    {
        return DB::table('transaksi_go_food')->count()
            + DB::table('transaksi_grab_food')->count()
            + DB::table('transaksi_shopee_food')->count();
    }

    private function getTotalPendapatan(): int
    {
        return DB::table('transaksi_go_food')->sum('total')
            + DB::table('transaksi_grab_food')->sum('total')
            + DB::table('transaksi_shopee_food')->sum('total');
    }

    private function getTotalTransaksiPerPlatform(int $platformId): int
    {
        return match ($platformId) {
            1 => DB::table('transaksi_go_food')->count(),
            2 => DB::table('transaksi_grab_food')->count(),
            3 => DB::table('transaksi_shopee_food')->count(),
        };
    }

    private function getTotalPendapatanPerPlatform(int $platformId): int
    {
        return match ($platformId) {
            1 => DB::table('transaksi_go_food')->sum('total'),
            2 => DB::table('transaksi_grab_food')->sum('total'),
            3 => DB::table('transaksi_shopee_food')->sum('total'),
        };
    }
}
