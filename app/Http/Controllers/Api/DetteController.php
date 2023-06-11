<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dette;
use App\Http\Requests\StoreDetteRequest;
use App\Http\Requests\UpdateDetteRequest;
use App\Http\Resources\DetteResource;
use App\Models\Entreprise;
use App\Models\Filiale;

class DetteController extends Controller
{

    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $role = auth()->user()->roles->first()->name;
        $branch = auth()->user()->filiales->first();

        $this->authorize('viewAny', Dette::class);

        if ($role === "admin") {
            $dette = Dette::all();
        } 
        else 
        {
            $id = $branch->id;
            if (!is_null($id)) {
                $filiale = Filiale::findOrFail($id);
                $dette = Dette::where([
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

        return DetteResource::collection($dette);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDetteRequest $request)
    {        
        $role = auth()->user()->roles->first()->name;
        $branch = auth()->user()->filiales->first();

        $auth = ["admin", "editor"];
        if (!in_array($role, $auth)) {
            abort(403, 'Unauthorized');
        }
        // $this->authorize('create', Dette::class);

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
        $dette = Dette::create($data);
        return new DetteResource($dette);
    }

    /**
     * Display the specified resource.
     */
    public function show(Dette $dette)
    {
        $role = auth()->user()->roles->first()->name;
        $branch = auth()->user()->filiales->first();

        $this->authorize('view', $dette);
        
        if ($role !== "admin") {
            if (($dette->debtor_id !== $branch->id) and ($dette->creditor_id !== $branch->id)) {
                return response([
                    'message' => "you don't have the permission de to this action"
                ], 422);    
            }
        }


        return new DetteResource($dette);    
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDetteRequest $request, Dette $dette)
    {

        $role = auth()->user()->roles->first()->name;
        $branch = auth()->user()->filiales->first();

        // $this->authorize('update', $dette);

        $auth = ["admin", "editor"];
        if (!in_array($role, $auth)) {
            abort(403, 'Unauthorized');
        }


        
        if ($role !== "admin") {
            if (($dette->debtor_id !== $branch->id) and ($dette->creditor_id !== $branch->id)) {
                return response([
                    'message' => "you don't have the permission de to this action"
                ], 422);    
            }
        }

        $data = $request->validated();
        $creditor_type = $data['creditor_type']; 
        $debtor_type = $data['debtor_type'];
        $montant_encaissement = $data['montant_encaissement'];
        $montant = $data['montant'];

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

        if ($montant < $montant_encaissement) {
            return response([
                'message' => "le montant d'encaissement doit etre inférieur aux montant de la détte"
            ], 422);           
        } else {
            if ($montant == $montant_encaissement) {
                $data['regler'] = TRUE;
            }
            if ($data['regler']) {
                $data['montant_encaissement'] = $montant;    
            }
        }   

        $dette->update($data);
        return new DetteResource($dette);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Dette $dette)
    {

        $role = auth()->user()->roles->first()->name;
        $branch = auth()->user()->filiales->first->id;

        $auth = ["admin", "editor"];
        if (!in_array($role, $auth)) {
            abort(403, 'Unauthorized');
        }

        // $this->authorize('delete', $dette);

        if ($role !== "admin") {
            if (($dette->debtor_id !== $branch->id) and ($dette->creditor_id !== $branch->id)) {
                return response([
                    'message' => "you don't have the permission de to this action"
                ], 422);    
            }
        } 
        
        $dette->delete();
        return response()->json(['message' => 'Dette deleted successfully']);

    }
}
