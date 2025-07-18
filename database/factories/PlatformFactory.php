<?php

namespace Database\Factories;

use App\Models\Platform;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlatformFactory extends Factory
{
    protected $model = Platform::class;

    public function definition()
    {
        return [
            'name' => $this->faker->unique()->company . ' Food', // Contoh: Grab Food, Go Food
        ];
    }
}