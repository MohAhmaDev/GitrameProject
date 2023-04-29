<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Stagiare;
use App\Http\Requests\StoreStagiareRequest;
use App\Http\Requests\UpdateStagiareRequest;
use App\Http\Resources\StagiareResource;
use App\Models\Filiale;

class StagiareController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $role = auth()->user()->roles->first()->name;
        $branch = auth()->user()->filiales->first();


        if ($role === "admin") {
            $stagiare = Stagiare::all();
        } 
        else {
            $id = $branch->id;
            if (!is_null($id)) {
                $filiale = Filiale::findOrFail($id);
                $stagiare = $filiale->stagiares;
            } else {
                return response([]);
            }
        }
        return StagiareResource::collection($stagiare);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStagiareRequest $request)
    {

        $role = auth()->user()->roles->first()->name;
        $branch = auth()->user()->filiales->first();


        $auth = ["admin", "editor"];
        if (!in_array($role, $auth)) {
            abort(403, 'Unauthorized');
        }

        $data = $request->validated();

        if ($role !== "admin") {
            if (!is_null($branch->id)) {
                $data['filiale_id'] = !empty($data['filiale_id']) ? $data['filiale_id'] : $branch->id;
            } else {
                return response([
                    'message' => "you don't have the permission de to this action"
                ], 422);                
            }
        }

        $stagiare = Stagiare::create($data);
        return new StagiareResource($stagiare);

    }

    /**
     * Display the specified resource.
     */
    public function show(Stagiare $stagiare)
    {
        $role = auth()->user()->roles->first()->name;
        $branch = auth()->user()->filiales->first();

        if ($role !== "admin") {
            if ($stagiare->filiale_id !== $branch->id) {
                return response([
                    'message' => "you don't have the permission de to this action"
                ], 422);    
            }
        }


        return new StagiareResource($stagiare);    
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStagiareRequest $request, Stagiare $stagiare)
    {
        $role = auth()->user()->roles->first()->name;
        $branch = auth()->user()->filiales->first();


        $auth = ["admin", "editor"];
        if (!in_array($role, $auth)) {
            abort(403, 'Unauthorized');
        }

        if ($role !== "admin") {
            if ($stagiare->filiale_id !== $branch->id) {
                return response([
                    'message' => "you don't have the permission de to this action"
                ], 422);    
            }
        }

        $data = $request->validated();
        $stagiare->update($data);
        return new StagiareResource($stagiare);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Stagiare $stagiare)
    {

        $role = auth()->user()->roles->first()->name;
        $branch = auth()->user()->filiales->first->id;

        $auth = ["admin", "editor"];
        if (!in_array($role, $auth)) {
            abort(403, 'Unauthorized');
        }

        if ($role !== "admin") {
            if ($stagiare->filiale_id !== $branch->id) {
                return response([
                    'message' => "you don't have the permission de to this action"
                ], 422);    
            }
        }  
        
        $stagiare->delete();
        return response()->json(['message' => 'Employee deleted successfully']);

    }
}
