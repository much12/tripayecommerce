<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(3, true);

        return [
            'sku' => 'TRP-' . strtoupper(Str::random(6)),
            'name' => ucwords($name),
            'price' => fake()->numberBetween(50, 999) * 1000,
            'reference' => 'REF-' . strtoupper(Str::random(8)),
        ];
    }
}
