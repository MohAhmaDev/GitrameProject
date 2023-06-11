<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Entreprise;
use Illuminate\Http\Request;

class EntrepriseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $entreprises = Entreprise::query()->where('groupe', '!=', 'gitrame')->get();

        $results = $entreprises->map(function ($entreprise) {
		    return [
			    'id' => $entreprise->id,
			    'name' => $entreprise->nom_entreprise,
		    ];
	    });
        
        return response([
            'entreprise' => $results
        ]);


    }

    public function store(Request $request) 
    {
        $role = auth()->user()->roles->first()->name;

        if ($role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $data = $request->validate([
            'nom_entreprise'=> 'required|string',
            'groupe'=> 'required|string',
            'adresse'=> 'required|string',
            'secteur'=> 'required|string',
            'nationalite'=> 'required|string',
            'num_tel_entr'=> 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'adress_emil_entr'=> ['required', 'regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix'],
            'status_juridique'=> 'required|string',
        ]);
    
        $entreprise = Entreprise::create($data);
        return response()->json([
            'message' => 'Entreprise assigned successfully'
        ]);
    }

}
