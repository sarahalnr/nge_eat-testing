<?php

namespace Database\Factories;

use App\Models\GoFood;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Carbon\Carbon;

class GoFoodFactory extends Factory
{
    protected $model = GoFood::class;

    public function definition()
    {
        // Pastikan id_pesanan unik
        $idPesanan = 'GOFO' . strtoupper(Str::random(8));
        while (GoFood::where('id_pesanan', $idPesanan)->exists()) {
            $idPesanan = 'GOFO' . strtoupper(Str::random(8));
        }

        return [
            'id_pesanan' => $idPesanan,
            'tanggal' => $this->faker->date(),
            'waktu' => $this->faker->time('H:i:s'),
            'nama_pelanggan' => $this->faker->name,
            'total' => $this->faker->randomFloat(2, 50000, 500000), // Total transaksi acak
            'metode_pembayaran' => $this->faker->randomElement(['Cash', 'OVO', 'GoPay', 'Dana']),
            'status' => $this->faker->boolean(),
            'jumlah' => $this->faker->numberBetween(1, 10), // Jumlah total item
        ];
    }

    /**
     * Configure the model factory.
     * Setelah GoFood dibuat, bisa secara otomatis membuat GoFoodItem.
     * Ini optional, bisa juga dibuat manual di test.
     */
    public function configure()
    {
        return $this->afterCreating(function (GoFood $goFood) {
            // Contoh: secara otomatis membuat 1 sampai 3 item untuk setiap transaksi
            // Anda bisa menyesuaikan logika ini atau membuat item secara manual di test
            // GoFoodItem::factory()->count($this->faker->numberBetween(1, 3))->create(['transaksi_id' => $goFood->id]);
        });
    }
}