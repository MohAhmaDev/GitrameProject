<?php

namespace Database\Factories;

use App\Models\Employe;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employe>
 */
class EmployeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "nom" => fake()->name($gender = null),
            "fonction" => fake()->jobTitle(),  
            "prenom" => fake()->firstName(),
            "sexe" => fake()->randomElement(['Homme', 'Femme']),
            "date_naissance" => fake()->dateTimeBetween('1960-01-01', '2004-12-31'),
            "date_recrutement" => fake()->dateTimeBetween('-7 years', 'now'),
            "date_retraite" => fake()->dateTimeBetween('-7 years', 'now'),
            "contract" => fake()->randomElement(['CDI', 'CDD']),
            "temp_occuper" => fake()->randomElement(['Temps plein', 'Temps partiel']),
            "handicape" => fake()->boolean(),
            "categ_sociopro" => fake()->word(5),
            "observation" => fake()->text(),
            "filiale_id" => fake()->numberBetween(1, 18)
        ];
    }
}
