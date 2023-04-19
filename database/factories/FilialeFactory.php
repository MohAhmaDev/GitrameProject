<?php

namespace Database\Factories;

use App\Models\Filiale;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Filiale>
 */
class FilialeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $id = 1;
        return [
            'nom_filiale' => 'F-' . str_pad($id++, 2, '0', STR_PAD_LEFT),
            'address' => fake()->address(),
            'ville' => fake()->city()
        ];
    }
}
