<?php

namespace Database\Factories;

use App\Models\GoFoodItem;
use App\Models\GoFood;
use App\Models\Menu;
use App\Models\Platform;
use App\Models\MenuPrice;
use Illuminate\Database\Eloquent\Factories\Factory;

class GoFoodItemFactory extends Factory
{
    protected $model = GoFoodItem::class;

    public function definition()
    {
        // Pastikan Menu, Platform, dan MenuPrice sudah ada atau dibuat
        $menu = Menu::factory()->create();
        $platform = Platform::factory()->create();
        $menuPrice = MenuPrice::factory()->create([
            'menu_id' => $menu->id,
            'platform_id' => $platform->id,
            'price' => $this->faker->randomFloat(2, 10000, 50000),
        ]);

        return [
            'transaksi_id' => GoFood::factory(), // Akan membuat transaksi GoFood baru jika belum ada
            'menu_id' => $menu->id,
            'menu_price_id' => $menuPrice->id,
            'platform_id' => $platform->id,
            'harga' => $menuPrice->price, // Menggunakan harga dari MenuPrice
            'jumlah' => $this->faker->numberBetween(1, 5),
        ];
    }
}