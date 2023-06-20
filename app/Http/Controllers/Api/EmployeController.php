<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employe;
use App\Http\Requests\StoreEmployeRequest;
use App\Http\Requests\UpdateEmployeRequest;
use App\Models\Filiale;
use App\Http\Resources\EmployeResource;
use Illuminate\Support\Facades\Auth;

class EmployeController extends Controller
{

    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     * 
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $role = auth()->user()->roles->first()->name;
        $branch = auth()->user()->filiales->first();


        $this->authorize('viewAny', Employe::class);

        if ($role === "global") {
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
        $branch = auth()->user()->filiales->first();


        $auth = ["global", "editor"];
        if (!in_array($role, $auth)) {
            abort(403, 'Unauthorized');
        }
        $this->authorize('create', Employe::class);


    
        $data = $request->validated();
        $data['handicape'] = !empty($data['handicape']) ? $data['handicape'] : false;
        if (($data['position'] === "retraiter") and ($data['date_retraite'] === null)) 
        {
            return response([
                'message' => "le champ date retaiter est vide"
            ], 422);                
        }

        if ($data['position'] === 'non retraiter') {
            $data['date_retraite'] = null;
        }

        if ($role !== "global") {
            if (!is_null($branch->id)) {
                $data['filiale_id'] = !empty($data['filiale_id']) ? $data['filiale_id'] : $branch->id;
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
        $branch = auth()->user()->filiales->first();

        if ($role !== "global") {
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
        $branch = auth()->user()->filiales->first();


        $auth = ["global", "editor"];
        if (!in_array($role, $auth)) {
            abort(403, 'Unauthorized');
        }

        $this->authorize('update', $employe);


        if ($role !== "global") {
            if ($employe->filiale_id !== $branch->id) {
                return response([
                    'message' => "you don't have the permission de to this action"
                ], 422);    
            }
        }

        $data = $request->validated();

        if (($data["position"] === "retraiter") and ($data['date_retraite'] === null)) 
        {
            return response([
                'message' => "le champ date retaiter est vide"
            ], 422);                
        }


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

        $auth = ["global", "editor"];
        if (!in_array($role, $auth)) {
            abort(403, 'Unauthorized');
        }

        $this->authorize('delete', $employe);


        if ($role !== "global") {
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
