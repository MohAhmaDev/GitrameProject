<?php

namespace Database\Factories;

use App\Models\Finance;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Finance>
 */
class FinanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $activite = DB::table('dagregats')
            ->select('agregats')
            ->distinct()
            ->get()->random()->agregats;
        $typeAgregats = DB::table('dagregats')
            ->select('Type_Agregats')
            ->distinct()
            ->where('agregats', $activite)
            ->inRandomOrder()
            ->first()
        ->Type_Agregats;
        return [
            'activite' => $activite,
            'type_activite' => $typeAgregats,
            'date_activite' => fake()->dateTimeBetween('-7 years', 'now'),
            'compte_scf' => fake()->numerify("###"),
            'privision' => rand(65000,89000),
            'realisation' => rand(65000,89000),
            "filiale_id" => rand(1,18)
        ];
    }
}
