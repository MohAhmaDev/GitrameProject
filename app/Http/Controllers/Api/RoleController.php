<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    

    public function index() 
    {
        $role = Role::where('name', '!=', 'admin')->get();
        return response([
            'role' => $role->setVisible(['id', 'name'])
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
        $validated = $request->validate([
            'role_name' => [
                'required',
                Rule::in(['basic', 'editor'])
            ]
        ]);
        $role_name = $validated;
        $role = Role::where('name', $role_name)->firstOrFail();
        
        $user->roles()->attach($role);
        return response()->json([
            'message' => 'Filiale assigned successfully'
        ]);
    }


    public function update(Request $request, $id)
    {
        if (auth()->user()->roles->first()->name !== "admin") {
            abort(403, 'Unauthorized');
        }
    
        $user = User::findOrFail($id);
        $validated = $request->validate([
            'role_name' => [
                'required',
                Rule::in(['basic', 'editor'])
            ]
        ]);
        $role_name = $validated;
        $role = Role::where('name', $role_name)->firstOrFail();
        $user->roles()->sync($role);
        return response()->json(['message' => 'Role assigned successfully']);
    }


}
