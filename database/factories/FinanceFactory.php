<?php

namespace Database\Factories;

use App\Models\Finance;
use Illuminate\Database\Eloquent\Factories\Factory;

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
        return [
            'activite' => fake()->randomElement(['Vente de Marchandises', 'Vente de travaux', 'Ventes de produit',
            'Production stockée ou déstockée', 'Production immobilisée', "Subventions d'exploitation", 'Services extérieurs',
            'Autres services extérieurs', 'Matières premières']),
            'type_activite' => fake()->randomElement(['vente', 'consomation', 'autre', 'production']),
            'date_activite' => fake()->dateTimeBetween('-7 years', 'now'),
            'compte_scf' => fake()->numerify("###"),
            'privision' => rand(65000,89000),
            'realisation' => rand(65000,89000),
            "filiale_id" => rand(1,18)
        ];
    }
}
