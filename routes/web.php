<?php

use App\Http\Controllers\Api\RoleController;
use App\Models\Employe;
use App\Models\Entreprise;
use App\Models\Filiale;
use App\Models\User;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\Cast\Object_;
use PhpParser\Node\Stmt\Echo_;

use function PHPSTORM_META\map;
use function PHPUnit\Framework\isEmpty;

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
    $filiale = \App\Models\Filiale::where('id', 1)->firstOrFail();
    $user = User::find(8);

    $agregets = DB::select('select agregats, ID_Agregats from dagregats where Type_Agregats = ?', ['autre']);
    $filiales = Filiale::query()->where('id', '!=', 1)->get();
        

    $agregets = DB::table('dagregats')
    ->selectRaw('agregats, ID_Agregats')
    ->where('Type_Agregats', '=', 'autre')
    ->get();

    $results = $agregets->map(function ($agreget) {
        return [
            'id' => $agreget->ID_Agregats,
            'agreget' => $agreget->agregats
        ];
    });

    

    // return response([
    //     'agreget' => $results
    // ]);
    // $formation = \App\Models\Employe::where('filiale_id', 1)->first()->id;
    // return $formation;

    $employe_name = Employe::find(31)->nom;

    return $employe_name;

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


Route::get('/addAge', function () {
    // $employes = \App\Models\filiale::get();

    $get_employes = DB::table('employes')->get();
    $get_transform = DB::table('dage')->get();
    $getting = (count($get_employes) - count($get_transform));
    if ($getting > 0) {
        $employes = \App\Models\filiale::orderBy('id', 'DESC')->get()->take($getting);
    } else {
        return "non update cause";
    }

    // Transformation des données
    $AgeTransform = $employes->map(function ($filiale) {
        $dateOfBirth = $filiale->date_naissance;
        $today = date("Y-m-d");
        $age = date_diff(date_create($dateOfBirth), date_create($today))->format('%y');
        switch ($age) {
            case ($age < 25):
                $tranche_Age = "21-25 ans";
                break;
            case ($age < 30):
                $tranche_Age = "26-30 ans";
                break;
            case ($age < 35):
                $tranche_Age = "31-35 ans";
                break;
            case ($age < 40):
                $tranche_Age = "36-40 ans";
                break;
            case ($age < 45):
                $tranche_Age = "41-45 ans";
                break;
            case ($age < 46):
                $tranche_Age = "46-50 ans";
                break;
            case ($age < 55):
                $tranche_Age = "51-55 ans";
                break;
            case ($age < 60):
                $tranche_Age = "56-60 ans";
                break;
            case ($age < 65):
                $tranche_Age = "61-65 ans";
                break;
            default:
                $tranche_Age = "65 ans et plus";
        }
        return [
            'Tranche_Age' => ucfirst(strtolower($tranche_Age)),
        ];
    });

    // Chargement des données transformées dans la destination
    $query = DB::table('dage')->insert($AgeTransform->toArray());
    if ($query) {
        echo "Les données ont bien été transformées.";
    } else {
        echo "Une erreur s'est produite lors du chargement des données.";
    }
});

Route::get('addSexe', function () {
    $get_employes = DB::table('employes')->get();
    $get_transform = DB::table('dsexe')->get();
    $getting = (count($get_employes) - count($get_transform));
    if ($getting > 0) {
        $employes = \App\Models\filiale::orderBy('id', 'DESC')->get()->take($getting);
    } else {
        return "non update cause";
    }

    $sexTransform = $employes->map(function ($filiale) {
        $sexe = $filiale->sexe;
        return [
            'Sexe' => ucfirst(strtolower($sexe)),
        ];
    });

    // Chargement des données transformées dans la destination
    $query = DB::table('dsexe')->insert($sexTransform->toArray());
    if ($query) {
        echo "Les données ont bien été transformées.";
    } else {
        echo "Une erreur s'est produite lors du chargement des données.";
    }
});

Route::get('addFonction', function () {
    $get_employes = DB::table('employes')->get();
    $get_transform = DB::table('dfonction')->get();
    $getting = (count($get_employes) - count($get_transform));
    if ($getting > 0) {
        $employes = \App\Models\filiale::orderBy('id', 'DESC')->get()->take($getting);
    } else {
        return "non update cause";
    }

    $FonctionTransform = $employes->map(function ($filiale) {
        $fonction = $filiale->fonction;
        return [
            'Fonction' => ucfirst(strtolower($fonction)),
        ];
    });

    // Chargement des données transformées dans la destination
    $query = DB::table('dfonction')->insert($FonctionTransform->toArray());
    if ($query) {
        echo "Les données ont bien été transformées.";
    } else {
        echo "Une erreur s'est produite lors du chargement des données.";
    }
});

Route::get('addFonction', function () {
    $get_employes = DB::table('employes')->distinct()->pluck('fonction');
    $get_transform = DB::table('dfonction')->get();
    $getting = (count($get_employes) - count($get_transform));
    if ($getting <= 0) {
        DB::table('dfonction')->truncate();
    }

    $FonctionTransform = $get_employes->map(function ($value) {
        return [
            'Fonction' => ucfirst(strtolower($value)),
        ];
    });

    // Chargement des données transformées dans la destination
    $query = DB::table('dfonction')->insert($FonctionTransform->toArray());
    if ($query) {
        echo "Les données ont bien été transformées.";
    } else {
        echo "Une erreur s'est produite lors du chargement des données.";
    }
});



Route::get('AddScociopro', function () {
    $get_employes = DB::table('employes')->distinct()->pluck('categ_sociopro');
    $get_transform = DB::table('dscociopro')->get();
    $getting = (count($get_employes) - count($get_transform));
    if ($getting <= 0) {
        return "non update cause";
    }

    $SocioproTransform = $get_employes->map(function ($value) {
        return [
            'Scocipro' => ucfirst(strtolower($value)),
        ];
    });

    // Chargement des données transformées dans la destination
    $query = DB::table('dscociopro')->insert($SocioproTransform->toArray());
    if ($query) {
        echo "Les données ont bien été transformées.";
    } else {
        echo "Une erreur s'est produite lors du chargement des données.";
    }

});



// Route::get('addPosition', function () {
//     $get_employes = DB::table('employes')->get();
//     $get_transform = DB::table('dposition')->get();
//     $getting = (count($get_employes) - count($get_transform));
//     if ($getting > 0) {
//         $employes = \App\Models\filiale::orderBy('id', 'DESC')->get()->take($getting);
//     } else {
//         return "non update cause";
//     }

//     $FonctionTransform = $employes->map(function ($filiale) {
//         $value = $filiale->date_retraite;
//         if (is_null($value)) {
//             $date_retraite = $value;
//         } else {
//             $date = date_format(date_create($value),"Y-m-d");
//             $date_retraite = ucfirst(strtolower(Date($date)));
//         }
//         return [
//             'date_retarite' => $date_retraite,
//         ];
//     });

//     // Chargement des données transformées dans la destination
//     $query = DB::table('dposition')->insert($FonctionTransform->toArray());
//     if ($query) {
//         echo "Les données ont bien été transformées.";                             
//     } else { 
//         echo "Une erreur s'est produite lors du chargement des données.";              
//     }

//     // return $FonctionTransform;
// });


Route::get('addAgregat', function () {
    $get_employes = DB::table('employes')->get();
    $get_transform = DB::table('ddate_recrutement')->get();
    $getting = (count($get_employes) - count($get_transform));
    if ($getting > 0) {
        $employes = \App\Models\filiale::orderBy('id', 'DESC')->get()->take($getting);
    } else {
        return "non update cause";
    }

    $FonctionTransform = $employes->map(function ($filiale) {
        $value = $filiale->date_recrutement;
        return [
            'Date_Recrutement' => $value,
        ];
    });

    // Chargement des données transformées dans la destination
    $query = DB::table('ddate_recrutement')->insert($FonctionTransform->toArray());
    if ($query) {
        echo "Les données ont bien été transformées.";                             
    } else { 
        echo "Une erreur s'est produite lors du chargement des données.";              
    }
    // return $FonctionTransform;
});

// Route::get('addDateRecrutement', function () {
//     $get_employes = DB::table('employes')->get();
//     $get_transform = DB::table('ddate_recrutement')->get();
//     $getting = (count($get_employes) - count($get_transform));
//     if ($getting > 0) {
//         $employes = \App\Models\filiale::orderBy('id', 'DESC')->get()->take($getting);
//     } else {
//         return "non update cause";
//     }

//     $FonctionTransform = $employes->map(function ($filiale) {
//         $value = $filiale->date_recrutement;
//         return [
//             'Date_Recrutement' => $value,
//         ];
//     });

//     // Chargement des données transformées dans la destination
//     $query = DB::table('ddate_recrutement')->insert($FonctionTransform->toArray());
//     if ($query) {
//         echo "Les données ont bien été transformées.";                             
//     } else { 
//         echo "Une erreur s'est produite lors du chargement des données.";              
//     }
//     // return $FonctionTransform;
// });


Route::get('addEntreprises', function () {
    $filiales = DB::table('filiales')->get();
    $entreprises = DB::table('entreprises')->get();

    $FilialeTransfom = $filiales->map(function ($filiale) {
        $nom = $filiale->nom_filiale;
        $groupe = $filiale->groupe;
        $secteur = $filiale->secteur;
        $nationalite = $filiale->nationalite;

        return [
            'Nom_Ent' => $nom,
            'Grp_Ent' => $groupe,
            'Sect_Ent' => $secteur,
            'Nationalite' => $nationalite,
        ];
    });

    $EntrepriseTransform = $entreprises->map(function ($entreprise) {
        $nom = $entreprise->nom_entreprise;
        $groupe = $entreprise->groupe;
        $secteur = $entreprise->secteur;
        $nationalite = $entreprise->nationalite;

        return [
            'Nom_Ent' => $nom,
            'Grp_Ent' => $groupe,
            'Sect_Ent' => $secteur,
            'Nationalite' => $nationalite,
        ];
    });

    // Chargement des données transformées dans la destination
    $query = DB::table('dentreprise')->insert($EntrepriseTransform->toArray());
    if ($query) {
        echo "Les données ont bien été transformées.";                             
    } else { 
        echo "Une erreur s'est produite lors du chargement des données.";              
    }
    // return $FonctionTransform;
});


Route::get('addTemps', function () {
    $startDate = Carbon\Carbon::createFromFormat('Y-m-d', '1900-01-01');
    $endDate = Carbon\Carbon::createFromFormat('Y-m-d', '2100-12-31');
    while ($startDate->lessThanOrEqualTo($endDate)) {
        $year = $startDate->year;
        $month = $startDate->month;
        $quarter = ceil($startDate->month / 4);
        $label = ucfirst($startDate->format('F'));
    
        DB::table('Dtemps')->insert([
            'DATE' => $startDate,   
            'mois' => $month,
            // 'Libelle_Mois' => $label,
            'trimestre' => $quarter,
            'annee' => $year,
        ]);

        $startDate->addMonth();
    }

    // $startDate = Carbon\Carbon::createFromFormat('Y-m-d', '1900-01-01');
    // $endDate = Carbon\Carbon::createFromFormat('Y-m-d', '2100-12-01');
    // $currentDate = $startDate;
    // // $trimestre = 1;
    // // $annee = 1900;

    // while ($currentDate <= $endDate) {
    //     DB::table('Dtemps')->insert([
    //         'DATE' => $currentDate,
    //         'mois' => date('n', strtotime($currentDate)),
    //         'trimestre' => ceil($startDate->month / 3),
    //         'annee' => $currentDate->year,
    //     ]);

    //     // Incrémenter la date
    //     $currentDate = date('Y-m-d', strtotime('+1 month', strtotime($currentDate)));

    //     // Incrémenter le trimestre et l'année si nécessaire
    //     // if ($trimestre < 4) {
    //     //     $trimestre++;
    //     // } else {
    //     //     $trimestre = 1;
    //     //     $annee++;
    //     // }
    // }
});




Route::get('get01', function () {


    $trancheAges = DB::table('femployee')
        ->join('dage', 'femployee.ID_Age', '=', 'dage.ID_Age')
        ->select(DB::raw('SUM(Nombre_Eff) as nb_employes'), 'dage.Tranche_Age')
        ->groupBy('femployee.ID_Age', 'dage.Tranche_Age')
        ->get();

    $SexeEmployes = DB::table('femployee')
        ->join('dsexe', 'femployee.ID_Sexe', '=', 'dsexe.ID_Sexe')
        ->select(DB::raw('SUM(Nombre_Eff) as nb_employes'), 'dsexe.Sexe')
        ->groupBy('femployee.ID_Sexe', 'dsexe.Sexe')
        ->get();

    $resultat_employes_sexe = $SexeEmployes->map(function ($SexeEmploye) {
        $labal = $SexeEmploye->Sexe;
        $value = $SexeEmploye->nb_employes;
        return [
            "id" => $labal,
            "label" => $labal,
            "value" => $value,
        ];
    }); 

    $results_employes_age = $trancheAges->map(function ($trancheAge) {
        $labal = $trancheAge->Tranche_Age;
        $value = $trancheAge->nb_employes;
        return [
            $labal => $value
        ];
    });


    return response([
        'dash01' => $resultat_employes_sexe,
        'dash02' => $results_employes_age,
    ]);
});

Route::get('get02', function () {

    
    $results = DB::table('ffinance')
    ->join('dtemps', 'ffinance.ID_Date_Agregats', '=', 'dtemps.Mois')
    ->join('dagregats', 'ffinance.ID_Agregats', '=', 'dagregats.ID_Agregats')
    ->select(DB::raw('dtemps.Annee AS ANNEE, dagregats.Type_Agregats AS TYPE_A, ffinance.taux_Realisation * 100 as taux'))
    ->groupBy('ANNEE', 'TYPE_A')
    ->get();

    $dataArray = json_decode($results, true);
    // Initialiser le tableau final

    // Créer un tableau associatif pour les couleurs
    $colors = [
        "vente" => "rgb(244, 117, 96)",
        "consomation" => "rgb(241, 225, 91)",
        "production" => "rgb(232, 193, 160)",
        "autre" => "rgb(131, 109, 90)"
    ];

    // Initialiser le tableau final
    $finalData = [];

    // Parcourir les données d'origine et les regrouper par TYPE_A
    foreach ($dataArray as $item) {
        $typeA = $item["TYPE_A"];
        $annee = $item["ANNEE"];
        $taux = $item["taux"];

        // Si le TYPE_A n'existe pas encore dans le tableau final, l'ajouter avec les données initiales
        if (!array_key_exists($typeA, $finalData)) {
            $finalData[$typeA] = [
                "id" => $typeA,
                "color" => $colors[$typeA],
                "data" => []
            ];
        }

        // Ajouter les données pour l'ANNEE actuelle dans le tableau final
        $finalData[$typeA]["data"][] = [
            "x" => $annee,
            "y" => $taux
        ];
    }

    // Réinitialiser les clés du tableau final pour qu'ils soient numériques
    $finalData = array_values($finalData);

    // Encoder le tableau final en JSON pour pouvoir l'utiliser en JavaScript
    $jsonData = json_encode($finalData);


    $ca = DB::table('ffinance')->sum('Montant_Realisation');

    // Afficher le résultat
    return response([
        'graph' => $jsonData,
        'ca' => $ca
    ]);

});

Route::get('/get03', function () {

    $get_Position_group = DB::table('femployee')
    ->select(DB::raw('COUNT(Nombre_Eff) AS nb_employes'), DB::raw("CASE WHEN ID_Date_Retraite = '0000-00' THEN 'non retraiter' WHEN ID_Date_Retraite != '0000-00' THEN 'retraiter' END AS positionEmploye"))
    ->groupBy('positionEmploye')
    ->get();

    $SexeEmployes = DB::table('femployee')
    ->join('dsexe', 'femployee.ID_Sexe', '=', 'dsexe.ID_Sexe')
    ->select(DB::raw('SUM(Nombre_Eff) as nb_employes'), 'dsexe.Sexe')
    ->groupBy('femployee.ID_Sexe', 'dsexe.Sexe')
    ->get();

    $ca = DB::table('ffinance')->sum('Montant_Realisation');

    return $SexeEmployes;
});


// Route::get('addFemploye', function () {
//     $timestemp = DB::table('controller_stamp')->max('last_timp_stamp');
//     $lastTimestamp = DB::table('employes')->max('updated_at');


//     $resultats = DB::table('employes')
//         ->join('dsexe', 'dsexe.Sexe', '=', 'employes.sexe')
//         ->join('dhandicap', 'dhandicap.Handicap', '=', 'employes.handicape')
//         ->join('dfonction', 'dfonction.Fonction', '=', 'employes.fonction')
//         ->join('filiales', 'employes.filiale_id', '=', 'filiales.id')
//         ->join('dentreprise', 'filiales.nom_filiale', '=', 'dentreprise.Nom_Ent')
//         ->join('dtemps_travail', 'dtemps_travail.Temps_Travail', '=', 'employes.temp_occuper')
//         ->join('dcontrat', 'dcontrat.Type_Contrat', '=', 'employes.contract')
//         ->select(
//         DB::raw("DATE_FORMAT(employes.created_at, '%Y-%m') AS ID_Temps"),
//         DB::raw("DATE_FORMAT(employes.date_recrutement, '%Y-%m') AS ID_Date_Recrutement"),
//         DB::raw("DATE_FORMAT(employes.date_retraite, '%Y-%m') AS ID_Date_Retraite"),
//         'dsexe.ID_Sexe AS ID_Sexe',
//         'dhandicap.ID_Handicap AS ID_Handicap',
//         'dfonction.ID_Fonction AS ID_Fonction',
//         'dentreprise.ID_Ent AS ID_Ent',
//         'dtemps_travail.ID_Temps_Trav AS ID_Temps_Trav',
//         'dcontrat.ID_Contrat AS ID_Contrat',
//         DB::raw("CASE
//             WHEN TIMESTAMPDIFF(YEAR, employes.date_naissance, CURDATE()) <= 20 THEN 1
//             WHEN TIMESTAMPDIFF(YEAR, employes.date_naissance, CURDATE()) BETWEEN 21 AND 25 THEN 2 
//             WHEN TIMESTAMPDIFF(YEAR, employes.date_naissance, CURDATE()) BETWEEN 26 AND 30 THEN 3
//             WHEN TIMESTAMPDIFF(YEAR, employes.date_naissance, CURDATE()) BETWEEN 31 AND 35 THEN 4
//             WHEN TIMESTAMPDIFF(YEAR, employes.date_naissance, CURDATE()) BETWEEN 36 AND 40 THEN 5
//             WHEN TIMESTAMPDIFF(YEAR, employes.date_naissance, CURDATE()) BETWEEN 41 AND 45 THEN 6
//             WHEN TIMESTAMPDIFF(YEAR, employes.date_naissance, CURDATE()) BETWEEN 46 AND 50 THEN 7
//             WHEN TIMESTAMPDIFF(YEAR, employes.date_naissance, CURDATE()) BETWEEN 51 AND 55 THEN 8
//             WHEN TIMESTAMPDIFF(YEAR, employes.date_naissance, CURDATE()) BETWEEN 56 AND 60 THEN 9
//             ELSE 10
//         END AS ID_Age"),
//         DB::raw("COUNT(employes.id) AS Nombre_Eff")
//         )
//         ->where('employes.updated_at','>', $timestemp)
//         ->groupBy('ID_Temps', 'ID_Date_Recrutement', 'ID_Date_Retraite', 'ID_Fonction', 'ID_Sexe', 'ID_Handicap', 'ID_Ent', 'ID_Temps_Trav', 'ID_Contrat', 'ID_Age')
//         ->get();

//     $TransformRequest = $resultats->map(function ($resultat) {
//         $id0 = $resultat->ID_Temps;
//         $id1 = $resultat->ID_Date_Recrutement;
//         $id2 = is_null($resultat->ID_Date_Retraite) ? '0000-00' : $resultat->ID_Date_Retraite;
//         $id3 = $resultat->ID_Sexe;
//         $id4 = $resultat->ID_Handicap;
//         $id5 = $resultat->ID_Fonction;
//         $id6 = $resultat->ID_Ent;
//         $id7 = $resultat->ID_Temps_Trav;
//         $id8 = $resultat->ID_Contrat;
//         $id9 = $resultat->ID_Age;
//         $Nombre_Eff = $resultat->Nombre_Eff;
//         return [
//             'ID_Temps' => $id0,
//             'ID_Date_Recrutement' => $id1,
//             'ID_Date_Retraite' => $id2,
//             'ID_Sexe' => $id3,
//             'ID_Handicap' => $id4,
//             'ID_Fonction' => $id5,
//             'ID_Ent' => $id6,
//             'ID_Temps_Trav' => $id7,
//             'ID_Contrat' => $id8,
//             'ID_Age' => $id9,
//             'Nombre_Eff' => $Nombre_Eff
//         ];
//     });
    
//     // $query = DB::table('femployee')->insert($TransformRequest->toArray());
//     // if ($query) {
//     //     DB::table('controller_stamp')
//     //     ->where('table_stamp', '=', 'employes')
//     //     ->update(['last_timp_stamp' => $lastTimestamp]);
//     //     echo "Les données ont bien été transformées.";                             
//     // } else { 
//     //     echo "Une erreur s'est produite lors du chargement des données.";              
//     // }
//     echo $TransformRequest->isEmpty() ? "vide" : "non vide";

//     // $query = DB::table('controller_stamp')->insert(['table_stamp' => 'employes', 'last_timp_stamp' => $lastTimestamp]);
//     // if ($query) {
//     //     echo "true";
//     // } else {
//     //     echo "false";
//     // }
    
//     // echo $TransformRequest;

// });


Route::get('addFEmployee', function () {


    $results = DB::table('employes')
        ->join('dsexe', 'dsexe.Sexe', '=', 'employes.sexe')
        ->join('dhandicap', 'dhandicap.Handicap', '=', 'employes.handicape')
        ->join('dfonction', 'dfonction.Fonction', '=', 'employes.fonction')
        ->join('filiales', 'employes.filiale_id', '=', 'filiales.id')
        ->join('dentreprise', 'filiales.nom_filiale', '=', 'dentreprise.Nom_Ent')
        ->join('dtemps_travail', 'dtemps_travail.Temps_Travail', '=', 'employes.temp_occuper')
        ->join('dcontrat', 'dcontrat.Type_Contrat', '=', 'employes.contract')
        ->join('dscociopro', 'dscociopro.Scocipro', '=', 'employes.categ_sociopro')
        ->join('dtemps as dtemps_cra', DB::raw("DATE_FORMAT(employes.created_at, '%Y-%m')"), '=', DB::raw("DATE_FORMAT(dtemps_cra.DATE, '%Y-%m')"))
        ->join('dtemps as dtemps_rec', DB::raw("DATE_FORMAT(employes.date_recrutement, '%Y-%m')"), '=', DB::raw("DATE_FORMAT(dtemps_rec.DATE, '%Y-%m')"))
        ->leftJoin('dtemps AS dtemps_ret', function ($join) {
            $join->on(DB::raw("DATE_FORMAT(employes.date_retraite, '%Y-%m')"), '=', DB::raw("DATE_FORMAT(dtemps_ret.DATE, '%Y-%m')"))
                ->orWhere(function ($query) {
                    $query->whereNull('employes.date_retraite')
                        ->where('dtemps_ret.ID_Temps', '=', 2413);
                });
        })
        ->select(
                'dtemps_cra.ID_Temps as ID_Temps',
                'dtemps_rec.ID_Temps as ID_Date_Recrutement',
                DB::raw('COALESCE(dtemps_ret.ID_Temps, 2413) as ID_Date_Retraite'),
                'dsexe.ID_Sexe as ID_Sexe',
                'dhandicap.ID_Handicap as ID_Handicap',
                'dfonction.ID_Fonction as ID_Fonction',
                'dentreprise.ID_Ent as ID_Ent',
                'dtemps_travail.ID_Temps_Trav as ID_Temps_Trav',
                'dcontrat.ID_Contrat as ID_Contrat',
                'dscociopro.ID_scociopro AS ID_scociopro',
                DB::raw('CASE
                    WHEN TIMESTAMPDIFF(YEAR, employes.date_naissance, CURDATE()) <= 20 THEN 1
                    WHEN TIMESTAMPDIFF(YEAR, employes.date_naissance, CURDATE()) BETWEEN 21 AND 25 THEN 2 
                    WHEN TIMESTAMPDIFF(YEAR, employes.date_naissance, CURDATE()) BETWEEN 21 AND 25 THEN 3
                    WHEN TIMESTAMPDIFF(YEAR, employes.date_naissance, CURDATE()) BETWEEN 26 AND 30 THEN 4
                    WHEN TIMESTAMPDIFF(YEAR, employes.date_naissance, CURDATE()) BETWEEN 31 AND 35 THEN 5
                    WHEN TIMESTAMPDIFF(YEAR, employes.date_naissance, CURDATE()) BETWEEN 36 AND 40 THEN 6
                    WHEN TIMESTAMPDIFF(YEAR, employes.date_naissance, CURDATE()) BETWEEN 41 AND 45 THEN 7
                    WHEN TIMESTAMPDIFF(YEAR, employes.date_naissance, CURDATE()) BETWEEN 46 AND 50 THEN 8
                    WHEN TIMESTAMPDIFF(YEAR, employes.date_naissance, CURDATE()) BETWEEN 51 AND 55 THEN 9
                    WHEN TIMESTAMPDIFF(YEAR, employes.date_naissance, CURDATE()) BETWEEN 56 AND 60 THEN 10
                    ELSE 11
                END AS ID_Age',),
                DB::raw('COUNT(employes.id) as Nombre_Eff')
            )
            ->groupBy('ID_Temps', 'ID_Date_Recrutement', 'ID_Date_Retraite', 'ID_Fonction', 'ID_Sexe', 'ID_Handicap', 'ID_Ent', 'ID_Temps_Trav', 'ID_Contrat', 'ID_Age')
            ->get();

            $TransformRequest = $results->map(function ($result) {
                $id0 = $result->ID_Temps;
                $id1 = $result->ID_Date_Recrutement;
                $id2 =  $result->ID_Date_Retraite;
                $id3 = $result->ID_Sexe;
                $id4 = $result->ID_Handicap;
                $id5 = $result->ID_Fonction;
                $id6 = $result->ID_Ent;
                $id7 = $result->ID_Temps_Trav;
                $id8 = $result->ID_Contrat;
                $id9 = $result->ID_Age;
                $id10 = $result->ID_scociopro;
                $Nombre_Eff = $result->Nombre_Eff;
                return [
                    'ID_Temps' => $id0,
                    'ID_Date_Recrutement' => $id1,
                    'ID_Date_Retraite' => $id2,
                    'ID_Sexe' => $id3,
                    'ID_Handicap' => $id4,
                    'ID_Fonction' => $id5,
                    'ID_Ent' => $id6,
                    'ID_Temps_Trav' => $id7,
                    'ID_Contrat' => $id8,
                    'ID_Age' => $id9,
                    'ID_scociopro' => $id10,
                    'Nombre_Eff' => $Nombre_Eff
                ];
            });

    $query = DB::table('femploye')->insert($TransformRequest->toArray());
    if ($query) {
        echo "Les données ont bien été transformées.";                             
    } else { 
        echo "Une erreur s'est produite lors du chargement des données.";              
    }

});

Route::get('addFinance', function () {
    $lastTimestamp = DB::table('employes')->max('updated_at');
    $resultats = DB::table('finances')
    ->join('dagregats', 'finances.activite', '=', 'dagregats.agregats')
    ->join('filiales', 'finances.filiale_id', '=', 'filiales.id')
    ->join('dentreprise', 'filiales.nom_filiale', '=', 'dentreprise.Nom_Ent')
    ->join('dtemps as dtemps_cra', DB::raw("DATE_FORMAT(finances.created_at, '%Y-%m')"), '=', DB::raw("DATE_FORMAT(dtemps_cra.DATE, '%Y-%m')"))
    ->join('dtemps as dtemps_act', DB::raw("DATE_FORMAT(finances.date_activite, '%Y-%m')"), '=', DB::raw("DATE_FORMAT(dtemps_act.DATE, '%Y-%m')"))
    ->select(
        'dtemps_act.ID_Temps AS ID_Date_Agregats',
        'dtemps_cra.ID_Temps AS ID_Temps',
        'dagregats.ID_Agregats AS ID_Agregats',
        'dentreprise.ID_Ent AS ID_Ent',
        DB::raw('SUM(finances.realisation) AS Montant_Realisation'),
        DB::raw('SUM(finances.privision) AS Montant_Privision'),
        DB::raw('SUM(finances.realisation - finances.privision) AS Ecart_Valeur'),
        DB::raw('SUM(finances.realisation / finances.privision) AS taux_Realisation')
    )
    ->groupBy('ID_Date_Agregats', 'ID_Temps', 'ID_Agregats', 'ID_Ent')
    ->get();
    

    $TransformRequest = $resultats->map(function ($resultat) {
        $id0 = $resultat->ID_Temps;
        $id1 = $resultat->ID_Date_Agregats;
        $id2 = $resultat->ID_Agregats;
        $id3 = $resultat->ID_Ent;
        $val1 = $resultat->Montant_Realisation;
        $val2 = $resultat->Montant_Privision;
        $val3 = $resultat->Ecart_Valeur;
        $val4 = $resultat->taux_Realisation;
        return [
            'ID_Temps' => $id0,
            'ID_Date_Agregats' => $id1,
            'ID_Agregats' => $id2,
            'ID_Ent' => $id3,
            'Montant_Realisation' => $val1,
            'Montant_Privision' => $val2,
            'Ecart_Valeur' => $val3,
            'taux_Realisation' => $val4
        ];
    });
    
    $query = DB::table('ffinance')->insert($TransformRequest->toArray());
    if ($query) {
        echo "Les données ont bien été transformées.";                             
    } else { 
        echo "Une erreur s'est produite lors du chargement des données.";              
    }
});


Route::get('finance_dashboard', function () {

    $year = 2016;
    $filiale = "";

    $test = DB::table('ffinance')
    ->join('dagregats', 'ffinance.ID_Agregats', '=', 'dagregats.ID_Agregats')
    ->join('dtemps', 'ffinance.ID_Date_Agregats', '=', 'dtemps.ID_Temps')
    ->select(
        DB::raw('ffinance.taux_Realisation * 100 AS taux'),
        DB::raw('SUM(ffinance.Montant_Realisation) as Montant_Realisation'),
        DB::raw('SUM(ffinance.Montant_Privision) as Montant_Privision'),
        DB::raw('SUM(ffinance.Ecart_Valeur) as Ecart_Valeur'),
        DB::raw("CASE
            WHEN dagregats.Type_Agregats = 'vente' THEN 'Chiffre d\'affaires'
            WHEN dagregats.Type_Agregats = 'production' THEN 'production'
            WHEN dagregats.Type_Agregats = 'consomation' THEN 'Consommations de la periode'
            WHEN dagregats.agregats IN ('Impots, taxes et versements assimiles', 'Charges de personnel') THEN 'calc'
            ELSE 'autre'
        END AS Agregat_calculer")
    );

    $resultats = $test
    ->groupBy('Agregat_calculer')
    ->get();

    if (!empty($year) && !empty($filiale)) {
        $resultats = $test
        ->where('dtemps.annee', $year)
        ->where('ffinance.ID_Ent', $filiale)
        ->groupBy('Agregat_calculer')
        ->get();
    } else {
        if (!empty($year)) {
            $resultats = $test
            ->where('dtemps.annee', $year)
            ->groupBy('Agregat_calculer')
            ->get();           
        } 
        if (!empty($filiale)) {
            $resultats = $test
            ->where('ffinance.ID_Ent', $filiale)
            ->groupBy('Agregat_calculer')
            ->get();                 
        }
    }


    $query = $resultats->map(function ($resultat) {
        $val1 = intval($resultat->Montant_Realisation);
        $val2 = intval($resultat->Montant_Privision);
        $val3 = intval($resultat->Ecart_Valeur);
        $val4 = round(floatval($resultat->taux), 2);
        $txt = $resultat->Agregat_calculer;

        return [
            'Agregat_calculer' => $txt,
            'Montant_Realisation' => $val1,
            'Montant_Privision' => $val2,
            'Ecart_Valeur' => $val3,
            'taux_Realisation' => $val4
        ];
    });


    $Chiffre_affaire = $resultats->where('Agregat_calculer', 'Chiffre d\'affaires')->first();
    $Production_Periode = $resultats->where('Agregat_calculer', 'production')->first();
    $Consommation_Periode = $resultats->where('Agregat_calculer', 'Consommations de la periode')->first();
    $EBE = $resultats->where('Agregat_calculer', 'calc')->first();
    $va = [];


    if ($Chiffre_affaire && $Production_Periode) {
        $ca1 = intval($Chiffre_affaire->Montant_Realisation);
        $ca2 = intval($Chiffre_affaire->Montant_Privision);
        $pp1 = intval($Production_Periode->Montant_Realisation);
        $pp2 = intval($Production_Periode->Montant_Privision);

        $pp1 += $ca1;
        $pp2 += $ca2;
        $Ecart_Valeur = $pp1 - $pp2;

        $Production_Periode->Montant_Realisation = $pp1;
        $Production_Periode->Montant_Privision = $pp2;
        $Production_Periode->Ecart_Valeur = $Ecart_Valeur;
        $Production_Periode->Agregat_calculer = "Production de la periode";
        $Production_Periode->taux = round(floatval($pp1 / $pp2) * 100, 2);
    } 

    if (!$Chiffre_affaire) {
        $Chiffre_affaire["Montant_Realisation"] = "-";
        $Chiffre_affaire["Montant_Privision"] = "-";
        $Chiffre_affaire["Ecart_Valeur"] = "-";
        $Chiffre_affaire["Agregat_calculer"] = 'Chiffre d\'affaires';
        $Chiffre_affaire["taux"] = "-";
    } else {
        $pp1 = intval($Chiffre_affaire->Montant_Realisation);
        $pp2 = intval($Chiffre_affaire->Montant_Privision);
        $pp3 = intval($Chiffre_affaire->Ecart_Valeur);  
        $pp4 = round(floatval($Chiffre_affaire->taux), 2);

        $Chiffre_affaire->Montant_Realisation = $pp1;
        $Chiffre_affaire->Montant_Privision = $pp2;
        $Chiffre_affaire->Ecart_Valeur = $pp3;
        $Chiffre_affaire->Agregat_calculer = 'Chiffre d\'affaires';
        $Chiffre_affaire->taux = $pp4;
    }
    if ($Consommation_Periode && $Production_Periode) {
        $cp1 = intval($Consommation_Periode->Montant_Realisation);
        $cp2 = intval($Consommation_Periode->Montant_Privision);
        $pp1 = intval($Production_Periode->Montant_Realisation);
        $pp2 = intval($Production_Periode->Montant_Privision);

        $pp1 -= $cp1;
        $pp2 -= $cp2;
        if ($pp2 < 0) {
            $Ecart_Valeur = $pp1 + $pp2;
        } else {
            $Ecart_Valeur = $pp1 - $pp2;
        }
        $va["Montant_Realisation"] = $pp1;
        $va["Montant_Privision"] = $pp2;
        $va["Ecart_Valeur"]= $Ecart_Valeur;
        $va["Agregat_calculer"] = "Valeur Ajoute";
        $va["taux"] = round(abs(floatval($pp1 / $pp2)) * 100, 2);
    } else {
        $va["Montant_Realisation"] = "-";
        $va["Montant_Privision"] = "-";
        $va["Ecart_Valeur"] = "-";
        $va["Agregat_calculer"] = "Valeur Ajoute";
        $va["taux"] = "-";
    }

    if (!$Consommation_Periode) {
        $Consommation_Periode["Montant_Realisation"] = "-";
        $Consommation_Periode["Montant_Privision"] = "-";
        $Consommation_Periode["Ecart_Valeur"] = "-";
        $Consommation_Periode["Agregat_calculer"] = 'Consommations de la periode';
        $Consommation_Periode["taux"] = "-";
    } else {
        $pp1 = intval($Consommation_Periode->Montant_Realisation);
        $pp2 = intval($Consommation_Periode->Montant_Privision);
        $pp3 = intval($Consommation_Periode->Ecart_Valeur);  
        $pp4 = round(floatval($Consommation_Periode->taux), 2);

        $Consommation_Periode->Montant_Realisation = $pp1;
        $Consommation_Periode->Montant_Privision = $pp2;
        $Consommation_Periode->Ecart_Valeur = $pp3;
        $Consommation_Periode->Agregat_calculer = 'Consommations de la periode';
        $Consommation_Periode->taux = $pp4;
    }
    if (!$Production_Periode) {
        $Production_Periode["Montant_Realisation"] = "-";
        $Production_Periode["Montant_Privision"] = "-";
        $Production_Periode["Ecart_Valeur"] = "-";
        $Production_Periode["Agregat_calculer"] = "Production de la periode";
        $Production_Periode["taux"] = "-";
    } else {
        $pp1 = intval($Production_Periode->Montant_Realisation);
        $pp2 = intval($Production_Periode->Montant_Privision);
        $pp3 = intval($Production_Periode->Ecart_Valeur);  
        $pp4 = round(floatval($Production_Periode->taux), 2);

        $Production_Periode->Montant_Realisation = $pp1;
        $Production_Periode->Montant_Privision = $pp2;
        $Production_Periode->Ecart_Valeur = $pp3;
        $Production_Periode->Agregat_calculer = "Production de la periode";
        $Production_Periode->taux = $pp4;
    }

    if ($va && $EBE) {
        $ca1 = intval($va["Montant_Realisation"]);
        $ca2 = intval($va["Montant_Privision"]);
        $pp1 = intval($EBE->Montant_Realisation);
        $pp2 = intval($EBE->Montant_Privision);

        $pp1 -= $ca1;
        $pp2 -= $ca2;
        if ($pp2 < 0) {
            $Ecart_Valeur = $pp1 + $pp2;
        } else {
            $Ecart_Valeur = $pp1 - $pp2;
        }        
        // $EBE = $Ecart_Valeur - $pp2;

        $EBE->Montant_Realisation = $pp1;
        $EBE->Montant_Privision = $pp2;
        $EBE->Ecart_Valeur = $Ecart_Valeur;
        $EBE->Agregat_calculer = "EBE";
        $EBE->taux = round(floatval($pp1 / $pp2) * 100, 2);        
    } 
    else {
        $EBE["Montant_Realisation"] = $va["Montant_Realisation"] ;
        $EBE["Montant_Privision"] = $va["Montant_Privision"] ;
        $EBE["Ecart_Valeur"] = $va["Ecart_Valeur"];
        $EBE["Agregat_calculer"] = "EBE";
        $EBE["taux"] = $va["taux"];        
    }



    // return response([$Production_Periode, $Consommation_Periode, $Chiffre_affaire, $va, $EBE]);
    $years = DB::table('finances')
    ->select(DB::raw("DISTINCT(DATE_FORMAT(finances.date_activite, '%Y')) as year"))
    ->orderBy(DB::raw("(DATE_FORMAT(finances.date_activite, '%Y'))"), 'ASC')
    ->get();



    return response([$Production_Periode, $Consommation_Periode, $Chiffre_affaire, $va, $EBE]);

});



Route::get('dash_rhs', function () {
    $req01 = DB::table('femploye')
    ->join('dscociopro', 'femploye.ID_scociopro', '=', 'dscociopro.ID_scociopro')
    ->select(DB::raw('SUM(femploye.Nombre_Eff) as nb_employes'), DB::raw('CASE WHEN ID_Sexe = 2 THEN "femme" WHEN ID_Sexe = 1 THEN "homme" END AS gestion_ressource'))
    ->groupBy('gestion_ressource')
    ->get();

    $req02 = DB::table('femploye')
    ->join('dscociopro', 'femploye.ID_scociopro', '=', 'dscociopro.ID_scociopro')
    ->select(DB::raw('SUM(femploye.Nombre_Eff) as nb_employes'), DB::raw('CASE WHEN dscociopro.Scocipro = "Cadre superieur" THEN "cadre superieur" WHEN dscociopro.Scocipro = "Cadre" THEN "cadre" WHEN dscociopro.Scocipro = "Cadre dirigeant" THEN "cadre dirigeant" WHEN dscociopro.Scocipro = "maitrise" THEN "maitrise" WHEN dscociopro.Scocipro = "execution" THEN "execution" ELSE "retraite" END AS gestion_ressource'))
    ->groupBy('gestion_ressource')
    ->get();

    $req03 = DB::table('femploye')
    ->join('dscociopro', 'femploye.ID_scociopro', '=', 'dscociopro.ID_scociopro')
    ->select(DB::raw('SUM(femploye.Nombre_Eff) as nb_employes'), DB::raw('CASE WHEN dscociopro.Scocipro IN ("maitrise", "execution") THEN "personnelle technique" WHEN dscociopro.Scocipro NOT IN ("maitrise", "execution") THEN "personnelle administratifs" END AS gestion_ressource'))
    ->groupBy('gestion_ressource')
    ->get();


    $result1 = $req01->map(function ($req) {
        $EBE = $req->gestion_ressource;
        $val = $req->nb_employes;

        return [
            'EBE' => $EBE,
            'val' => $val
        ];
    });

    $result2 = $req02->map(function ($req) {
        $EBE = $req->gestion_ressource;
        $val = $req->nb_employes;

        return [
            'EBE' => $EBE,
            'val' => $val
        ];
    });

    $result3 = $req03->map(function ($req) {
        $EBE = $req->gestion_ressource;
        $val = $req->nb_employes;

        return [
            'EBE' => $EBE,
            'val' => $val
        ];
    });


    return response(["part1" => $result1,"part2" => $result2, "part3" => $result3]);
});