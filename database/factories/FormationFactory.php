<?php

namespace Database\Factories;

use App\Models\Formation;
use App\Models\Employe;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Formation>
 */
class FormationFactory extends Factory
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
            'employe_id' => Employe::pluck('id')->random(),
            'domaine_formation' => DB::table('ddomaine')
            ->select('Domaine')
            ->distinct()
            ->get()->random()->Domaine,
            'diplomes_obtenues' => fake()->randomElement(["titulaire d'une formation", "perfection"]) , 
            'intitule_formation' => 'Foramtion-' . str_pad($id++, 2, '0', STR_PAD_LEFT), 
            'duree_formation' => rand(10, 120), 
            'montant' => rand(10000, 120000), 
            'lieu_formation' => fake()->randomElement(['alger', 'oran', 'bab ezzouar', 'kouba', 
            'reghia', 'ruiba', 'telemcen', 'Boumerdes']),
        ];
    }
    // php artisan tinker
    // >>use App\Models\Formation
    // >>Formation::factory()->count(10)->create();   

}
