<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SynchFcreances_dettes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'synch:fcreances_dettes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $timestemp = DB::table('controller_stamp')
        ->where('table_stamp', 'dettes_creances')
        ->max('last_timp_stamp');

        $date = DB::selectOne('SELECT DATE_FORMAT(CURRENT_DATE(), "%Y-%m") AS formatted_date');
        $formattedDate = $date->formatted_date;
    
        if ($formattedDate !== $timestemp) {
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
        } else {
            $results = [];
        }
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
        $aggregatedResultsArray = json_decode(json_encode($aggregatedResults), true);

        if (count($aggregatedResultsArray) === 0) {
            Log::info("Non update have Done in ffinance");
        } else {
            $query = DB::table('fcreances_dettes')->insert($aggregatedResultsArray);
            if ($query) {
                DB::table('controller_stamp')
                ->where('table_stamp', '=', 'dettes_creances')
                ->update(['last_timp_stamp' => $formattedDate]);      
                Log::info("Update done in fcreances_dettes!");                  
            } else { 
                Log::info("something doesn't work fine!!!");
            }
        }
    }
}
