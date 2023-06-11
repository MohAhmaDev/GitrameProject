<?php

namespace Database\Factories;
use App\Models\Entreprise;



use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Entreprise>
 */
class EntrepriseFactory extends Factory
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
            'nom_entreprise' => 'F-' . str_pad($id++, 2, '0', STR_PAD_LEFT),
            'groupe' => "gitrame",
            'secteur' => "traveaux public",
            'nationalite' => "algerienne",
            'adresse' => fake()->address(),
            'num_tel_entr' => fake()->unique()->e164PhoneNumber(),
            'adress_emil_entr' => fake()->unique()->email(),
            'status_juridique' => "Entreprise individuelle",
        ];
    }
}
