<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StartController extends Controller
{
    
    public function index(Request $request) 
    {
        $role = $request->user()->roles()->first();
        $roleName = $role ? $role->name : null;

        $filiale = $request->user()->filiales()->first();
        $filiale_id = $filiale ? $filiale->id : 1;
        $filiale_name = $filiale ? $filiale->nom_filiale : "";

        return response([
            'user' => $request->user(),
            'role' => $roleName,
            'filiale' => ["id" => $filiale_id, "name" => $filiale_name]
        ]);
    }
}
