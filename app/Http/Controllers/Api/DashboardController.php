<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
        
    public function get_Employes_dash(Request $request) 
    {
        $trancheAges = DB::table('femploye')
            ->join('dage', 'femploye.ID_Age', '=', 'dage.ID_Age')
            ->select(DB::raw('SUM(Nombre_Eff) as nb_employes'), 'dage.Tranche_Age')
            ->groupBy('femploye.ID_Age', 'dage.Tranche_Age')
            ->get();

        $SexeEmployes = DB::table('femploye')
            ->join('dsexe', 'femploye.ID_Sexe', '=', 'dsexe.ID_Sexe')
            ->select(DB::raw('SUM(Nombre_Eff) as nb_employes'), 'dsexe.Sexe')
            ->groupBy('femploye.ID_Sexe', 'dsexe.Sexe')
            ->get();

        $ContractEmployes = DB::table('femploye')
            ->join('dcontrat', 'femploye.ID_Contrat', '=', 'dcontrat.ID_Contrat')
            ->select(DB::raw('SUM(Nombre_Eff) as nb_employes'), 'dcontrat.Type_Contrat')
            ->groupBy('femploye.ID_Contrat', 'dcontrat.Type_Contrat')
            ->get();

        $get_Position_group = DB::table('femploye')
            ->select(DB::raw('COUNT(Nombre_Eff) AS nb_employes'), DB::raw("CASE WHEN ID_Date_Retraite = 2413 THEN 'non retraiter' WHEN ID_Date_Retraite != 2413 THEN 'retraiter' END AS positionEmploye"))
            ->groupBy('positionEmploye')
            ->get();

        $results_employes_age = $trancheAges->map(function ($trancheAge) {
            $labal = $trancheAge->Tranche_Age;
            $value = $trancheAge->nb_employes;
            return [
                $labal => $value
            ];
        });
        $resultat_employes_contract = $ContractEmployes->map(function ($ContractEmploye) {
            $labal = $ContractEmploye->Type_Contrat;
            $value = $ContractEmploye->nb_employes;
            
            return [
                "id" => $labal,
                "label" => $labal,
                "value" => intval($value),
            ];
        });



        return response([
            'dash01' => $SexeEmployes,
            'dash02' => $results_employes_age,
            'dash03' => $resultat_employes_contract,
            'dash04' => $get_Position_group
        ]); 
    }


    public function get_FFinances() 
    {
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
                        $data = ["x" => $year, "y" => 100];
    
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
        return $resultJson;
    }

    public function get_ca() {
        $ca = DB::table('ffinance')->sum('Montant_Realisation');
        return $ca;
    }

    public function get_dashboard_finance(Request $request) 
    {
        $role = auth()->user()->roles->first()->name;
        $branch = auth()->user()->filiales->first();

        $year = $request->date;
        if ($role === "global") {
            $filiale = $request->filiale;
        } else {
            $filiale = $branch->id;
        }

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
    
        $NBEmployes = DB::table('femploye')
        ->select(DB::raw('SUM(Nombre_Eff) as nb_employes'));
    
        $req04 = $NBEmployes
        ->get();
    
        if (!empty($year) && !empty($filiale)) {
            $resultats = $test
            ->where('dtemps.annee', $year)
            ->where('ffinance.ID_Ent', $filiale)
            ->groupBy('Agregat_calculer')
            ->get();
    
            $req04 = $NBEmployes
            ->where('dtemps.annee', $year)
            ->where('femploye.ID_Ent', $filiale)
            ->get();
        } else {
            if (!empty($year)) {
                $resultats = $test
                ->where('dtemps.annee', $year)
                ->groupBy('Agregat_calculer')
                ->get();  
                
                $req04 = $NBEmployes
                ->where('dtemps.annee', $year)
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
            
        } 
        else {
            $EBE["Montant_Realisation"] = $va["Montant_Realisation"] ;
            $EBE["Montant_Privision"] = $va["Montant_Privision"] ;
            $EBE["Ecart_Valeur"] = $va["Ecart_Valeur"];
            $EBE["Agregat_calculer"] = "EBE";
            $EBE["taux"] = $va["taux"];  
        }
    
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
            $taux_marge = ['label' => 'taux de marge', 'val' => $val];
        } else {
            $taux_marge = ['label' => 'taux de marge', 'val' => "-"];
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
            $rendement_employe = ['label' => "rendement par employée", 'val' => $val];
        } else {
            $rendement_employe = ['label' => "rendement par employé", 'val' => "-"];
        }
        if (($req04[0]->nb_employes) && !is_array($Production_Periode)) 
        {
            $val1 = floatval($Production_Periode->Montant_Realisation);
            $val2 = floatval($req04[0]->nb_employes);
            $val = round($val1 / $val2, 2);
            $effictif_prod = ['label' => "poids des effectifs de production", 'val' => $val];
        } else {
            $effictif_prod = ['label' => "poids des effectifs de production", 'val' => "-"];
        }

        if (($req04[0]->nb_employes) && is_numeric($va['Montant_Realisation']))
        {
            $val1 = floatval($va['Montant_Realisation']);
            $val2 = floatval($req04[0]->nb_employes);
            $val = round($val1 / $val2, 2);
            $taux_personne = ['label' => "taux d'efficience du personnel", 'val' => $val];
        } else {
            $taux_personne = ['label' => "taux d'efficience du personnel", 'val' => "-"];
        } 
    

        
        $kpi_finance = [$maitrise_consomation, $taux_marge, $taux_va, $marge_operationnelle,
         $va_salarier, $va_etats, $taux_personne, $effictif_prod, $rendement_employe];
    
        $agregat_finance = [$Production_Periode,
         $Consommation_Periode, $Chiffre_affaire, $va, $EBE];
    
        return (['tab1' => $agregat_finance,'tab2' => $kpi_finance]);
    
    }

    public function get_dashboard_rhs(Request $request) {

        $year = $request->date;
        $filiale = $request->filiale;

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
        ->select(DB::raw('SUM(Nombre_Eff) as nb_employes'));

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
            ->where('dtemps.annee', $year)
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
                ->where('dtemps.annee', $year)
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
    }
    

    public function get_fcreance_dette(Request $request) {
        $results = DB::table('fcreances_dettes')
        ->groupBy('ID_Ent_A', 'ID_Ent_B')
        ->get();
    
        $role = auth()->user()->roles->first()->name;
        $branch = auth()->user()->filiales->first();
        $req = [];

        $date = $request->date;
        if ($role === "global") {
            $filiale = $request->filiale;
        } else {
            $filiale = $branch->id;
        }



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

        // *********************************************** Groupe *********************************************************        
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


        return response(["data2" => $finalResults2, "data1" => $finalResults,
        'groupe' => $groupe_list, 'secteur' => $secteur_list, "data3" => $finalResults3]);
    }


    
    public function get_Mois() {
        $result = DB::table('fcreances_dettes')
        ->join('dtemps', 'dtemps.ID_Temps', '=', 'fcreances_dettes.ID_Temps')
        ->select(DB::raw('DISTINCT(DATE_FORMAT(dtemps.DATE, "%Y-%m")) as date_mois, fcreances_dettes.ID_Temps as id'))
        ->get();
    

        return response($result);
    }



    public function get_fformation(Request $request) {

        $result1 = DB::table('fformation')->sum('Montant');
        $result2 = DB::table('fformation')->count('Nombre_Eff');
    
        $result3 = DB::table('fformation')
        ->join('ddomaine', 'fformation.ID_Domaine', '=', 'ddomaine.ID_Domaine')
        ->select(DB::raw('COUNT(fformation.Nombre_Eff) as nb_effectif, SUM(fformation.Montant) as montant, ddomaine.Domaine as Domaine'))
        ->groupBy('ddomaine.Domaine')
        ->get();
    
    
        return response(['type_formation' => $result3, 'Montant' => $result2,
        'NB_personne' => $result1]);
    }


    public function get_rhs_stats() {


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


        return [
            'tab' => array_values($filteredResults),
            'tranchAge' => $ages
        ];
    }


}


