<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Filiale;
use App\Models\User;

class FilialeController extends Controller
{
    

    public function index()
    {
        $filiale = Filiale::all();
        return response([
            'filiale' => $filiale->setVisible(['id', 'nom_filiale'])
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

}
