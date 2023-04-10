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

        return response([
            'user' => $request->user(),
            'role' => $roleName,
        ]);
    }
}
