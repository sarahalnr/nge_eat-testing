<?php

namespace App\Services;

use App\Models\GoFood;
use App\Models\GoFoodItem;
use App\Models\MenuPrice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GoFoodService
{
    /**
     * Generate a unique order ID.
     * @return string
     */
    public function generateIdPesanan(): string
    {
        do {
            $id = 'GOFO' . strtoupper(Str::random(8));
        } while (GoFood::where('id_pesanan', $id)->exists());

        return $id;
    }

    /**
     * Create a new food transaction.
     * @param array $data
     * @return \App\Models\GoFood
     * @throws \Exception
     */
    public function createTransaction(array $data): GoFood
    {
        DB::beginTransaction();

        try {
            $total = 0;
            $jumlahTotalItem = 0;

            $transaksi = GoFood::create([
                'id_pesanan' => $this->generateIdPesanan(),
                'tanggal' => $data['tanggal'],
                'waktu' => $data['waktu'],
                'nama_pelanggan' => $data['nama_pelanggan'],
                'metode_pembayaran' => $data['metode_pembayaran'],
                'status' => $data['status'] ?? 0,
                'total' => 0,
                'jumlah' => 0,
            ]);

            foreach ($data['items'] as $item) {
                $menuPrice = MenuPrice::where('menu_id', $item['menu_id'])
                    ->where('platform_id', $item['platform_id'])
                    ->first();

                if (!$menuPrice) {
                    throw new \Exception('Harga tidak ditemukan untuk menu_id ' . $item['menu_id'] . ' dan platform_id ' . $item['platform_id']);
                }

                $subtotal = $menuPrice->price * $item['jumlah'];
                $total += $subtotal;
                $jumlahTotalItem += $item['jumlah'];

                GoFoodItem::create([
                    'transaksi_id' => $transaksi->id,
                    'menu_id' => $item['menu_id'],
                    'menu_price_id' => $menuPrice->id,
                    'platform_id' => $item['platform_id'],
                    'harga' => $menuPrice->price,
                    'jumlah' => $item['jumlah'],
                ]);
            }

            $transaksi->update([
                'total' => $total,
                'jumlah' => $jumlahTotalItem,
            ]);

            DB::commit();

            return $transaksi;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update an existing food transaction.
     * @param \App\Models\GoFood $transaksi
     * @param array $data
     * @return \App\Models\GoFood
     * @throws \Exception
     */
    public function updateTransaction(GoFood $transaksi, array $data): GoFood
    {
        DB::beginTransaction();

        try {
            $transaksi->items()->delete(); // Hapus item lama

            $total = 0;
            $jumlahTotalItem = 0;

            foreach ($data['items'] as $item) {
                $menuPrice = MenuPrice::where('menu_id', $item['menu_id'])
                    ->where('platform_id', $item['platform_id'])
                    ->first();

                if (!$menuPrice) {
                    throw new \Exception('Harga tidak ditemukan untuk menu_id ' . $item['menu_id'] . ' dan platform_id ' . $item['platform_id']);
                }

                $subtotal = $menuPrice->price * $item['jumlah'];
                $total += $subtotal;
                $jumlahTotalItem += $item['jumlah'];

                GoFoodItem::create([
                    'transaksi_id' => $transaksi->id,
                    'menu_id' => $item['menu_id'],
                    'menu_price_id' => $menuPrice->id,
                    'platform_id' => $item['platform_id'],
                    'harga' => $menuPrice->price,
                    'jumlah' => $item['jumlah'],
                ]);
            }

            $transaksi->update([
                'tanggal' => $data['tanggal'],
                'waktu' => $data['waktu'],
                'nama_pelanggan' => $data['nama_pelanggan'],
                'metode_pembayaran' => $data['metode_pembayaran'],
                'status' => $data['status'] ?? $transaksi->status,
                'total' => $total,
                'jumlah' => $jumlahTotalItem,
            ]);

            DB::commit();

            return $transaksi;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete a food transaction.
     * @param \App\Models\GoFood $transaksi
     * @return bool
     * @throws \Exception
     */
    public function deleteTransaction(GoFood $transaksi): bool
    {
        DB::beginTransaction();
        try {
            $transaksi->items()->delete();
            $result = $transaksi->delete();
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}