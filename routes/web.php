<?php

use App\Http\Controllers\Api\RoleController;
use App\Models\Employe;
use App\Models\Entreprise;
use App\Models\Filiale;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\Cast\Object_;
use PhpParser\Node\Stmt\Echo_;

use function PHPSTORM_META\map;

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
        $month = $startDate->format('Y-m');
        $quarter = ceil($startDate->month / 3);
        $label = ucfirst($startDate->format('F'));
    
        DB::table('Dtemps')->insert([
            'Mois' => $month,
            'Libelle_Mois' => $label,
            'Trimestre' => $quarter,
            'Annee' => $year,
        ]);

        $startDate->addMonth();
    }
});


Route::get('addFinance', function () {
    $lastTimestamp = DB::table('employes')->max('updated_at');
    $resultats = DB::table('finances')
    ->join('dagregats', 'finances.type_activite', '=', 'dagregats.Type_Agregats')
    ->join('filiales', 'finances.filiale_id', '=', 'filiales.id')
    ->join('dentreprise', 'filiales.nom_filiale', '=', 'dentreprise.Nom_Ent')
    ->select(DB::raw('DATE_FORMAT(finances.date_activite, "%Y-%m") AS ID_Date_Agregats,
                      DATE_FORMAT(finances.created_at, "%Y-%m") AS ID_Temps,
                      dagregats.ID_Agregats AS ID_Agregats,
                      dentreprise.ID_Ent AS ID_Ent,
                      SUM(finances.realisation) AS Montant_Realisation,
                      SUM(finances.privision) AS Montant_Privision,
                      SUM(finances.realisation - finances.privision) AS Ecart_Valeur,
                      SUM(finances.realisation / finances.privision) AS taux_Realisation'))
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






Route::get('addFemploye', function () {
    $timestemp = DB::table('controller_stamp')->max('last_timp_stamp');
    $lastTimestamp = DB::table('employes')->max('updated_at');


    $resultats = DB::table('employes')
        ->join('dsexe', 'dsexe.Sexe', '=', 'employes.sexe')
        ->join('dhandicap', 'dhandicap.Handicap', '=', 'employes.handicape')
        ->join('dfonction', 'dfonction.Fonction', '=', 'employes.fonction')
        ->join('filiales', 'employes.filiale_id', '=', 'filiales.id')
        ->join('dentreprise', 'filiales.nom_filiale', '=', 'dentreprise.Nom_Ent')
        ->join('dtemps_travail', 'dtemps_travail.Temps_Travail', '=', 'employes.temp_occuper')
        ->join('dcontrat', 'dcontrat.Type_Contrat', '=', 'employes.contract')
        ->select(
        DB::raw("DATE_FORMAT(employes.created_at, '%Y-%m') AS ID_Temps"),
        DB::raw("DATE_FORMAT(employes.date_recrutement, '%Y-%m') AS ID_Date_Recrutement"),
        DB::raw("DATE_FORMAT(employes.date_retraite, '%Y-%m') AS ID_Date_Retraite"),
        'dsexe.ID_Sexe AS ID_Sexe',
        'dhandicap.ID_Handicap AS ID_Handicap',
        'dfonction.ID_Fonction AS ID_Fonction',
        'dentreprise.ID_Ent AS ID_Ent',
        'dtemps_travail.ID_Temps_Trav AS ID_Temps_Trav',
        'dcontrat.ID_Contrat AS ID_Contrat',
        DB::raw("CASE
            WHEN TIMESTAMPDIFF(YEAR, employes.date_naissance, CURDATE()) <= 20 THEN 1
            WHEN TIMESTAMPDIFF(YEAR, employes.date_naissance, CURDATE()) BETWEEN 21 AND 25 THEN 2 
            WHEN TIMESTAMPDIFF(YEAR, employes.date_naissance, CURDATE()) BETWEEN 26 AND 30 THEN 3
            WHEN TIMESTAMPDIFF(YEAR, employes.date_naissance, CURDATE()) BETWEEN 31 AND 35 THEN 4
            WHEN TIMESTAMPDIFF(YEAR, employes.date_naissance, CURDATE()) BETWEEN 36 AND 40 THEN 5
            WHEN TIMESTAMPDIFF(YEAR, employes.date_naissance, CURDATE()) BETWEEN 41 AND 45 THEN 6
            WHEN TIMESTAMPDIFF(YEAR, employes.date_naissance, CURDATE()) BETWEEN 46 AND 50 THEN 7
            WHEN TIMESTAMPDIFF(YEAR, employes.date_naissance, CURDATE()) BETWEEN 51 AND 55 THEN 8
            WHEN TIMESTAMPDIFF(YEAR, employes.date_naissance, CURDATE()) BETWEEN 56 AND 60 THEN 9
            ELSE 10
        END AS ID_Age"),
        DB::raw("COUNT(employes.id) AS Nombre_Eff")
        )
        ->where('employes.updated_at','>', $timestemp)
        ->groupBy('ID_Temps', 'ID_Date_Recrutement', 'ID_Date_Retraite', 'ID_Fonction', 'ID_Sexe', 'ID_Handicap', 'ID_Ent', 'ID_Temps_Trav', 'ID_Contrat', 'ID_Age')
        ->get();

    $TransformRequest = $resultats->map(function ($resultat) {
        $id0 = $resultat->ID_Temps;
        $id1 = $resultat->ID_Date_Recrutement;
        $id2 = is_null($resultat->ID_Date_Retraite) ? '0000-00' : $resultat->ID_Date_Retraite;
        $id3 = $resultat->ID_Sexe;
        $id4 = $resultat->ID_Handicap;
        $id5 = $resultat->ID_Fonction;
        $id6 = $resultat->ID_Ent;
        $id7 = $resultat->ID_Temps_Trav;
        $id8 = $resultat->ID_Contrat;
        $id9 = $resultat->ID_Age;
        $Nombre_Eff = $resultat->Nombre_Eff;
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
            'Nombre_Eff' => $Nombre_Eff
        ];
    });
    
    // $query = DB::table('femployee')->insert($TransformRequest->toArray());
    // if ($query) {
    //     DB::table('controller_stamp')
    //     ->where('table_stamp', '=', 'employes')
    //     ->update(['last_timp_stamp' => $lastTimestamp]);
    //     echo "Les données ont bien été transformées.";                             
    // } else { 
    //     echo "Une erreur s'est produite lors du chargement des données.";              
    // }
    echo $TransformRequest->isEmpty() ? "vide" : "non vide";

    // $query = DB::table('controller_stamp')->insert(['table_stamp' => 'employes', 'last_timp_stamp' => $lastTimestamp]);
    // if ($query) {
    //     echo "true";
    // } else {
    //     echo "false";
    // }
    
    // echo $TransformRequest;

});