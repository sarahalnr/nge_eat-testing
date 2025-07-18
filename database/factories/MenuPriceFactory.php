<?php

namespace Database\Factories;

use App\Models\MenuPrice;
use App\Models\Menu;
use App\Models\Platform;
use Illuminate\Database\Eloquent\Factories\Factory;

class MenuPriceFactory extends Factory
{
    protected $model = MenuPrice::class;

    public function definition()
    {
        return [
            'menu_id' => Menu::factory(), // Akan membuat menu baru
            'platform_id' => Platform::factory(), // Akan membuat platform baru
            'price' => $this->faker->randomFloat(2, 10000, 100000), // Harga antara 10.000 sampai 100.000
        ];
    }
}