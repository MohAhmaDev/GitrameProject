<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
        
    public function get_Employes_dash() 
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
                $data = ["x" => $x, "y" => $y];
                $element = ["id" => $id, "color" => $color, "data" => [$data]];
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
    
        return response([$Production_Periode, $Consommation_Periode, $Chiffre_affaire, $va, $EBE]);
    


    }
    



}
