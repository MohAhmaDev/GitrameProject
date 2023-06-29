<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SynchFformations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'synch:fformations';

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

        if ($TransformRequest->isEmpty()) {
            Log::info("Non update have Done in fformation");
        } else {
            $query = DB::table('fformation')->insert($TransformRequest->toArray());
            if ($query) {
                DB::table('controller_stamp')
                ->where('table_stamp', '=', 'fformation')
                ->update(['last_timp_stamp' => $lastTimestamp]);
                Log::info("Update done in fformation!");
            }   
            else {
                Log::info("something doesn't work fine!!");
            }
        }


    }
}
