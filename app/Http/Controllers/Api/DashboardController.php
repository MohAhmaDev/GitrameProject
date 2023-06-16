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
        $year = $request->date;
        $filiale = $request->filiale;

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
    
            $maitrise_consomation["Montant_Realisation"] = "-";
            $maitrise_consomation["Montant_Privision"] = "-";
            $maitrise_consomation["Ecart_Valeur"] = "-";
            $maitrise_consomation["Agregat_calculer"] = 'maitrise de consomation';
            $maitrise_consomation["taux"] = "-";
    
            $taux_va["Montant_Realisation"] = "-";
            $taux_va["Montant_Privision"] = "-";
            $taux_va["Ecart_Valeur"] = "-";
            $taux_va["Agregat_calculer"] = 'taux de va';
            $taux_va["taux"] = "-";
    
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
    
            $taux = round(abs(floatval($mp1 / $mp2)) * 100, 2);
            $va["Montant_Realisation"] = $mp1;
            $va["Montant_Privision"] = $mp2;
            $va["Ecart_Valeur"]= $Ecart_Valeur1;
            $va["Agregat_calculer"] = "Valeur Ajoute";
            $va["taux"] = round(abs(floatval($mp1 / $mp2)) * 100, 2);
    
            $maitrise_consomation['Montant_Realisation'] = round(($va['Montant_Realisation'] / $pp1), 2);
            $maitrise_consomation['Montant_Privision'] = round(($va['Montant_Privision'] / $pp2), 2);
            $maitrise_consomation['Ecart_Valeur'] = round(($va['Ecart_Valeur'] / $Ecart_Valeur), 2);
            $maitrise_consomation['Agregat_calculer'] = "maitrise de consomation";
            $maitrise_consomation['taux'] = round(($va['taux'] / $taux), 2); 
            
            if (!$taux_va) {
                $ca1 = $Chiffre_affaire->Montant_Realisation;
                $ca2 = $Chiffre_affaire->Montant_Privision;
                $ca3 = $Chiffre_affaire->Ecart_Valeur;
                $ca4 = $Chiffre_affaire->taux;
        
                $taux_va['Montant_Realisation'] = round(($va["Montant_Realisation"]/$ca1), 2);
                $taux_va['Montant_Privision'] = round(($va["Montant_Privision"]/$ca2), 2);
                $taux_va['Ecart_Valeur'] = round(($va["Ecart_Valeur"]/$ca3), 2);
                $taux_va['Agregat_calculer'] = "taux de va";
                $taux_va['taux'] = round(($va["taux"]/$ca4), 2); 
            }
    
        } else {
            $va["Montant_Realisation"] = "-";
            $va["Montant_Privision"] = "-";
            $va["Ecart_Valeur"] = "-";
            $va["Agregat_calculer"] = "Valeur Ajoute";
            $va["taux"] = "-";
    
            $maitrise_consomation['Montant_Realisation'] = '-';
            $maitrise_consomation['Montant_Privision'] = '-';
            $maitrise_consomation['Ecart_Valeur'] = '-';
            $maitrise_consomation['Agregat_calculer'] = "maitrise de consomation";
            $maitrise_consomation['taux'] = '-';   
    
            $taux_va['Montant_Realisation'] = '-';
            $taux_va['Montant_Privision'] = '-';
            $taux_va['Ecart_Valeur'] = '-';
            $taux_va['Agregat_calculer'] = "taux de va";
            $taux_va['taux'] = '-';  
    
            $va_salarier['Montant_Realisation'] = "-";
            $va_salarier['Montant_Privision'] = "-";
            $va_salarier['Ecart_Valeur'] = "-";
            $va_salarier['Agregat_calculer'] = "va des salriers";
            $va_salarier['taux'] = "-"; 
    
            $va_etats['Montant_Realisation'] = "-";
            $va_etats['Montant_Privision'] = "-";
            $va_etats['Ecart_Valeur'] = "-";
            $va_etats['Agregat_calculer'] = "va des salriers";
            $va_etats['taux'] = "-";  
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
            $taux = round(floatval($pp1 / $pp2) * 100, 2);
    
            $EBE->Montant_Realisation = $pp1;
            $EBE->Montant_Privision = $pp2;
            $EBE->Ecart_Valeur = $Ecart_Valeur;
            $EBE->Agregat_calculer = "EBE";
            $EBE->taux = round(floatval($pp1 / $pp2) * 100, 2);
            
            $taux_marge['Montant_Realisation'] = round(($pp1 / $va['Montant_Realisation']), 2);
            $taux_marge['Montant_Privision'] = round(($pp2 / $va['Montant_Privision']), 2);
            $taux_marge['Ecart_Valeur'] = round(($Ecart_Valeur / $va['Ecart_Valeur']), 2);
            $taux_marge['Agregat_calculer'] = "taux de marge";
            $taux_marge['taux'] = round(($taux / $va['taux'] ), 2);    
    
            if (!$marge_operationnelle) {
                $ca1 = $Chiffre_affaire->Montant_Realisation;
                $ca2 = $Chiffre_affaire->Montant_Privision;
                $ca3 = $Chiffre_affaire->Ecart_Valeur;
                $ca4 = $Chiffre_affaire->taux;  
    
                $marge_operationnelle['Montant_Realisation'] = round(($pp1/$ca1), 2);
                $marge_operationnelle['Montant_Privision'] = round(($pp2/$ca2), 2);
                $marge_operationnelle['Ecart_Valeur'] = round(($Ecart_Valeur/$ca3), 2);
                $marge_operationnelle['Agregat_calculer'] = "marge operationnelle";
                $marge_operationnelle['taux'] = round(($taux/$ca4), 2); 
            }
        } 
        else {
            $EBE["Montant_Realisation"] = $va["Montant_Realisation"] ;
            $EBE["Montant_Privision"] = $va["Montant_Privision"] ;
            $EBE["Ecart_Valeur"] = $va["Ecart_Valeur"];
            $EBE["Agregat_calculer"] = "EBE";
            $EBE["taux"] = $va["taux"];  
    
            if (!$marge_operationnelle) {
                $ca1 = $Chiffre_affaire->Montant_Realisation;
                $ca2 = $Chiffre_affaire->Montant_Privision;
                $ca3 = $Chiffre_affaire->Ecart_Valeur;
                $ca4 = $Chiffre_affaire->taux;
        
                $marge_operationnelle['Montant_Realisation'] = round(($va["Montant_Realisation"]/$ca1), 2);
                $marge_operationnelle['Montant_Privision'] = round(($va["Montant_Privision"]/$ca2), 2);
                $marge_operationnelle['Ecart_Valeur'] = round(($va["Ecart_Valeur"]/$ca3), 2);
                $marge_operationnelle['Agregat_calculer'] = "marge operationnelle";
                $marge_operationnelle['taux'] = round(($va["taux"]/$ca4), 2); 
            }
            
            $taux_marge['Montant_Realisation'] = 1;
            $taux_marge['Montant_Privision'] = 1;
            $taux_marge['Ecart_Valeur'] = 1;
            $taux_marge['Agregat_calculer'] = "taux de marge";
            $taux_marge['taux'] = 1;
        }
    
        if ($charge && $va && !$va_salarier) {
            $c1 = $charge->Montant_Realisation;
            $c2 = $charge->Montant_Privision;
            $c3 = $charge->Ecart_Valeur;
            $c4 = $charge->taux;
    
            $va_salarier['Montant_Realisation'] = round(($c1/$va["Montant_Realisation"]), 2);
            $va_salarier['Montant_Privision'] = round(($c2/$va["Montant_Privision"]/$ca2), 2);
            $va_salarier['Ecart_Valeur'] = round(($c3/$va["Ecart_Valeur"]/$ca3), 2);
            $va_salarier['Agregat_calculer'] = "va des salariers";
            $va_salarier['taux'] = round(($c4/$va["taux"]), 2); 
        } else {
            $va_salarier['Montant_Realisation'] = "-";
            $va_salarier['Montant_Privision'] = "-";
            $va_salarier['Ecart_Valeur'] = "-";
            $va_salarier['Agregat_calculer'] = "va des salriers";
            $va_salarier['taux'] = "-"; 
        }
    
        if ($impot && $va && !$va_etats) {
            $c1 = $impot->Montant_Realisation;
            $c2 = $impot->Montant_Privision;
            $c3 = $impot->Ecart_Valeur;
            $c4 = $impot->taux;
    
            $va_etats['Montant_Realisation'] = round(($c1/$va["Montant_Realisation"]), 2);
            $va_etats['Montant_Privision'] = round(($c2/$va["Montant_Privision"]/$ca2), 2);
            $va_etats['Ecart_Valeur'] = round(($c3/$va["Ecart_Valeur"]/$ca3), 2);
            $va_etats['Agregat_calculer'] = "va des salariers";
            $va_etats['taux'] = round(($c4/$va["taux"]), 2); 
        } else {
            $va_etats['Montant_Realisation'] = "-";
            $va_etats['Montant_Privision'] = "-";
            $va_etats['Ecart_Valeur'] = "-";
            $va_etats['Agregat_calculer'] = "va des salriers";
            $va_etats['taux'] = "-"; 
        }
    
    
        return response([$Production_Periode,
         $Consommation_Periode, $Chiffre_affaire, $va, $EBE, $taux_marge, $maitrise_consomation,
        $va_etats, $va_salarier, $marge_operationnelle, $taux_va]);
    

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
    

    function get_fcreance_dette(Request $request) {
        $results = DB::table('fcreances_dettes')
        ->groupBy('ID_Ent_A', 'ID_Ent_B')
        ->get();
    
        $req = [];

        $date = $request->date;
        $filiale = $request->filiale;

        if (!empty($date) or !empty($filiale)) {

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
                return response($finalResults);
            }
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
        return response($finalResults);
    }


    function get_Mois() {
        $result = DB::table('fcreances_dettes')
        ->join('dtemps', 'dtemps.ID_Temps', '=', 'fcreances_dettes.ID_Temps')
        ->select(DB::raw('DISTINCT(DATE_FORMAT(dtemps.DATE, "%Y-%m")) as date_mois, fcreances_dettes.ID_Temps as id'))
        ->get();
    

        return response($result);
    }


}
