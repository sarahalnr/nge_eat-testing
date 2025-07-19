<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Query\Builder as QueryBuilder;

class LaporanService
{
    /**
     * Get the total count of items for a specific platform.
     *
     * @param int $platformId 1 for GoFood, 2 for GrabFood, 3 for ShopeeFood
     * @return int
     */
    public function getTotalItem(int $platformId): int
    {
        $table = match ($platformId) {
            1 => 'transaksi_go_food_items',
            2 => 'transaksi_grab_food_items',
            3 => 'transaksi_shopee_food_items',
            default => throw new \InvalidArgumentException("Invalid platform ID: {$platformId}"),
        };

        return DB::table($table)->where('platform_id', $platformId)->sum('jumlah');
    }

    /**
     * Get the total number of transactions across all platforms.
     *
     * @return int
     */
    public function getTotalTransaksi(): int
    {
        return DB::table('transaksi_go_food')->count()
            + DB::table('transaksi_grab_food')->count()
            + DB::table('transaksi_shopee_food')->count();
    }

    /**
     * Get the total revenue across all platforms.
     *
     * @return int|float
     */
    public function getTotalPendapatan(): int|float
    {
        return DB::table('transaksi_go_food')->sum('total')
            + DB::table('transaksi_grab_food')->sum('total')
            + DB::table('transaksi_shopee_food')->sum('total');
    }

    /**
     * Get the total number of transactions for a specific platform.
     *
     * @param int $platformId 1 for GoFood, 2 for GrabFood, 3 for ShopeeFood
     * @return int
     */
    public function getTotalTransaksiPerPlatform(int $platformId): int
    {
        return match ($platformId) {
            1 => DB::table('transaksi_go_food')->count(),
            2 => DB::table('transaksi_grab_food')->count(),
            3 => DB::table('transaksi_shopee_food')->count(),
            default => throw new \InvalidArgumentException("Invalid platform ID: {$platformId}"),
        };
    }

    /**
     * Get the total revenue for a specific platform.
     *
     * @param int $platformId 1 for GoFood, 2 for GrabFood, 3 for ShopeeFood
     * @return int|float
     */
    public function getTotalPendapatanPerPlatform(int $platformId): int|float
    {
        return match ($platformId) {
            1 => DB::table('transaksi_go_food')->sum('total'),
            2 => DB::table('transaksi_grab_food')->sum('total'),
            3 => DB::table('transaksi_shopee_food')->sum('total'),
            default => throw new \InvalidArgumentException("Invalid platform ID: {$platformId}"),
        };
    }

    /**
     * Build the base query for report data, including joins and selects.
     *
     * @param string $tableName The base table name (e.g., 'transaksi_go_food')
     * @return \Illuminate\Database\Query\Builder
     */
    protected function buildBaseReportQuery(string $tableName): QueryBuilder
    {
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

        // This assumes {tableName}_items is the related items table for the platform
        $itemsTable = str_replace('transaksi_', 'transaksi_', $tableName) . '_items';

        return DB::table("{$tableName} as tf")
            ->join("{$itemsTable} as t", 'tf.id', '=', 't.transaksi_id')
            ->join('menus as m', 't.menu_id', '=', 'm.id')
            ->join('categories as c', 'm.category_id', '=', 'c.id')
            ->select($selectColumns)
            ->groupBy($groupColumns);
    }

    /**
     * Apply date filters to a given query builder.
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param string|null $startDate
     * @param string|null $endDate
     * @return \Illuminate\Database\Query\Builder
     */
    public function applyDateFilter(QueryBuilder $query, ?string $startDate, ?string $endDate): QueryBuilder
    {
        if ($startDate) {
            $query->where('tf.tanggal', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('tf.tanggal', '<=', $endDate);
        }
        return $query;
    }

    /**
     * Get filtered report transactions based on platform and date range.
     *
     * @param string|null $platform 'gofood', 'grabfood', 'shopeefood', or null for all
     * @param string|null $startDate
     * @param string|null $endDate
     * @param int $perPage Number of items per page for pagination
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getFilteredTransactions(
        ?string $platform = null,
        ?string $startDate = null,
        ?string $endDate = null,
        int $perPage = 10
    ): LengthAwarePaginator {
        $query = null;

        if ($platform === 'gofood') {
            $query = $this->buildBaseReportQuery('transaksi_go_food');
            $this->applyDateFilter($query, $startDate, $endDate);
        } elseif ($platform === 'grabfood') {
            $query = $this->buildBaseReportQuery('transaksi_grab_food');
            $this->applyDateFilter($query, $startDate, $endDate);
        } elseif ($platform === 'shopeefood') {
            $query = $this->buildBaseReportQuery('transaksi_shopee_food');
            $this->applyDateFilter($query, $startDate, $endDate);
        } else {
            // Union all platforms
            // Note: selectRaw should include all columns being selected in buildBaseReportQuery
            // plus the 'platform' alias.
            $gofood = $this->buildBaseReportQuery('transaksi_go_food')
                ->when($startDate, fn($q) => $q->where('tf.tanggal', '>=', $startDate))
                ->when($endDate, fn($q) => $q->where('tf.tanggal', '<=', $endDate))
                ->selectRaw("tf.id_pesanan, DATE_FORMAT(tf.tanggal, '%Y-%m-%d') as tanggal, DATE_FORMAT(tf.waktu, '%H:%i:%s') as waktu, tf.status, tf.metode_pembayaran, tf.total, GROUP_CONCAT(DISTINCT c.name SEPARATOR ', ') as kategori, 'gofood' as platform");

            $grabfood = $this->buildBaseReportQuery('transaksi_grab_food')
                ->when($startDate, fn($q) => $q->where('tf.tanggal', '>=', $startDate))
                ->when($endDate, fn($q) => $q->where('tf.tanggal', '<=', $endDate))
                ->selectRaw("tf.id_pesanan, DATE_FORMAT(tf.tanggal, '%Y-%m-%d') as tanggal, DATE_FORMAT(tf.waktu, '%H:%i:%s') as waktu, tf.status, tf.metode_pembayaran, tf.total, GROUP_CONCAT(DISTINCT c.name SEPARATOR ', ') as kategori, 'grabfood' as platform");

            $shopeefood = $this->buildBaseReportQuery('transaksi_shopee_food')
                ->when($startDate, fn($q) => $q->where('tf.tanggal', '>=', $startDate))
                ->when($endDate, fn($q) => $q->where('tf.tanggal', '<=', $endDate))
                ->selectRaw("tf.id_pesanan, DATE_FORMAT(tf.tanggal, '%Y-%m-%d') as tanggal, DATE_FORMAT(tf.waktu, '%H:%i:%s') as waktu, tf.status, tf.metode_pembayaran, tf.total, GROUP_CONCAT(DISTINCT c.name SEPARATOR ', ') as kategori, 'shopeefood' as platform");

            $union = $gofood->unionAll($grabfood)->unionAll($shopeefood);

            $query = DB::query()
                ->fromSub($union, 'sub')
                ->orderByDesc(DB::raw("STR_TO_DATE(CONCAT(sub.tanggal, ' ', sub.waktu), '%Y-%m-%d %H:%i:%s')"));
        }

        $filteredTotalQuery = clone $query;
        $totalFiltered = $filteredTotalQuery->get()->sum('total');

        $paginatedResults = $query->paginate($perPage);

        $paginatedResults->setTotalFiltered($totalFiltered);
        $paginatedResults->appends(array_filter([
            'platform' => $platform,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]));

        return $paginatedResults;
    }
}