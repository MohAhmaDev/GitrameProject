<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employe;
use App\Http\Requests\StoreEmployeRequest;
use App\Http\Requests\UpdateEmployeRequest;
use App\Models\Filiale;
use App\Http\Resources\EmployeResource;

class EmployeController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $role = auth()->user()->roles->first()->name;
        $branch = auth()->user()->filiales->first->id;


        if ($role === "admin") {
            $employe = Employe::all();
        } 
        else {
            $id = $branch->id;
            if (!is_null($id)) {
                $filiale = Filiale::findOrFail($id);
                $employe = $filiale->employes;
            } else {
                return response([]);
            }
        }
        return EmployeResource::collection($employe);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeRequest $request)
    {

        $role = auth()->user()->roles->first()->name;
        $branch = auth()->user()->filiales->first->id;


        $auth = ["admin", "editor"];
        if (!in_array($role, $auth)) {
            abort(403, 'Unauthorized');
        }

        $data = $request->validated();
        $data['handicape'] = !empty($data['handicape']) ? $data['handicape'] : false;

        if ($role !== "admin") {
            if (!is_null($branch->id)) {
                $data['filiale_id'] = !empty($data['filiale_id']) ? $data['filiale_id'] : $branch;
            } else {
                return response([
                    'message' => "you don't have the permission de to this action"
                ], 422);                
            }
        }



        $employe = Employe::create($data);
        return new EmployeResource($employe);
    }

    /**
     * Display the specified resource.
     */
    public function show(Employe $employe)
    {
        $role = auth()->user()->roles->first()->name;
        $branch = auth()->user()->filiales->first->id;

        if ($role !== "admin") {
            if ($employe->filiale_id !== $branch->id) {
                return response([
                    'message' => "you don't have the permission de to this action"
                ], 422);    
            }
        }


        return new EmployeResource($employe);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeRequest $request, Employe $employe)
    {
        $role = auth()->user()->roles->first()->name;
        $branch = auth()->user()->filiales->first->id;


        $auth = ["admin", "editor"];
        if (!in_array($role, $auth)) {
            abort(403, 'Unauthorized');
        }

        if ($role !== "admin") {
            if ($employe->filiale_id !== $branch->id) {
                return response([
                    'message' => "you don't have the permission de to this action"
                ], 422);    
            }
        }

        $data = $request->validated();
        $employe->update($data);
        return new EmployeResource($employe);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employe $employe)
    {
        $role = auth()->user()->roles->first()->name;
        $branch = auth()->user()->filiales->first->id;

        $auth = ["admin", "editor"];
        if (!in_array($role, $auth)) {
            abort(403, 'Unauthorized');
        }

        if ($role !== "admin") {
            if ($employe->filiale_id !== $branch->id) {
                return response([
                    'message' => "you don't have the permission de to this action"
                ], 422);    
            }
        }  
        
        $employe->delete();
        return response()->json(['message' => 'Employee deleted successfully']);
    }
}
