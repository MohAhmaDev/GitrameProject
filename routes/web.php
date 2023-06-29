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
use PhpParser\Node\Stmt\Return_;

use function PHPSTORM_META\map;
use function PHPSTORM_META\type;
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
    // $employe_ids = Employe::pluck('id');
    $employe_ids = Employe::pluck('id')->all();
    $random_id = $employe_ids[array_rand($employe_ids)];
    $domaines = DB::table('ddomaine')
    ->select('Domaine')
    ->distinct()
    ->get()->random()->Domaine;

    $entreprise = \App\Models\Entreprise::query()->where('groupe', '!=', 'gitrame')->get();
    
    return $entreprise;

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



Route::get('addFormation', function () {

    $results = DB::table('formations')
    ->join('employes', 'formations.employe_id', '=', 'employes.id')
    ->join('dtemps', DB::raw("DATE_FORMAT(formations.created_at, '%Y-%m')"), '=', DB::raw("DATE_FORMAT(dtemps.DATE, '%Y-%m')"))
    ->join('ddomaine', 'formations.domaine_formation', '=', 'ddomaine.Domaine')
    ->join('dentreprise', 'employes.filiale_id', '=', 'dentreprise.ID_Ent')
    ->select(
        'dtemps.ID_Temps as ID_Temps',
        'dentreprise.ID_Ent as ID_Ent',
        'ddomaine.ID_Domaine as ID_Domaine',
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
        END AS ID_Age'),
        DB::raw('CASE
            WHEN formations.duree_formation < 15 THEN 1
            WHEN formations.duree_formation BETWEEN 15 AND 29 THEN 2 
            WHEN formations.duree_formation BETWEEN 30 AND 44 THEN 3
            WHEN formations.duree_formation BETWEEN 45 AND 59 THEN 4
            WHEN formations.duree_formation BETWEEN 60 AND 74 THEN 5
            WHEN formations.duree_formation BETWEEN 75 AND 89 THEN 6
            ELSE 7
        END as ID_Duree'),
        DB::raw('COUNT(employes.id) as Nombre_Eff'),
        DB::raw('SUM(formations.montant) as Montant')
    )
    ->groupBy('ID_Temps', 'ID_Ent', 'ID_Domaine', 'ID_Duree', 'ID_Age')
    ->get();

    $TransformRequest = $results->map(function ($result) {
        $id0 = $result->ID_Temps;
        $id1 = $result->ID_Ent;
        $id2 =  $result->ID_Domaine;
        $id4 = $result->ID_Duree;
        $id5 = $result->ID_Age;
        $Nombre_Eff = $result->Nombre_Eff;
        $Montant = $result->Montant;
        return [
            'ID_Temps' => $id0,
            'ID_Ent' => $id1,
            'ID_Domaine' => $id2,
            'ID_Duree' => $id4,
            'ID_Age' => $id5,
            'Nombre_Eff' => $Nombre_Eff,
            'Montant' => $Montant
        ];
    });

    $query = DB::table('fformation')->insert($TransformRequest->toArray());
    if ($query) {
        echo "Les données ont bien été transformées.";                             
    } else { 
        echo "Une erreur s'est produite lors du chargement des données.";              
    }
});





Route::get('dash_rhs', function () {


    $year = 2016;
    $filiale = "";

    $res01 = DB::table('femploye')
    ->join('dscociopro', 'femploye.ID_scociopro', '=', 'dscociopro.ID_scociopro')
    ->join('dtemps', 'femploye.ID_Date_Recrutement', '=', 'dtemps.ID_Temps')
    ->select(DB::raw('SUM(femploye.Nombre_Eff) as nb_employes'), DB::raw('CASE WHEN ID_Sexe = 2 THEN "femme" WHEN ID_Sexe = 1 THEN "homme" END AS gestion_ressource'));
    
    $req01 = $res01
    ->groupBy('gestion_ressource')
    ->get();

    $res02 = DB::table('femploye')
    ->join('dscociopro', 'femploye.ID_scociopro', '=', 'dscociopro.ID_scociopro')
    ->join('dtemps', 'femploye.ID_Date_Recrutement', '=', 'dtemps.ID_Temps')
    ->select(DB::raw('SUM(femploye.Nombre_Eff) as nb_employes'), DB::raw('CASE WHEN dscociopro.Scocipro = "Cadre superieur" THEN "cadre superieur" WHEN dscociopro.Scocipro = "Cadre" THEN "cadre" WHEN dscociopro.Scocipro = "Cadre dirigeant" THEN "cadre dirigeant" WHEN dscociopro.Scocipro = "maitrise" THEN "maitrise" WHEN dscociopro.Scocipro = "execution" THEN "execution" ELSE "retraite" END AS gestion_ressource'));

    $req02 = $res02
    ->groupBy('gestion_ressource')
    ->get(); 

    $res03 = DB::table('femploye')
    ->join('dscociopro', 'femploye.ID_scociopro', '=', 'dscociopro.ID_scociopro')
    ->join('dtemps', 'femploye.ID_Date_Recrutement', '=', 'dtemps.ID_Temps')
    ->select(DB::raw('SUM(femploye.Nombre_Eff) as nb_employes'), DB::raw('CASE WHEN dscociopro.Scocipro IN ("maitrise", "execution") THEN "personnelle technique" WHEN dscociopro.Scocipro NOT IN ("maitrise", "execution") THEN "personnelle administratifs" END AS gestion_ressource'));

    $req03 = $res03
    ->groupBy('gestion_ressource')
    ->get();

    $NBEmployes = DB::table('femploye')
    ->select(DB::raw('SUM(Nombre_Eff) as nb_employes'))
    ->join('dtemps', 'femploye.ID_Date_Recrutement', '=', 'dtemps.ID_Temps');

    $req04 = $NBEmployes
    ->get();

    if (!empty($year) && !empty($filiale)) {
        $req01 = $res01
        ->where('dtemps.annee', $year)
        ->where('femploye.ID_Ent', $filiale)
        ->groupBy('gestion_ressource')
        ->get();

        $req02 = $res02
        ->where('dtemps.annee', $year)
        ->where('femploye.ID_Ent', $filiale)
        ->groupBy('gestion_ressource')
        ->get();

        $req03 = $res03
        ->where('dtemps.annee', $year)
        ->where('femploye.ID_Ent', $filiale)
        ->groupBy('gestion_ressource')
        ->get();

        $req04 = $NBEmployes
        ->where('dtemps.annee', ">=", $year)
        ->where('femploye.ID_Ent', $filiale)
        ->get();

    } else {
        if (!empty($year)) {
            $req01 = $res01
            ->where('dtemps.annee', $year)
            ->groupBy('gestion_ressource')
            ->get();

            $req02 = $res02
            ->where('dtemps.annee', $year)
            ->groupBy('gestion_ressource')
            ->get();

            $req03 = $res03
            ->where('dtemps.annee', $year)
            ->groupBy('gestion_ressource')
            ->get();   
            
            $req04 = $NBEmployes
            ->where('dtemps.annee', ">=", $year)
            ->get();
        } 
        if (!empty($filiale)) {
            $req01 = $res01
            ->where('femploye.ID_Ent', $filiale)
            ->groupBy('gestion_ressource')
            ->get();

            $req02 = $res02
            ->where('femploye.ID_Ent', $filiale)
            ->groupBy('gestion_ressource')
            ->get();

            $req03 = $res03
            ->where('femploye.ID_Ent', $filiale)
            ->groupBy('gestion_ressource')
            ->get(); 
            
            $req04 = $NBEmployes
            ->where('femploye.ID_Ent', $filiale)
            ->get();
        }
    }

    $result1 = $req01->map(function ($req) {
        $EBE = $req->gestion_ressource;
        $val = $req->nb_employes;

        return [
            'key' => $EBE,
            'val' => $val
        ];
    });

    $result2 = $req02->map(function ($req) {
        $EBE = $req->gestion_ressource;
        $val = $req->nb_employes;

        return [
            'key' => $EBE,
            'val' => $val
        ];
    });

    $result3 = $req03->map(function ($req) {
        $EBE = $req->gestion_ressource;
        $val = $req->nb_employes;

        return [
            'key' => $EBE,
            'val' => $val
        ];
    });


    $result = $result1->concat($result3);



    return response(["ebe1" => $result, "ebe2" => $result2, "total" => $req04]);
});


Route::get('fdettes_crences', function () {

    // Exécutez la requête initiale et récupérez les résultats
    // Exécutez la requête initiale et récupérez les résultats
    $results = DB::table('creances')
        ->select(
            'dtemps.ID_Temps AS ID_Temps',
            'dentreprise_A.ID_Ent AS ID_Ent_A',
            'dentreprise_B.ID_Ent AS ID_Ent_B',
            DB::raw('CASE WHEN DATE_FORMAT(creances.anteriorite_creance, "%Y-%m") >= DATE_FORMAT(CURRENT_DATE(), "%Y-%m") THEN SUM(creances.Montant - creances.montant_encaissement) ELSE 0 END AS Montant_Factures'),
            DB::raw('CASE WHEN DATE_FORMAT(creances.anteriorite_creance, "%Y-%m") < DATE_FORMAT(CURRENT_DATE(), "%Y-%m") THEN SUM(creances.Montant - creances.montant_encaissement) ELSE 0 END AS Montant_Creances'),
            DB::raw('CASE WHEN DATE_FORMAT(creances.anteriorite_creance, "%Y-%m") >= DATE_FORMAT(CURRENT_DATE(), "%Y-%m") THEN COUNT(creances.Montant) ELSE 0 END AS Nbr_Factures'),
            DB::raw('CASE WHEN DATE_FORMAT(creances.anteriorite_creance, "%Y-%m") < DATE_FORMAT(CURRENT_DATE(), "%Y-%m") THEN COUNT(creances.Montant) ELSE 0 END AS Nbr_Creances'),
            DB::raw('0 AS Montant_Dettes'),
            DB::raw('0 AS Nbr_Dettes'),
            DB::raw('0 AS Creances_vs_Dettes')
        )
        ->join('dtemps', DB::raw("DATE_FORMAT(CURRENT_DATE(), '%Y-%m')"), '=', DB::raw("DATE_FORMAT(dtemps.DATE, '%Y-%m')"))
        ->join('dentreprise AS dentreprise_A', 'creances.debtor_id', '=', 'dentreprise_A.ID_Ent')
        ->join('dentreprise AS dentreprise_B', 'creances.creditor_id', '=', 'dentreprise_B.ID_Ent')
        ->where('creances.regler', 0)
        ->groupBy('dentreprise_A.ID_Ent', 'dentreprise_B.ID_Ent')
        ->union(function ($query) {
            $query->select(
                'dtemps.ID_Temps AS ID_Temps',
                'dentreprise_A.ID_Ent AS ID_Ent_A',
                'dentreprise_B.ID_Ent AS ID_Ent_B',
                DB::raw('0 AS Montant_Factures'),
                DB::raw('0 AS Montant_Creances'),
                DB::raw('0 AS Nbr_Factures'),
                DB::raw('0 AS Nbr_Creances'),
                DB::raw('SUM(dettes.Montant - dettes.montant_encaissement) AS Montant_Dettes'),
                DB::raw('COUNT(dettes.Montant) AS Nbr_Dettes'),
                DB::raw('0 AS Creances_vs_Dettes')
            )
            ->from('dettes')
            ->join('dtemps', DB::raw("DATE_FORMAT(CURRENT_DATE(), '%Y-%m')"), '=', DB::raw("DATE_FORMAT(dtemps.DATE, '%Y-%m')"))
            ->join('dentreprise AS dentreprise_A', 'dettes.debtor_id', '=', 'dentreprise_A.ID_Ent')
            ->join('dentreprise AS dentreprise_B', 'dettes.creditor_id', '=', 'dentreprise_B.ID_Ent')
            ->where('dettes.regler', 0)
            ->groupBy('dentreprise_A.ID_Ent', 'dentreprise_B.ID_Ent');
        })
    ->groupBy('ID_Ent_A', 'ID_Ent_B')
    ->get();


    // Tableau pour stocker les agrégats
    $aggregatedResults = [];

    // Parcourez les résultats
    foreach ($results as $result) {
        $key = $result->ID_Ent_A . '_' . $result->ID_Ent_B . '_' . $result->ID_Temps;

        $resultArray = json_decode(json_encode($result), true);
        // Vérifie si la clé existe déjà dans le tableau des agrégats
        if (isset($aggregatedResults[$key])) {
            // Ajoute les valeurs agrégées aux résultats existants
            $aggregatedResults[$key]->Montant_Factures += $result->Montant_Factures;
            $aggregatedResults[$key]->Montant_Creances += $result->Montant_Creances;
            $aggregatedResults[$key]->Nbr_Factures += $result->Nbr_Factures;
            $aggregatedResults[$key]->Nbr_Creances += $result->Nbr_Creances;
            $aggregatedResults[$key]->Montant_Dettes += $result->Montant_Dettes;
            $aggregatedResults[$key]->Nbr_Dettes += $result->Nbr_Dettes;
            $aggregatedResults[$key]->Creances_vs_Dettes = $aggregatedResults[$key]->Montant_Creances
            - $aggregatedResults[$key]->Montant_Dettes;
        } 
        else {
            // Crée une nouvelle entrée dans le tableau des agrégats
            $aggregatedResults[$key] = (object) $resultArray;
            $aggregatedResults[$key]->Montant_Factures = $result->Montant_Factures;
            $aggregatedResults[$key]->Montant_Creances = $result->Montant_Creances;
            $aggregatedResults[$key]->Nbr_Factures = $result->Nbr_Factures;
            $aggregatedResults[$key]->Nbr_Creances = $result->Nbr_Creances;
            $aggregatedResults[$key]->Montant_Dettes = $result->Montant_Dettes;
            $aggregatedResults[$key]->Nbr_Dettes = $result->Nbr_Dettes;
            $aggregatedResults[$key]->Creances_vs_Dettes = $result->Montant_Creances - $result->Montant_Dettes;      
        }
    }

    // Convertit le tableau des agrégats en une liste de résultats
    $finalResults = array_values($aggregatedResults);
    // $results = $finalResults;

    $aggregatedResultsArray = json_decode(json_encode($aggregatedResults), true);

    $query = DB::table('fcreances_dettes')->insert($aggregatedResultsArray);
    if ($query) {
        echo "Les données ont bien été transformées.";                             
    } else { 
        echo "Une erreur s'est produite lors du chargement des données.";              
    }
});



Route::get('dash_creance_dettes', function () {

    $results = DB::table('fcreances_dettes')
    ->groupBy('ID_Ent_A', 'ID_Ent_B')
    ->get();

    $req = [];

    for ($i = 1; $i < 19; $i++) {
        for ($j = 1; $j < 19; $j++) {
            $key = $i . '-' . $j;

            $filteredResult = $results->where('ID_Ent_A', $i)->where('ID_Ent_B', $j)->first();

            if ($filteredResult) {
                $req[$key] = $filteredResult;

            } else {
                $req[$key] = (object) [
                    'ID_Ent_A' => $i,
                    'ID_Ent_B' => $j,
                    'Montant_Factures' => 0,
                    'Montant_Creances' => 0,
                    'Nbr_Factures' => 0,
                    'Nbr_Creances' => 0,
                    'Montant_Dettes' => 0,
                    'Nbr_Dettes' => 0,
                    'Creances_vs_Dettes' => 0
                ];
            }
        }
    }

    $finalResults = array_values($req);
    return $finalResults;
});



Route::get('test_finance', function () {

    $results = DB::table('ffinance')
    ->join('dtemps', 'ffinance.ID_Date_Agregats', '=', 'dtemps.ID_Temps')
    ->join('dagregats', 'ffinance.ID_Agregats', '=', 'dagregats.ID_Agregats')
    ->select(DB::raw('dtemps.Annee AS ANNEE, dagregats.Type_Agregats AS TYPE_A, ffinance.taux_Realisation * 100 as taux'))
    ->groupBy('ANNEE', 'TYPE_A')
    ->get();

    $dataArray = json_decode($results, true);
    // Initialiser le tableau final
    $result = [];
    $allYears = range(2016, 2023);


    // Parcourir le tableau associatif
    foreach ($dataArray as $item) {
        // Récupérer les informations importantes
        $id = $item["TYPE_A"];
        $x = $item["ANNEE"];
        $y = $item["taux"];

        // Définir la couleur en fonction de l'identifiant
        switch ($id) {
            case "vente":
                $color = "rgb(244, 117, 96)";
                break;
            case "consomation":
                $color = "rgb(241, 225, 91)";
                break;
            case "production":
                $color = "rgb(232, 193, 160)";
                break;
            case "autre":
                $color = "rgb(131, 109, 90)";
                break;
            default:
                $color = "rgb(0, 0, 0)";
                break;
        }

        // Vérifier si l'identifiant existe déjà dans le tableau final
        $key = array_search($id, array_column($result, "id"));
        if ($key !== false) {
            // Ajouter les données à l'élément existant
            $data = ["x" => $x, "y" => $y];
            array_push($result[$key]["data"], $data);
        } else {
            // Ajouter un nouvel élément au tableau final
            $element = ["id" => $id, "color" => $color, "data" => []];
    
            // Remplir les données pour toutes les années entre 2016 et 2023
            foreach ($allYears as $year) {
                // Vérifier si l'année existe dans les données récupérées
                if ($results->where('TYPE_A', $id)->where('ANNEE', $year)->all() !== []) {
                    // Ajouter la valeur réelle pour l'année correspondante
                    if ($year === $x) {
                        $data = ["x" => $year, "y" => $y];
                        array_push($element["data"], $data);
                    }
                } else {
                    // Ajouter la valeur 0 pour les années manquantes
                    $data = ["x" => $year, "y" => 0];

                    if (!in_array($element["data"], $data)) {
                        array_push($element["data"], $data);
                    }
                }
                
            }
            // Ajouter l'élément au tableau final
            array_push($result, $element);
        }
    }

    // Transformer le tableau final en chaîne JSON
    $resultJson = json_encode($result);
    // Afficher le résultat
    return $result;
    // 16 / 18 / 19 / 22 / 23

    /*
    
    $data = ["x" => 2018, "y" => 0];
    $tab = collect($result)->where("id", "autre")->pluck('data')[0];
    $bool = in_array($data, $tab);    
    */
});


Route::get('RHS', function () {
    $NBEmployes = DB::table('femploye')
        ->select(DB::raw('SUM(Nombre_Eff) as nb_employes'))
        ->get();
    return $NBEmployes;

});




Route::get("UpdateFemploye", function () {


    $timestemp = DB::table('controller_stamp')->max('last_timp_stamp');
    $lastTimestamp = DB::table('employes')->max('updated_at');
    
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
                WHEN TIMESTAMPDIFF(YEAR, employes.date_naissance, CURDATE()) BETWEEN 26 AND 30 THEN 3
                WHEN TIMESTAMPDIFF(YEAR, employes.date_naissance, CURDATE()) BETWEEN 31 AND 35 THEN 4
                WHEN TIMESTAMPDIFF(YEAR, employes.date_naissance, CURDATE()) BETWEEN 36 AND 40 THEN 5
                WHEN TIMESTAMPDIFF(YEAR, employes.date_naissance, CURDATE()) BETWEEN 41 AND 45 THEN 6
                WHEN TIMESTAMPDIFF(YEAR, employes.date_naissance, CURDATE()) BETWEEN 46 AND 50 THEN 7
                WHEN TIMESTAMPDIFF(YEAR, employes.date_naissance, CURDATE()) BETWEEN 51 AND 55 THEN 8
                WHEN TIMESTAMPDIFF(YEAR, employes.date_naissance, CURDATE()) BETWEEN 56 AND 60 THEN 9
                ELSE 10
            END AS ID_Age'),
            DB::raw('COUNT(employes.id) as Nombre_Eff')
        )
        ->where('employes.updated_at', '>', $timestemp)
        ->groupBy('ID_Temps', 'ID_Date_Recrutement', 'ID_Date_Retraite', 'ID_Fonction', 'ID_Sexe', 'ID_Handicap', 'ID_Ent', 'ID_Temps_Trav', 'ID_Contrat', 'ID_Age')
        ->get();
    
    $TransformRequest = $results->map(function ($result) {
        $id0 = $result->ID_Temps;
        $id1 = $result->ID_Date_Recrutement;
        $id2 = $result->ID_Date_Retraite;
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
    
    $values = $TransformRequest->toArray();
    
    $query = DB::transaction(function () use ($values) {
        foreach ($values as $data) {
            $existingRecord = DB::table('femploye')
                ->where([
                    'ID_Age' => $data['ID_Age'],
                    'ID_Contrat' => $data['ID_Contrat'],
                    'ID_Date_Recrutement' => $data['ID_Date_Recrutement'],
                    'ID_Date_Retraite' => $data['ID_Date_Retraite'],
                    'ID_Ent' => $data['ID_Ent'],
                    'ID_Fonction' => $data['ID_Fonction'],
                    'ID_Handicap' => $data['ID_Handicap'],
                    'ID_Sexe' => $data['ID_Sexe'],
                    'ID_Temps' => $data['ID_Temps'],
                    'ID_Temps_Trav' => $data['ID_Temps_Trav'],
                    'ID_scociopro' => $data['ID_scociopro'],
                ])
                ->first();
    
            if ($existingRecord) {
                DB::table('femploye')
                    ->where('ID_Age', $data['ID_Age'])
                    ->where('ID_Contrat', $data['ID_Contrat'])
                    ->where('ID_Date_Recrutement', $data['ID_Date_Recrutement'])
                    ->where('ID_Date_Retraite', $data['ID_Date_Retraite'])
                    ->where('ID_Ent', $data['ID_Ent'])
                    ->where('ID_Fonction', $data['ID_Fonction'])
                    ->where('ID_Handicap', $data['ID_Handicap'])
                    ->where('ID_Sexe', $data['ID_Sexe'])
                    ->where('ID_Temps', $data['ID_Temps'])
                    ->where('ID_Temps_Trav', $data['ID_Temps_Trav'])
                    ->where('ID_scociopro', $data['ID_scociopro'])
                    ->increment('Nombre_Eff', 1);
                return true;

            } else {
                DB::table('femploye')->insert($data);
                return true;
            }
        }
    });

 

    if ($query) {
        echo "Les données ont bien été transformées."; 
        DB::table('controller_stamp')
         ->where('table_stamp', '=', 'employes')
         ->update(['last_timp_stamp' => $lastTimestamp]);                            
    } else { 
        echo "Une erreur s'est produite lors du chargement des données.";              
    }
        
});


// [{"ID_Temps":1482,"ID_Date_Recrutement":1417,"ID_Date_Retraite":"2413","ID_Sexe":1,"ID_Handicap":1,"ID_Fonction":12,"ID_Ent":1,"ID_Temps_Trav":1,"ID_Contrat":2,"ID_scociopro":3,"ID_Age":3,"Nombre_Eff":1}]


Route::get('Updateffinance', function () {

    $timestemp = DB::table('controller_stamp')
    ->where('table_stamp', 'finances')
    ->max('last_timp_stamp');
    $lastTimestamp = DB::table('finances')->max('updated_at');


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
        ->where('finances.updated_at','>', $timestemp)
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
        DB::table('controller_stamp')
            ->where('table_stamp', '=', 'finances')
            ->update(['last_timp_stamp' => $lastTimestamp]);
        echo "Les données ont bien été transformées.";                             
    } else { 
        echo "Une erreur s'est produite lors du chargement des données.";              
    }

});







Route::get('UpdateFormation', function () {

    $timestemp = DB::table('controller_stamp')
    ->where('table_stamp', 'formations')
    ->max('last_timp_stamp');
    $lastTimestamp = DB::table('formations')->max('updated_at');

    $results = DB::table('formations')
    ->join('employes', 'formations.employe_id', '=', 'employes.id')
    ->join('dtemps', DB::raw("DATE_FORMAT(formations.created_at, '%Y-%m')"), '=', DB::raw("DATE_FORMAT(dtemps.DATE, '%Y-%m')"))
    ->join('ddomaine', 'formations.domaine_formation', '=', 'ddomaine.Domaine')
    ->join('dentreprise', 'employes.filiale_id', '=', 'dentreprise.ID_Ent')
    ->select(
        'dtemps.ID_Temps as ID_Temps',
        'dentreprise.ID_Ent as ID_Ent',
        'ddomaine.ID_Domaine as ID_Domaine',
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
        END AS ID_Age'),
        DB::raw('CASE
            WHEN formations.duree_formation < 15 THEN 1
            WHEN formations.duree_formation BETWEEN 15 AND 29 THEN 2 
            WHEN formations.duree_formation BETWEEN 30 AND 44 THEN 3
            WHEN formations.duree_formation BETWEEN 45 AND 59 THEN 4
            WHEN formations.duree_formation BETWEEN 60 AND 74 THEN 5
            WHEN formations.duree_formation BETWEEN 75 AND 89 THEN 6
            ELSE 7
        END as ID_Duree'),
        DB::raw('COUNT(employes.id) as Nombre_Eff'),
        DB::raw('SUM(formations.montant) as Montant')
    )
    ->where('formations.updated_at','>', $timestemp)
    ->groupBy('ID_Temps', 'ID_Ent', 'ID_Domaine', 'ID_Duree', 'ID_Age')
    ->get();

    $TransformRequest = $results->map(function ($result) {
        $id0 = $result->ID_Temps;
        $id1 = $result->ID_Ent;
        $id2 =  $result->ID_Domaine;
        $id4 = $result->ID_Duree;
        $id5 = $result->ID_Age;
        $Nombre_Eff = $result->Nombre_Eff;
        $Montant = $result->Montant;
        return [
            'ID_Temps' => $id0,
            'ID_Ent' => $id1,
            'ID_Domaine' => $id2,
            'ID_Duree' => $id4,
            'ID_Age' => $id5,
            'Nombre_Eff' => $Nombre_Eff,
            'Montant' => $Montant
        ];
    });

    $query = DB::table('fformation')->insert($TransformRequest->toArray());
    if ($query) {
        DB::table('controller_stamp')
        ->where('table_stamp', '=', 'formations')
        ->update(['last_timp_stamp' => $lastTimestamp]);
        echo "Les données ont bien été transformées.";                             
    } else { 
        echo "Une erreur s'est produite lors du chargement des données.";              
    }
});





Route::get('Updatafdettes_crences', function () {

    $timestemp = DB::table('controller_stamp')
    ->where('table_stamp', 'creances_dettes')
    ->max(DB::raw("DATE_FORMAT(last_timp_stamp, '%Y-%m')"));
    $lastTimestamp = DB::raw("DATE_FORMAT(CURRENT_DATE(), '%Y-%m')");

    $results = DB::table('creances')
    ->select(
        'dtemps.ID_Temps AS ID_Temps',
        'dentreprise_A.ID_Ent AS ID_Ent_A',
        'dentreprise_B.ID_Ent AS ID_Ent_B',
        DB::raw('CASE WHEN DATE_FORMAT(creances.anteriorite_creance, "%Y-%m") >= DATE_FORMAT(CURRENT_DATE(), "%Y-%m") THEN SUM(creances.Montant - creances.montant_encaissement) ELSE 0 END AS Montant_Factures'),
        DB::raw('CASE WHEN DATE_FORMAT(creances.anteriorite_creance, "%Y-%m") < DATE_FORMAT(CURRENT_DATE(), "%Y-%m") THEN SUM(creances.Montant - creances.montant_encaissement) ELSE 0 END AS Montant_Creances'),
        DB::raw('CASE WHEN DATE_FORMAT(creances.anteriorite_creance, "%Y-%m") >= DATE_FORMAT(CURRENT_DATE(), "%Y-%m") THEN COUNT(creances.Montant) ELSE 0 END AS Nbr_Factures'),
        DB::raw('CASE WHEN DATE_FORMAT(creances.anteriorite_creance, "%Y-%m") < DATE_FORMAT(CURRENT_DATE(), "%Y-%m") THEN COUNT(creances.Montant) ELSE 0 END AS Nbr_Creances'),
        DB::raw('0 AS Montant_Dettes'),
        DB::raw('0 AS Nbr_Dettes'),
        DB::raw('0 AS Creances_vs_Dettes')
    )
    ->join('dtemps', DB::raw("DATE_FORMAT(CURRENT_DATE(), '%Y-%m')"), '=', DB::raw("DATE_FORMAT(dtemps.DATE, '%Y-%m')"))
    ->join('dentreprise AS dentreprise_A', 'creances.debtor_id', '=', 'dentreprise_A.ID_Ent')
    ->join('dentreprise AS dentreprise_B', 'creances.creditor_id', '=', 'dentreprise_B.ID_Ent')
    ->where('creances.regler', 0)
    ->groupBy('dentreprise_A.ID_Ent', 'dentreprise_B.ID_Ent')
    ->union(function ($query) {
        $query->select(
            'dtemps.ID_Temps AS ID_Temps',
            'dentreprise_A.ID_Ent AS ID_Ent_A',
            'dentreprise_B.ID_Ent AS ID_Ent_B',
            DB::raw('0 AS Montant_Factures'),
            DB::raw('0 AS Montant_Creances'),
            DB::raw('0 AS Nbr_Factures'),
            DB::raw('0 AS Nbr_Creances'),
            DB::raw('SUM(dettes.Montant - dettes.montant_encaissement) AS Montant_Dettes'),
            DB::raw('COUNT(dettes.Montant) AS Nbr_Dettes'),
            DB::raw('0 AS Creances_vs_Dettes')
        )
        ->from('dettes')
        ->join('dtemps', DB::raw("DATE_FORMAT(CURRENT_DATE(), '%Y-%m')"), '=', DB::raw("DATE_FORMAT(dtemps.DATE, '%Y-%m')"))
        ->join('dentreprise AS dentreprise_A', 'dettes.debtor_id', '=', 'dentreprise_A.ID_Ent')
        ->join('dentreprise AS dentreprise_B', 'dettes.creditor_id', '=', 'dentreprise_B.ID_Ent')
        ->where('dettes.regler', 0)
        ->groupBy('dentreprise_A.ID_Ent', 'dentreprise_B.ID_Ent');
    })
    // ->whereRaw("DATE_FORMAT(CURRENT_DATE(), '%Y-%m') != ?", [$timestemp])
    ->groupBy('ID_Ent_A', 'ID_Ent_B')
    ->get();


    
    
    // Tableau pour stocker les agrégats
    $aggregatedResults = [];
    
    // Parcourez les résultats
    foreach ($results as $result) {
        $key = $result->ID_Ent_A . '_' . $result->ID_Ent_B . '_' . $result->ID_Temps;
    
        $resultArray = json_decode(json_encode($result), true);
        // Vérifie si la clé existe déjà dans le tableau des agrégats
        if (isset($aggregatedResults[$key])) {
            // Ajoute les valeurs agrégées aux résultats existants
            $aggregatedResults[$key]->Montant_Factures += $result->Montant_Factures;
            $aggregatedResults[$key]->Montant_Creances += $result->Montant_Creances;
            $aggregatedResults[$key]->Nbr_Factures += $result->Nbr_Factures;
            $aggregatedResults[$key]->Nbr_Creances += $result->Nbr_Creances;
            $aggregatedResults[$key]->Montant_Dettes += $result->Montant_Dettes;
            $aggregatedResults[$key]->Nbr_Dettes += $result->Nbr_Dettes;
            $aggregatedResults[$key]->Creances_vs_Dettes = $aggregatedResults[$key]->Montant_Creances
            - $aggregatedResults[$key]->Montant_Dettes;
        } 
        else {
            // Crée une nouvelle entrée dans le tableau des agrégats
            $aggregatedResults[$key] = (object) $resultArray;
            $aggregatedResults[$key]->Montant_Factures = $result->Montant_Factures;
            $aggregatedResults[$key]->Montant_Creances = $result->Montant_Creances;
            $aggregatedResults[$key]->Nbr_Factures = $result->Nbr_Factures;
            $aggregatedResults[$key]->Nbr_Creances = $result->Nbr_Creances;
            $aggregatedResults[$key]->Montant_Dettes = $result->Montant_Dettes;
            $aggregatedResults[$key]->Nbr_Dettes = $result->Nbr_Dettes;
            $aggregatedResults[$key]->Creances_vs_Dettes = $result->Montant_Creances - $result->Montant_Dettes;      
        }
    }
    
    // Convertit le tableau des agrégats en une liste de résultats
    $finalResults = array_values($aggregatedResults);
    // $results = $finalResults;
    
    $aggregatedResultsArray = json_decode(json_encode($aggregatedResults), true);
    
    $query = DB::table('fcreances_dettes')->insert($aggregatedResultsArray);
    if ($query) {
        echo "Les données ont bien été transformées.";     
        DB::table('controller_stamp')
        ->where('table_stamp', '=', 'dettes_creances')
        ->update(['last_timp_stamp' => $lastTimestamp]);                        
    } else { 
        echo "Une erreur s'est produite lors du chargement des données.";              
    }

    // return $aggregatedResultsArray;
});
    


































Route::get('finance_dashboard', function () {

    $year = 2020;
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
    // ->where('dtemps.annee', 2016)
    ->groupBy('Agregat_calculer')
    ->get();

    $NBEmployes = DB::table('femploye')
    ->select(DB::raw('SUM(Nombre_Eff) as nb_employes'))
    ->join('dtemps', 'femploye.ID_Date_Recrutement', '=', 'dtemps.ID_Temps');

    $req04 = $NBEmployes
    ->get();

    if (!empty($year) && !empty($filiale)) {
        $resultats = $test
        ->where('dtemps.annee', $year)
        ->where('ffinance.ID_Ent', $filiale)
        ->groupBy('Agregat_calculer')
        ->get();

        $req04 = $NBEmployes
        ->where('dtemps.annee', '<=', $year)
        ->where('femploye.ID_Ent', $filiale)
        ->get();
    } else {
        if (!empty($year)) {
            $resultats = $test
            ->where('dtemps.annee', $year)
            ->groupBy('Agregat_calculer')
            ->get();  
            
            $req04 = $NBEmployes
            ->where('dtemps.annee', '<=', $year)
            ->get();
        } 
        if (!empty($filiale)) {
            $resultats = $test
            ->where('ffinance.ID_Ent', $filiale)
            ->groupBy('Agregat_calculer')
            ->get();     
            
            $req04 = $NBEmployes
            ->where('femploye.ID_Ent', $filiale)
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

    $request_ = DB::table('ffinance')
    ->join('dagregats', 'ffinance.ID_Agregats', '=', 'dagregats.ID_Agregats')
    ->join('dtemps', 'ffinance.ID_Date_Agregats', '=', 'dtemps.ID_Temps')
    ->select(
        DB::raw('ffinance.taux_Realisation * 100 AS taux'),
        DB::raw('SUM(Montant_Realisation) AS Montant_Realisation'),
        DB::raw('SUM(Montant_Privision) AS Montant_Privision'),
        DB::raw('SUM(Ecart_Valeur) AS Ecart_Valeur'),
        DB::raw("CASE
            WHEN dagregats.agregats = 'Charges de personnel' THEN 'charge'
            WHEN dagregats.agregats = 'Impots, taxes et versements assimiles' THEN 'impots'
            ELSE '+++'
        END AS Agregat_calculer")
    )
    ->groupBy('Agregat_calculer')
    ->get();

    $charge = $request_->where('Agregat_calculer', 'charge')->first();
    $impot = $request_->where('Agregat_calculer', 'impots')->first();
    $maitrise_consomation = [];
    $marge_operationnelle = [];
    $taux_marge = [];
    $taux_va = [];
    $va_etats = [];
    $va_salarier = [];
    $kpi_finance = [];
    $rendement_employe=[];
    $effictif_prod=[];
    $taux_personne=[];



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

        // $maitrise_consomation["Montant_Realisation"] = "-";
        // $maitrise_consomation["Montant_Privision"] = "-";
        // $maitrise_consomation["Ecart_Valeur"] = "-";
        // $maitrise_consomation["Agregat_calculer"] = 'maitrise de consomation';
        // $maitrise_consomation["taux"] = "-";

        // $taux_va["Montant_Realisation"] = "-";
        // $taux_va["Montant_Privision"] = "-";
        // $taux_va["Ecart_Valeur"] = "-";
        // $taux_va["Agregat_calculer"] = 'taux de va';
        // $taux_va["taux"] = "-";

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

        $mp1 = $pp1 - $cp1;
        $mp2 = $pp2 - $cp2;
        if ($mp1 < 0) {
            $Ecart_Valeur1 = $mp1 + $mp2;
        } else {
            $Ecart_Valeur1 = $mp1 - $mp2;
        }

        $va["Montant_Realisation"] = $mp1;
        $va["Montant_Privision"] = $mp2;
        $va["Ecart_Valeur"]= $Ecart_Valeur1;
        $va["Agregat_calculer"] = "Valeur Ajoute";
        $va["taux"] = round(abs(floatval($mp1 / $mp2)) * 100, 2);

        // $maitrise_consomation['Montant_Realisation'] = round(($va['Montant_Realisation'] / $pp1), 2);
        // $maitrise_consomation['Montant_Privision'] = round(($va['Montant_Privision'] / $pp2), 2);
        // $maitrise_consomation['Ecart_Valeur'] = round(($va['Ecart_Valeur'] / $Ecart_Valeur), 2);
        // $maitrise_consomation['Agregat_calculer'] = "maitrise de consomation";
        // $maitrise_consomation['taux'] = round(($va['taux'] / $taux), 2); 
        
        // if (!$taux_va) {
        //     $ca1 = $Chiffre_affaire->Montant_Realisation;
        //     $ca2 = $Chiffre_affaire->Montant_Privision;
        //     $ca3 = $Chiffre_affaire->Ecart_Valeur;
        //     $ca4 = $Chiffre_affaire->taux;
    
        //     $taux_va['Montant_Realisation'] = round(($va["Montant_Realisation"]/$ca1), 2);
        //     $taux_va['Montant_Privision'] = round(($va["Montant_Privision"]/$ca2), 2);
        //     $taux_va['Ecart_Valeur'] = round(($va["Ecart_Valeur"]/$ca3), 2);
        //     $taux_va['Agregat_calculer'] = "taux de va";
        //     $taux_va['taux'] = round(($va["taux"]/$ca4), 2); 
        // }

    } else {
        $va["Montant_Realisation"] = "-";
        $va["Montant_Privision"] = "-";
        $va["Ecart_Valeur"] = "-";
        $va["Agregat_calculer"] = "Valeur Ajoute";
        $va["taux"] = "-";

        // $maitrise_consomation['Montant_Realisation'] = '-';
        // $maitrise_consomation['Montant_Privision'] = '-';
        // $maitrise_consomation['Ecart_Valeur'] = '-';
        // $maitrise_consomation['Agregat_calculer'] = "maitrise de consomation";
        // $maitrise_consomation['taux'] = '-';   

        // $taux_va['Montant_Realisation'] = '-';
        // $taux_va['Montant_Privision'] = '-';
        // $taux_va['Ecart_Valeur'] = '-';
        // $taux_va['Agregat_calculer'] = "taux de va";
        // $taux_va['taux'] = '-';  

        // $va_salarier['Montant_Realisation'] = "-";
        // $va_salarier['Montant_Privision'] = "-";
        // $va_salarier['Ecart_Valeur'] = "-";
        // $va_salarier['Agregat_calculer'] = "va des salriers";
        // $va_salarier['taux'] = "-"; 

        // $va_etats['Montant_Realisation'] = "-";
        // $va_etats['Montant_Privision'] = "-";
        // $va_etats['Ecart_Valeur'] = "-";
        // $va_etats['Agregat_calculer'] = "va des salriers";
        // $va_etats['taux'] = "-";  
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

        $mp1 = $pp1 - $ca1;
        $mp2 = $pp2 - $ca2;
        if ($mp2 < 0) {
            $Ecart_Valeur = $mp1 + $mp2;
        } else {
            $Ecart_Valeur = $mp1 - $mp2;
        }        
        // $EBE = $Ecart_Valeur - $pp2;
        $taux = round(floatval($pp1 / $pp2) * 100, 2);

        $EBE->Montant_Realisation = $mp1;
        $EBE->Montant_Privision = $mp2;
        $EBE->Ecart_Valeur = $Ecart_Valeur;
        $EBE->Agregat_calculer = "EBE";
        $EBE->taux = round(floatval($mp1 / $mp2) * 100, 2);
        
        // $taux_marge['Montant_Realisation'] = $ca1 !== 0 ? round(($pp1 / $ca1), 2) : '-';
        // $taux_marge['Montant_Privision'] = $ca2 !== 0 ? round(($pp2 / $ca2), 2) : '-';
        // $taux_marge['Ecart_Valeur'] = round(($Ecart_Valeur / intval($va['Ecart_Valeur'])), 2);
        // $taux_marge['Agregat_calculer'] = "taux de marge";
        // $taux_marge['taux'] = round(($taux / floatval($va['taux'])), 2);    

        // if (!$marge_operationnelle) {
        //     $ca1 = $Chiffre_affaire->Montant_Realisation;
        //     $ca2 = $Chiffre_affaire->Montant_Privision;
        //     $ca3 = $Chiffre_affaire->Ecart_Valeur;
        //     $ca4 = $Chiffre_affaire->taux;  

        //     $marge_operationnelle['Montant_Realisation'] = round(($pp1/$ca1), 2);
        //     $marge_operationnelle['Montant_Privision'] = round(($pp2/$ca2), 2);
        //     $marge_operationnelle['Ecart_Valeur'] = round(($Ecart_Valeur/$ca3), 2);
        //     $marge_operationnelle['Agregat_calculer'] = "marge operationnelle";
        //     $marge_operationnelle['taux'] = round(($taux/$ca4), 2); 
        // }
    } 
    else {
        $EBE["Montant_Realisation"] = $va["Montant_Realisation"] ;
        $EBE["Montant_Privision"] = $va["Montant_Privision"] ;
        $EBE["Ecart_Valeur"] = $va["Ecart_Valeur"];
        $EBE["Agregat_calculer"] = "EBE";
        $EBE["taux"] = $va["taux"];  

        // if (!$marge_operationnelle) {
        //     $ca1 = $Chiffre_affaire->Montant_Realisation;
        //     $ca2 = $Chiffre_affaire->Montant_Privision;
        //     $ca3 = $Chiffre_affaire->Ecart_Valeur;
        //     $ca4 = $Chiffre_affaire->taux;
    
        //     $marge_operationnelle['Montant_Realisation'] = round(($va["Montant_Realisation"]/$ca1), 2);
        //     $marge_operationnelle['Montant_Privision'] = round(($va["Montant_Privision"]/$ca2), 2);
        //     $marge_operationnelle['Ecart_Valeur'] = round(($va["Ecart_Valeur"]/$ca3), 2);
        //     $marge_operationnelle['Agregat_calculer'] = "marge operationnelle";
        //     $marge_operationnelle['taux'] = round(($va["taux"]/$ca4), 2); 
        // }
        
        // $taux_marge['Montant_Realisation'] = 1;
        // $taux_marge['Montant_Privision'] = 1;
        // $taux_marge['Ecart_Valeur'] = 1;
        // $taux_marge['Agregat_calculer'] = "taux de marge";
        // $taux_marge['taux'] = 1;
    }

    // if ($charge && $va && !$va_salarier) {
    //     $c1 = $charge->Montant_Realisation;
    //     $c2 = $charge->Montant_Privision;
    //     $c3 = $charge->Ecart_Valeur;
    //     $c4 = $charge->taux;

    //     $va_salarier['Montant_Realisation'] = round(($c1/$va["Montant_Realisation"]), 2);
    //     $va_salarier['Montant_Privision'] = round(($c2/$va["Montant_Privision"]/$ca2), 2);
    //     $va_salarier['Ecart_Valeur'] = round(($c3/$va["Ecart_Valeur"]/$ca3), 2);
    //     $va_salarier['Agregat_calculer'] = "va des salariers";
    //     $va_salarier['taux'] = round(($c4/$va["taux"]), 2); 
    // } else {
    //     $va_salarier['Montant_Realisation'] = "-";
    //     $va_salarier['Montant_Privision'] = "-";
    //     $va_salarier['Ecart_Valeur'] = "-";
    //     $va_salarier['Agregat_calculer'] = "va des salriers";
    //     $va_salarier['taux'] = "-"; 
    // }

    // if ($impot && $va && !$va_etats) {
    //     $c1 = $impot->Montant_Realisation;
    //     $c2 = $impot->Montant_Privision;
    //     $c3 = $impot->Ecart_Valeur;
    //     $c4 = $impot->taux;

    //     $va_etats['Montant_Realisation'] = round(($c1/$va["Montant_Realisation"]), 2);
    //     $va_etats['Montant_Privision'] = round(($c2/$va["Montant_Privision"]/$ca2), 2);
    //     $va_etats['Ecart_Valeur'] = round(($c3/$va["Ecart_Valeur"]/$ca3), 2);
    //     $va_etats['Agregat_calculer'] = "va des salariers";
    //     $va_etats['taux'] = round(($c4/$va["taux"]), 2); 
    // } else {
    //     $va_etats['Montant_Realisation'] = "-";
    //     $va_etats['Montant_Privision'] = "-";
    //     $va_etats['Ecart_Valeur'] = "-";
    //     $va_etats['Agregat_calculer'] = "va des salriers";
    //     $va_etats['taux'] = "-"; 
    // }

        

    // $bool = false;
    if (is_numeric($va['Montant_Realisation']) && !is_array($Production_Periode))
    {
        $val1 = floatval($va['Montant_Realisation']);
        $val2 = floatval($Production_Periode->Montant_Realisation);
        $val = round($val1 / $val2, 2);
        $maitrise_consomation = ['label' => 'maitrise de consomation', 'val' => $val];
    } else {
        $maitrise_consomation = ['label' => 'maitrise de consomation', 'val' => "-"];
    }

    if (!is_array($Chiffre_affaire) && !is_array($EBE)) {
        $val1 = floatval($EBE->Montant_Realisation);
        $val2 = floatval($Chiffre_affaire->Montant_Realisation);
        $val = round($val1 / $val2, 2);
        $marge_operationnelle = ['label' => 'marge operationnelle', 'val' => $val];
    } else {
        $marge_operationnelle = ['label' => 'marge operationnelle', 'val' => "-"];
    }

    if (is_numeric($va['Montant_Realisation']) && !is_array($EBE)) {

        $val1 = floatval($EBE->Montant_Realisation);        
        $val2 = floatval($va['Montant_Realisation']);
        $val = round($val1 / $val2, 2);
        $maitrise_consomation = ['label' => 'marge operationnelle', 'val' => $val];
    } else {
        $maitrise_consomation = ['label' => 'marge operationnelle', 'val' => "-"];
    }

    if (is_numeric($va['Montant_Realisation']) && !is_array($Chiffre_affaire))
    {
        $val1 = floatval($va['Montant_Realisation']);
        $val2 = floatval($Chiffre_affaire->Montant_Realisation);
        $val = round($val1 / $val2, 2);
        $taux_va = ['label' => 'taux de va', 'val' => $val];
    } else {
        $taux_va = ['label' => 'taux de va', 'val' => "-"];
    }

    if (is_numeric($va['Montant_Realisation']) && !is_array($impot))
    {
        $val1 = floatval($va['Montant_Realisation']);
        $val2 = floatval($impot->Montant_Realisation);
        $val = round($val2 / $val1, 2);
        $va_etats = ['label' => "va revenant de l'etat", 'val' => $val];
    } else {
        $va_etats = ['label' => "va revenant de l'etat", 'val' => "-"];
    }

    if (is_numeric($va['Montant_Realisation']) && !is_array($charge))
    {
        $val1 = floatval($va['Montant_Realisation']);
        $val2 = floatval($charge->Montant_Realisation);
        $val = round($val2 / $val1, 2);
        $va_salarier = ['label' => "va revenant aux salrier", 'val' => $val];
    } else {
        $va_salarier = ['label' => "va revenant aux salarie", 'val' => "-"];
    }

    if (($req04[0]->nb_employes) && !is_array($Chiffre_affaire))
    {
        $val1 = floatval($Chiffre_affaire->Montant_Realisation);
        $val2 = floatval($req04[0]->nb_employes);
        $val = round($val1 / $val2, 2);
        $rendement_employe = ['label' => "taux d'efficience du personne", 'val' => $val];
    } else {
        $rendement_employe = ['label' => "taux d'efficience du personne", 'val' => "-"];
    }
    if (($req04[0]->nb_employes) && !is_array($Production_Periode)) 
    {
        $val1 = floatval($Production_Periode->Montant_Realisation);
        $val2 = floatval($req04[0]->nb_employes);
        $val = round($val1 / $val2, 2);
        $effictif_prod = ['label' => "taux d'efficience du personne", 'val' => $val];
    } else {
        $effictif_prod = ['label' => "taux d'efficience du personne", 'val' => "-"];
    }
     

    $kpi_finance = [$maitrise_consomation, $taux_marge, $taux_va, $marge_operationnelle,
     $va_salarier, $va_etats, $taux_personne, $effictif_prod, $rendement_employe];

    $agregat_finance = [$Production_Periode,
     $Consommation_Periode, $Chiffre_affaire, $va, $EBE];

    return (['tab1' => $agregat_finance,'tab2' => $kpi_finance]);

    // return $req04;

});




















































































































Route::get('fcreances_dettes', function () {

    $results = DB::table('fcreances_dettes')
    ->groupBy('ID_Ent_A', 'ID_Ent_B')
    ->get();

    $req = [];

    $date = "";
    $filiale = 1;


    for ($i = 1; $i < 19; $i++) {
        for ($j = 1; $j < 19; $j++) {
            $key = $i . '-' . $j;

            $filteredResult = $results->where('ID_Ent_A', $i)->where('ID_Ent_B', $j)->first();

            if ($filteredResult) {
                $req[$key] = $filteredResult;

            } else {
                $req[$key] = (object) [
                    'ID_Ent_A' => $i,
                    'ID_Ent_B' => $j,
                    'Montant_Factures' => 0,
                    'Montant_Creances' => 0,
                    'Nbr_Factures' => 0,
                    'Nbr_Creances' => 0,
                    'Montant_Dettes' => 0,
                    'Nbr_Dettes' => 0,
                    'Creances_vs_Dettes' => 0
                ];
            }
        }
    
    }

    $finalResults = array_values($req);

    if (!empty($date) or !empty($filiale)) {

        $req = [];

        if (!empty($date)) {
            $results = DB::table('fcreances_dettes')
            ->groupBy('ID_Ent_A', 'ID_Ent_B')
            ->where('fcreances_dettes.ID_Temps', $date)
            ->get();
        }

        if (!empty($filiale)) {
            for ($j = 1; $j < 19; $j++) {
                $key = $filiale . '-' . $j;
    
                $filteredResult = $results->where('ID_Ent_A', $filiale)->where('ID_Ent_B', $j)->first();
    
                if ($filteredResult) {
                    $req[$key] = $filteredResult;
    
                } else {
                    $req[$key] = (object) [
                        'ID_Ent_A' => $filiale,
                        'ID_Ent_B' => $j,
                        'Montant_Factures' => 0,
                        'Montant_Creances' => 0,
                        'Nbr_Factures' => 0,
                        'Nbr_Creances' => 0,
                        'Montant_Dettes' => 0,
                        'Nbr_Dettes' => 0,
                        'Creances_vs_Dettes' => 0
                    ];
                } 
            }

            $finalResults = array_values($req);
        }
    } 

// *********************************** groupe *****************************************************************

    $resultats = DB::table('fcreances_dettes')
    ->join('dentreprise', 'fcreances_dettes.ID_Ent_B', '=', 'dentreprise.ID_Ent')
    ->selectRaw('*, Grp_Ent')
    ->get();
    $req01 = [];

    $maxId = DB::table('dentreprise')->max('ID_Ent');
    $groupes = DB::table('dentreprise')->selectRaw('DISTINCT(Grp_Ent)')->get();

    foreach ($groupes as $groupe) {
        for ($i = 1; $i < 19; $i++) {
            $G = $groupe->Grp_Ent;
            $key = $i . "-" . $G;
            for ($j = 1; $j <= intval($maxId); $j++) { // Changement : inclusif (<=) au lieu de strictement inférieur (<)
                $resultat = $resultats
                    ->where('Grp_Ent', $G)
                    ->where('ID_Ent_A', $i)
                    ->where('ID_Ent_B', $j)
                    ->first(); // Changement : utilisez "first()" pour obtenir un seul résultat

                if ($resultat) {
                    if (isset($req01[$key])) {
                        $req01[$key]->Montant_Factures += $resultat->Montant_Factures;
                        $req01[$key]->Nbr_Factures += $resultat->Nbr_Factures;
                        $req01[$key]->Nbr_Creances += $resultat->Nbr_Creances;
                        $req01[$key]->Montant_Creances += $resultat->Montant_Creances;
                        $req01[$key]->Montant_Dettes += $resultat->Montant_Dettes;
                        $req01[$key]->Nbr_Dettes += $resultat->Nbr_Dettes;
                        $req01[$key]->Creances_vs_Dettes += $resultat->Creances_vs_Dettes;
                    } else {
                        $req01[$key] = (object) [
                            'ID_Ent_A' => $i,
                            'Grp_Ent' => $G,
                            'Montant_Factures' => $resultat->Montant_Factures,
                            'Montant_Creances' => 0,
                            'Nbr_Factures' => $resultat->Nbr_Factures,
                            'Nbr_Creances' => 0,
                            'Montant_Dettes' => $resultat->Montant_Dettes,
                            'Nbr_Dettes' => $resultat->Nbr_Dettes,
                            'Creances_vs_Dettes' => $resultat->Creances_vs_Dettes
                        ];
                    }
                } else {
                    if (!isset($req01[$key])) {
                        $req01[$key] = (object) [
                            'ID_Ent_A' => $i,
                            'Grp_Ent' => $G,
                            'Montant_Factures' => 0,
                            'Montant_Creances' => 0,
                            'Nbr_Factures' => 0,
                            'Nbr_Creances' => 0,
                            'Montant_Dettes' => 0,
                            'Nbr_Dettes' => 0,
                            'Creances_vs_Dettes' => 0
                        ];
                    }
                }
            }
        }
    }

    $finalResults2 = array_values($req01);
    $groupe_list = DB::table('dentreprise')->select('Grp_Ent')->distinct()->get();

    if (!empty($date) or !empty($filiale)) {
        if (!empty($date)) {
            $resultats = DB::table('fcreances_dettes')
            ->join('dentreprise', 'fcreances_dettes.ID_Ent_B', '=', 'dentreprise.ID_Ent')
            ->selectRaw('*, Grp_Ent')
            ->where('fcreances_dettes.ID_Temps', $date)
            ->get();
        }

        if (!empty($filiale)) {
            $req01 = [];
            foreach ($groupes as $g) {
                $G = $g->Grp_Ent;
                $key = $filiale . "-" . $G;
                for ($j = 1; $j <= intval($maxId); $j++) { // Changement : inclusif (<=) au lieu de strictement inférieur (<)
                    $resultat = $resultats
                        ->where('Grp_Ent', $G)
                        ->where('ID_Ent_A', $filiale)
                        ->where('ID_Ent_B', $j)
                        ->first(); // Changement : utilisez "first()" pour obtenir un seul résultat
    
                    if ($resultat) {
                        if (isset($req01[$key])) {
                            $req01[$key]->Montant_Factures += $resultat->Montant_Factures;
                            $req01[$key]->Nbr_Factures += $resultat->Nbr_Factures;
                            $req01[$key]->Nbr_Creances += $resultat->Nbr_Creances;
                            $req01[$key]->Montant_Creances += $resultat->Montant_Creances;
                            $req01[$key]->Montant_Dettes += $resultat->Montant_Dettes;
                            $req01[$key]->Nbr_Dettes += $resultat->Nbr_Dettes;
                            $req01[$key]->Creances_vs_Dettes += $resultat->Creances_vs_Dettes;
                        } else {
                            $req01[$key] = (object) [
                                'ID_Ent_A' => $filiale,
                                'Grp_Ent' => $G,
                                'Montant_Factures' => $resultat->Montant_Factures,
                                'Montant_Creances' => 0,
                                'Nbr_Factures' => $resultat->Nbr_Factures,
                                'Nbr_Creances' => 0,
                                'Montant_Dettes' => $resultat->Montant_Dettes,
                                'Nbr_Dettes' => $resultat->Nbr_Dettes,
                                'Creances_vs_Dettes' => $resultat->Creances_vs_Dettes
                            ];
                        }
                    } else {
                        if (!isset($req01[$key])) {
                            $req01[$key] = (object) [
                                'ID_Ent_A' => $filiale,
                                'Grp_Ent' => $G,
                                'Montant_Factures' => 0,
                                'Montant_Creances' => 0,
                                'Nbr_Factures' => 0,
                                'Nbr_Creances' => 0,
                                'Montant_Dettes' => 0,
                                'Nbr_Dettes' => 0,
                                'Creances_vs_Dettes' => 0
                            ];
                        }
                    }
                }
            }


            $finalResults2 = array_values($req01);
        }
    } 

    // return response(["data2" => $finalResults2, "data1" => $finalResults]);

// ************************************ Secteur *******************************************************************

    $request_sec = DB::table('fcreances_dettes')
    ->join('dentreprise', 'fcreances_dettes.ID_Ent_B', '=', 'dentreprise.ID_Ent')
    ->selectRaw('*, Sect_Ent')
    ->get();
    $req02 = [];

    $maxId1 = DB::table('dentreprise')->max('ID_Ent');
    $secteurs = DB::table('dentreprise')->selectRaw('DISTINCT(Sect_Ent)')->get();


    foreach ($secteurs as $secteur) {
        for ($i = 1; $i < 19; $i++) {
            $S = $secteur->Sect_Ent;
            $key = $i . "-" . $S;
            for ($j = 1; $j <= intval($maxId1); $j++) { // Changement : inclusif (<=) au lieu de strictement inférieur (<)
                $resultat = $request_sec
                    ->where('Sect_Ent', $S)
                    ->where('ID_Ent_A', $i)
                    ->where('ID_Ent_B', $j)
                    ->first(); // Changement : utilisez "first()" pour obtenir un seul résultat

                if ($resultat) {
                    if (isset($req02[$key])) {
                        $req02[$key]->Montant_Factures += $resultat->Montant_Factures;
                        $req02[$key]->Nbr_Factures += $resultat->Nbr_Factures;
                        $req02[$key]->Nbr_Creances += $resultat->Nbr_Creances;
                        $req02[$key]->Montant_Creances += $resultat->Montant_Creances;
                        $req02[$key]->Montant_Dettes += $resultat->Montant_Dettes;
                        $req02[$key]->Nbr_Dettes += $resultat->Nbr_Dettes;
                        $req02[$key]->Creances_vs_Dettes += $resultat->Creances_vs_Dettes;
                    } else {
                        $req02[$key] = (object) [
                            'ID_Ent_A' => $i,
                            'Sect_Ent' => $S,
                            'Montant_Factures' => $resultat->Montant_Factures,
                            'Montant_Creances' => 0,
                            'Nbr_Factures' => $resultat->Nbr_Factures,
                            'Nbr_Creances' => 0,
                            'Montant_Dettes' => $resultat->Montant_Dettes,
                            'Nbr_Dettes' => $resultat->Nbr_Dettes,
                            'Creances_vs_Dettes' => $resultat->Creances_vs_Dettes
                        ];
                    }
                } else {
                    if (!isset($req02[$key])) {
                        $req02[$key] = (object) [
                            'ID_Ent_A' => $i,
                            'Sect_Ent' => $S,
                            'Montant_Factures' => 0,
                            'Montant_Creances' => 0,
                            'Nbr_Factures' => 0,
                            'Nbr_Creances' => 0,
                            'Montant_Dettes' => 0,
                            'Nbr_Dettes' => 0,
                            'Creances_vs_Dettes' => 0
                        ];
                    }
                }
            }
        }
    }


    $finalResults3 = array_values($req02);
    $secteur_list = DB::table('dentreprise')->select('Sect_Ent')->distinct()->get();

    if (!empty($date) or !empty($filiale)) {
        if (!empty($date)) {
            $request_sec = DB::table('fcreances_dettes')
            ->join('dentreprise', 'fcreances_dettes.ID_Ent_B', '=', 'dentreprise.ID_Ent')
            ->selectRaw('*, Sect_Ent')
            ->where('fcreances_dettes.ID_Temps', $date)
            ->get();
        }

        if (!empty($filiale)) {
            $req02 = [];
            foreach ($secteurs as $secteur) {
                    $S = $secteur->Sect_Ent;
                        $i = $filiale;
                        $key = $i . "-" . $S;
                        for ($j = 1; $j <= intval($maxId1); $j++) { // Changement : inclusif (<=) au lieu de strictement inférieur (<)
                            $resultat = $request_sec
                                ->where('Sect_Ent', $S)
                                ->where('ID_Ent_A', $i)
                                ->where('ID_Ent_B', $j)
                                ->first(); // Changement : utilisez "first()" pour obtenir un seul résultat
            
                            if ($resultat) {
                                if (isset($req02[$key])) {
                                    $req02[$key]->Montant_Factures += $resultat->Montant_Factures;
                                    $req02[$key]->Nbr_Factures += $resultat->Nbr_Factures;
                                    $req02[$key]->Nbr_Creances += $resultat->Nbr_Creances;
                                    $req02[$key]->Montant_Creances += $resultat->Montant_Creances;
                                    $req02[$key]->Montant_Dettes += $resultat->Montant_Dettes;
                                    $req02[$key]->Nbr_Dettes += $resultat->Nbr_Dettes;
                                    $req02[$key]->Creances_vs_Dettes += $resultat->Creances_vs_Dettes;
                                } else {
                                    $req02[$key] = (object) [
                                        'ID_Ent_A' => $i,
                                        'Sect_Ent' => $S,
                                        'Montant_Factures' => $resultat->Montant_Factures,
                                        'Montant_Creances' => 0,
                                        'Nbr_Factures' => $resultat->Nbr_Factures,
                                        'Nbr_Creances' => 0,
                                        'Montant_Dettes' => $resultat->Montant_Dettes,
                                        'Nbr_Dettes' => $resultat->Nbr_Dettes,
                                        'Creances_vs_Dettes' => $resultat->Creances_vs_Dettes
                                    ];
                                }
                            } else {
                                if (!isset($req02[$key])) {
                                    $req02[$key] = (object) [
                                        'ID_Ent_A' => $i,
                                        'Sect_Ent' => $S,
                                        'Montant_Factures' => 0,
                                        'Montant_Creances' => 0,
                                        'Nbr_Factures' => 0,
                                        'Nbr_Creances' => 0,
                                        'Montant_Dettes' => 0,
                                        'Nbr_Dettes' => 0,
                                        'Creances_vs_Dettes' => 0
                                    ];
                                }
                            }
                        }                        
                    
                
            }


            $finalResults3 = array_values($req02);
        }
    } 


    $groupe_list = DB::table('dentreprise')->select('Grp_Ent')->distinct()->get();
    return $finalResults3;
    // $resultat = $resultats->where('Grp_Ent', 'Gitrama')
    // ->where('ID_Ent_A', 1)->where('ID_Ent_B', 2);
    // return response($resultat[0]->Nbr_Factures);

});




Route::get('fformation_dash', function () {

    $result1 = DB::table('fformation')->sum('Montant');
    $result2 = DB::table('fformation')->count('Nombre_Eff');

    $result3 = DB::table('fformation')
    ->join('ddomaine', 'fformation.ID_Domaine', '=', 'ddomaine.ID_Domaine')
    ->select(DB::raw('COUNT(fformation.Nombre_Eff) as nb_effectif, SUM(fformation.Montant) as montant, ddomaine.Domaine as Domaine'))
    ->groupBy('ddomaine.Domaine')
    ->get();


    return response(['type_formation' => $result3, 'Montant' => $result2,
    'NB_personne' => $result1]);
});

















Route::get('femploye_dash', function () {
    
    $result = DB::table('femploye')
    ->join('dscociopro', 'femploye.ID_scociopro', '=', 'dscociopro.ID_scociopro')
    ->join('dsexe', 'femploye.ID_Sexe', '=', 'dsexe.ID_Sexe')
    ->join('dtemps_travail', 'femploye.ID_Temps_Trav', '=', 'dtemps_travail.ID_Temps_Trav')
    ->join('dcontrat', 'femploye.ID_Contrat', '=', 'dcontrat.ID_Contrat')
    ->join('dage', 'femploye.ID_Age', '=', 'dage.ID_Age')
    ->select('femploye.Nombre_Eff as nb_effectifs', 'dscociopro.Scocipro as statue',
        'dcontrat.Type_Contrat as contact', 'dtemps_travail.Temps_Travail as temps',
        'dsexe.Sexe as sexe', 'dage.Tranche_Age as age')
    ->distinct()
    ->get();

    $sexes = ["Femme", "Homme"];
    $temps = DB::table('dtemps_travail')->select('Temps_Travail')->distinct()->get();
    $status = DB::table('dscociopro')->select('Scocipro')->distinct()->get();
    $ages = DB::table('dage')->select('Tranche_Age')->distinct()->get();
    $contracts = ["CDI", "CDD"];
    $req = [];

    foreach ($status as $statu) {
        foreach ($sexes as $sexe) {
            foreach ($ages as $age) {
                foreach ($temps as $temp) {
                    foreach ($contracts as $contract) {
                        $key = $statu->Scocipro . '-' . $sexe . '-' . $age->Tranche_Age . '-' . $temp->Temps_Travail . '-' . $contract;
                        $finalResult = $result->where('sexe', $sexe)
                            ->where('statue', $statu->Scocipro)
                            ->where('contact', $contract)
                            ->where('age', $age->Tranche_Age)
                            ->where('temps', $temp->Temps_Travail)
                            ->first();

                        if ($finalResult) {
                            $req[$key] = $finalResult;
                        } else {
                            $req[$key] = (object) [
                                "nb_effectifs" => 0,
                                "statue" => $statu->Scocipro,
                                "contact" => $contract,
                                "temps" => $temp->Temps_Travail,
                                "sexe" => $sexe,
                                "age" => $age->Tranche_Age
                            ];
                        }
                    }
                }
            }
        }
    }

    $finalResults = array_values($req);

    $filteredResults = collect($finalResults)->where('statue', 'Cadre')->all();


    return array_values($filteredResults);


        // return $result->where('sexe', 'Homme');
    // {"nb_effectifs":1,"statue":"Cadre","contact":"CDI","temps":"Temps plein","sexe":"Femme","age":"21-25 ans"}
});

