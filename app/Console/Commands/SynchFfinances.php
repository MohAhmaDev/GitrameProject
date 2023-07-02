<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class SynchFfinances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'synch:ffinances';

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
    
        if ($TransformRequest->isEmpty()) {
            Log::info("Non update have Done in ffinance");
        } else {
            $query = DB::table('ffinance')->insert($TransformRequest->toArray());
            if ($query) {
                DB::table('controller_stamp')
                ->where('table_stamp', '=', 'ffinance')
                ->update(['last_timp_stamp' => $lastTimestamp]);
                Log::info("Update done in ffinance!");
            }   
            else {
                Log::info("something doesn't work fine!!");
            }
        }
    
    
    
    
    }
}
