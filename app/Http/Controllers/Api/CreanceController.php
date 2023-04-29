<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Creance;
use App\Http\Requests\StoreCreanceRequest;
use App\Http\Requests\UpdateCreanceRequest;
use App\Http\Resources\CreanceResource;
use App\Models\Filiale;
use App\Models\Entreprise;

class CreanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $role = auth()->user()->roles->first()->name;
        $branch = auth()->user()->filiales->first();

        if ($role === "admin") {
            $creance = Creance::all();
        } 
        else 
        {
            $id = $branch->id;
            if (!is_null($id)) {
                $filiale = Filiale::findOrFail($id);
                $creance = Creance::where([
                    ['creditor_type', Filiale::class],
                    ['creditor_id', $filiale->id]
                ])
                ->orWhere([
                    ['debtor_type', Filiale::class],
                    ['debtor_id', $filiale->id]
                ])->get();
            } else {
                return response([]);
            }
        }

        return CreanceResource::collection($creance);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCreanceRequest $request)
    {        
        $role = auth()->user()->roles->first()->name;
        $branch = auth()->user()->filiales->first();

        $auth = ["admin", "editor"];
        if (!in_array($role, $auth)) {
            abort(403, 'Unauthorized');
        }

        $data = $request->validated();
        $creditor_type = $data['creditor_type']; 
        $debtor_type = $data['debtor_type'];

        $data['creditor_type'] = $creditor_type === 'filiale' ? Filiale::class : Entreprise::class;
        $data['debtor_type'] = $debtor_type === 'entreprise' ? Entreprise::class : Filiale::class;


        $firmes_type = ["filiale", "entreprise"];
        if (!in_array($creditor_type, $firmes_type) and !in_array($debtor_type, $firmes_type)) {
            return response([
                'message' => "une valeur non valide a été saisi dans le formulaire"
            ], 422);    
        }
        if (($creditor_type !== "filiale") and ($debtor_type !== "filiale"))
        {
            return response([
                'message' => "une valeur non valide a été saisi dans le formulaire"
            ], 422);                 
        }

        if ($role !== "admin") {
            $id = $branch->id;
            if (!is_null($id)) {
                if (($data['creditor_id'] !== $id) and ($data['debtor_id'] !== $id))
                {
                    return response([
                        'message' => "une valeur non valide a été saisi dans le formulaire"
                    ], 422);   
                }
            }
        }
        $creance = Creance::create($data);
        return new CreanceResource($creance);
    }

    /**
     * Display the specified resource.
     */
    public function show(Creance $creance)
    {
        $role = auth()->user()->roles->first()->name;
        $branch = auth()->user()->filiales->first();

        if ($role !== "admin") {
            if (($creance->debtor_id !== $branch->id) and ($creance->creditor_id !== $branch->id)) {
                return response([
                    'message' => "you don't have the permission de to this action"
                ], 422);    
            }
        }

        return new CreanceResource($creance);    
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCreanceRequest $request, Creance $creance)
    {

        $role = auth()->user()->roles->first()->name;
        $branch = auth()->user()->filiales->first();

        $auth = ["admin", "editor"];
        if (!in_array($role, $auth)) {
            abort(403, 'Unauthorized');
        }
        
        if ($role !== "admin") {
            if (($creance->debtor_id !== $branch->id) and ($creance->creditor_id !== $branch->id)) {
                return response([
                    'message' => "you don't have the permission de to this action"
                ], 422);    
            }
        }

        $data = $request->validated();
        $creditor_type = $data['creditor_type']; 
        $debtor_type = $data['debtor_type'];

        $data['creditor_type'] = $creditor_type === 'filiale' ? Filiale::class : Entreprise::class;
        $data['debtor_type'] = $debtor_type === 'entreprise' ? Entreprise::class : Filiale::class;

        $firmes_type = ["filiale", "entreprise"];
        if (!in_array($creditor_type, $firmes_type) and !in_array($debtor_type, $firmes_type)) {
            return response([
                'message' => "une valeur non valide a été saisi dans le formulaire"
            ], 422);    
        }
        if (($creditor_type !== "filiale") and ($debtor_type !== "filiale"))
        {
            return response([
                'message' => "une valeur non valide a été saisi dans le formulaire"
            ], 422);                 
        }

        $creance->update($data);
        return new CreanceResource($creance);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Creance $creance)
    {
        $role = auth()->user()->roles->first()->name;
        $branch = auth()->user()->filiales->first->id;

        $auth = ["admin", "editor"];
        if (!in_array($role, $auth)) {
            abort(403, 'Unauthorized');
        }

        if ($role !== "admin") {
            if (($creance->debtor_id !== $branch->id) and ($creance->creditor_id !== $branch->id)) {
                return response([
                    'message' => "you don't have the permission de to this action"
                ], 422);    
            }
        } 
        
        $creance->delete();
        return response()->json(['message' => 'Creance deleted successfully']);    
    }
}

