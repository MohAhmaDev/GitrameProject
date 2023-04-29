<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Finance;
use Illuminate\Http\Request;

class StartController extends Controller
{
    
    public function index(Request $request) 
    {
        $role = $request->user()->roles()->first();
        $roleName = $role ? $role->name : null;

        $filiale = $request->user()->filiales()->first();
        $filiale_id = $filiale ? $filiale->id : null;
        $filiale_name = $filiale ? $filiale->nom_filiale : "";

        return response([
            'user' => $request->user(),
            'role' => $roleName,
            'filiale' => ["id" => $filiale_id, "name" => $filiale_name]
        ]);
    }


    public function finances() 
    {
        $finances = Finance::all();

        $results = $finances->map(function ($finance) {
		    return [
                "id" => $finance->id,
                "type_activite" => $finance->type_activite,
                "activite" => $finance->activite,
                "date_activite" => $finance->date_activite,
                "privision" => $finance->privision,
                "realisation" => $finance->realisation,
                "compte_scf" => $finance->compte_scf,
                "filiale_id" => $finance->filiale_id,    
		    ];
	    });

        return $results;
    }
}
