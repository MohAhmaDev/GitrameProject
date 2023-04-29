<?php

use App\Http\Controllers\Api\RoleController;
use App\Models\Entreprise;
use App\Models\Filiale;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    $dette = \App\Models\Creance::where([
        ['creditor_type', Filiale::class],
        ['creditor_id', 1]
    ])
    ->orWhere([
        ['debtor_type', Filiale::class],
        ['debtor_id', 1]
    ])->get();

    // $dettes = \App\Models\Dette::where('creditor_id', 1)->where(function($query) {
    //     $query->where('creditor_type', Filiale::class)
    //     ->orWhere('creditor_type', Filiale::class);
    // })->get();

    $filiales = \App\Models\Filiale::query()->where('id', '!=', 1)->get();
    $filiale = \App\Models\Filiale::findOrFail(1);


    return $dette;
});

Route::get('/dettes', function () {
    // Créer une nouvelle dette avec l'ID de l'entreprise endettée
    $dette = \App\Models\Dette::create([
        'intitule_projet' => 'Projet A',
        'num_fact' => '1245',
        'num_situation' => 'S1',
        'date_dettes' => '2023-04-21',
        'montant' => 10000,
        'observations' => 'N/A',
        'debtor_id' => 1,
        'debtor_type' => Entreprise::class,
        'creditor_id' => 10,
        'creditor_type' => Filiale::class,
    ]);

    // Vérifier que la dette a bien été créée
    if ($dette) {
        echo "La dette a été créée avec succès !";
    } else {
        echo "Une erreur s'est produite lors de la création de la dette.";
    }
});


Route::get('/creances', function () {
    // Créer une nouvelle creance avec l'ID de l'entreprise endettée
    $creance = \App\Models\Creance::create([
        'intitule_projet' => 'Projet A',
        'num_fact' => '1245',
        'num_situation' => 'S1',
        'anteriorite_creance' => '2022-05-09',
        'date_creance' => '2023-04-21',
        'montant' => 10000,  
        'observations' => 'N/A',
        'debtor_id' => 1,    
        'debtor_type' => Entreprise::class,
        'creditor_id' => 10,      
        'creditor_type' => Filiale::class,
    ]);                          

    // Vérifier que la creance a bien été créée
    if ($creance) {
        echo "La creance a été créée avec succès !";
    } else {
        echo "Une erreur s'est produite lors de la création de la creance.";
    }

});

Route::get('/stagiares', function () {
    // Créer une nouvelle creance avec l'ID de l'entreprise endettée
    $stagiare = \App\Models\Stagiare::create([
        'filiale_id' => 3,
        'nom' => "nazim",
        'prenom' => "djamal",
        'date_naissance' => "2000-05-06",
        'domaine_formation' => "mecanique",
        'diplomes_obtenues' => "technicien",
        'intitule_formation' => "formation 01",
        'duree_formation' => 4,
        'montant' => 25000,
        'lieu_formation' => "bab ezzouar",
    ]);                          

    // Vérifier que la creance a bien été créée
    if ($stagiare) {
        echo "le stagiare a été créée avec succès !";
    } else {
        echo "Une erreur s'est produite lors de la création du stagiare.";
    }

});