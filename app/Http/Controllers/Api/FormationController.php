<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Formation;
use App\Http\Requests\StoreFormationRequest;
use App\Http\Requests\UpdateFormationRequest;
use App\Http\Resources\FormationResource;
use App\Models\Employe;
use App\Models\Filiale;

class FormationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $role = auth()->user()->roles->first()->name;
        $branch = auth()->user()->filiales->first();


        if ($role === "admin") {
            $formations = Formation::all();
        } else {
            $id = $branch->id;
            if (!is_null($id)) {
                $formations = Formation::whereHas('employe', function ($query) use ($id) {
                    $query->where('filiale_id', $id);
                })->get();
            } else {
                return response([]);
            }
        }
        return FormationResource::collection($formations);    
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFormationRequest $request)
    {
        $role = auth()->user()->roles->first()->name;
        $branch = auth()->user()->filiales->first();


        $auth = ["admin", "editor"];
        if (!in_array($role, $auth)) {
            abort(403, 'Unauthorized');
        }

        $data = $request->validated();

        $formation = Formation::create($data);
        return new FormationResource($formation);    
    }

    /**
     * Display the specified resource.
     */
    public function show(Formation $formation)
    {
        $role = auth()->user()->roles->first()->name;
        $branch = auth()->user()->filiales->first();

        if ($role !== "admin") {
            $id = $branch->id;
            if ($formation->employe_id !== Employe::where('filiale_id', $id)->first()->id) {
                return response([
                    'message' => "you don't have the permission de to this action"
                ], 422);    
            }
        }

        return new FormationResource($formation);  
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFormationRequest $request, Formation $formation)
    {
        $role = auth()->user()->roles->first()->name;
        $branch = auth()->user()->filiales->first();


        $auth = ["admin", "editor"];
        if (!in_array($role, $auth)) {
            abort(403, 'Unauthorized');
        }

        if ($role !== "admin") {
            $id = $branch->id;
            if ($formation->employe_id !== Employe::where('filiale_id', $id)->first()->id) {
                return response([
                    'message' => "you don't have the permission de to this action"
                ], 422);    
            }
        }

        $data = $request->validated();
        $formation->update($data);
        return new FormationResource($formation);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Formation $formation)
    {
        $role = auth()->user()->roles->first()->name;
        $branch = auth()->user()->filiales->first->id;

        $auth = ["admin", "editor"];
        if (!in_array($role, $auth)) {
            abort(403, 'Unauthorized');
        }

        if ($role !== "admin") {
            $id = $branch->id;
            if ($formation->employe_id !== Employe::where('filiale_id', $id)->first()->id) {
                return response([
                    'message' => "you don't have the permission de to this action"
                ], 422);    
            }
        }
    
        $formation->delete();
        return response()->json(['message' => 'Formataion deleted successfully']);
    
    }
}
