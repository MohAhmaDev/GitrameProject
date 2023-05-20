<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SynchFemployes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'synch:femployes';

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
        if ($TransformRequest->isEmpty()) {
            Log::info("Non update have Done");
        } else {
            $query = DB::table('femployee')->insert($TransformRequest->toArray());
            if ($query) {
                DB::table('controller_stamp')
                ->where('table_stamp', '=', 'employes')
                ->update(['last_timp_stamp' => $lastTimestamp]);
                Log::info("Update done !");
            }   
            else {
                Log::info("something doesn't work fine!");
            }
        }

    }
}
