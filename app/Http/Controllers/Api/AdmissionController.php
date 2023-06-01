<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\User;
use Illuminate\Http\Request;

class AdmissionController extends Controller
{
    public function index()
    {
        $admissions = Admission::all();

        $results = $admissions->map(function ($admission) {
		    return [
			    'id' => $admission->id,
			    'table' => $admission->table,
		    ];
	    });
        
        return response([
            'admission' => $results
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
        $admission = Admission::findOrFail($request->id);

        $user->admissions()->attach($admission);
        return response()->json([
            'message' => 'Admission assigned successfully'
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
        $admission = Admission::findOrFail($request->id);

        $user->admissions()->sync($admission);
        return response()->json(['message' => 'Admission update successfully']);

    }
}
