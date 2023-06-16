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
            ->where('employes.updated_at','>', $timestemp)
            ->groupBy('ID_Temps', 'ID_Date_Recrutement', 'ID_Date_Retraite', 'ID_Fonction', 'ID_Sexe', 'ID_Handicap', 'ID_Ent', 'ID_Temps_Trav', 'ID_Contrat', 'ID_Age')
            ->get();
   
    
        $TransformRequest = $resultats->map(function ($result) {
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

        if ($TransformRequest->isEmpty()) {
            Log::info("Non update have Done");
        } else {
            $query = DB::table('femploye')->insert($TransformRequest->toArray());
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
