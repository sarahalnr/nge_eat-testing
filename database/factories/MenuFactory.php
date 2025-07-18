<?php

namespace Database\Factories;

use App\Models\Menu;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class MenuFactory extends Factory
{
    protected $model = Menu::class;

    public function definition()
    {
        return [
            'name' => $this->faker->unique()->word . ' ' . $this->faker->randomElement(['Nasi', 'Mie', 'Ayam', 'Kopi', 'Teh']),
            'category_id' => Category::factory(), // Akan membuat kategori baru jika belum ada
            'description' => $this->faker->sentence,
        ];
    }
}