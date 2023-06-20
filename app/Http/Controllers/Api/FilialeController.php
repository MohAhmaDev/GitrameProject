<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Filiale;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class FilialeController extends Controller
{
    

    public function index()
    {
        $role = auth()->user()->roles->first()->name;
        $access = ["admin", "global"];
        $branch = auth()->user()->filiales->first();

        if (in_array($role, $access)) {
            $filiales = Filiale::all();
        } else {
            $id = $branch->id;
            if (!is_null($id)) {
                $filiales = Filiale::query()->where('id', '!=', 1)->get();
            } else {
                return response(['filiale' => []]);
            }
        }


        $results = $filiales->map(function ($filiale) {
		    return [
			    'id' => $filiale->id,
			    'name' => $filiale->nom_filiale,
		    ];
	    });
        
        return response([
            'filiale' => $results
        ]);
    }

    public function store(Request $request, $id) 
    {
        // Vérifier si l'utilisateur est administrateur
        if (auth()->user()->roles->first()->name !== "admin") {
            abort(403, 'Unauthorized');
        }
        // Vérifier si l'utilisateur existe
        $user = User::findOrFail($id);
        $filiale = Filiale::findOrFail($request->filiale_id);

        $user->filiales()->attach($filiale);
        return response()->json([
            'message' => 'Filiale assigned successfully'
        ]);
    }

    public function update(Request $request, $id)
    {

        if (auth()->user()->roles->first()->name !== "admin") 
        {
            abort(403, 'Unauthorized');
        }
        // Vérifier si l'utilisateur existe
        $user = User::findOrFail($id);
        $filiale = Filiale::findOrFail($request->filiale_id);

        $user->filiales()->sync($filiale);
        return response()->json(['message' => 'Filiale update successfully']);
    }

    public function get_agregat($type_agregat)  
    {
        $agregets = DB::table('dagregats')
        ->selectRaw('agregats, ID_Agregats')
        ->where('Type_Agregats', '=', $type_agregat)
        ->get();
    
        $results = $agregets->map(function ($agreget) {
            return [
                'id' => $agreget->ID_Agregats,
                'name' => $agreget->agregats
            ];
        });
    
        return response([
            'agreget' => $results
        ]);

    }

    public function get_years() {

        $years = DB::table('finances')
        ->select(DB::raw("DISTINCT(DATE_FORMAT(finances.date_activite, '%Y')) as year"))
        ->orderBy(DB::raw("(DATE_FORMAT(finances.date_activite, '%Y'))"), 'ASC')
        ->get();
    
        return response($years);
    }

}
