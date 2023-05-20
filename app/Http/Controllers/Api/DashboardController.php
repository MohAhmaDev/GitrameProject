<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
        
    public function get_Employes_dash() 
    {
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

        $ContractEmployes = DB::table('femployee')
            ->join('dcontrat', 'femployee.ID_Contrat', '=', 'dcontrat.ID_Contrat')
            ->select(DB::raw('SUM(Nombre_Eff) as nb_employes'), 'dcontrat.Type_Contrat')
            ->groupBy('femployee.ID_Contrat', 'dcontrat.Type_Contrat')
            ->get();

        $get_Position_group = DB::table('femployee')
            ->select(DB::raw('COUNT(Nombre_Eff) AS nb_employes'), DB::raw("CASE WHEN ID_Date_Retraite = '0000-00' THEN 'non retraiter' WHEN ID_Date_Retraite != '0000-00' THEN 'retraiter' END AS positionEmploye"))
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
        ->join('dtemps', 'ffinance.ID_Date_Agregats', '=', 'dtemps.Mois')
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
    



}
