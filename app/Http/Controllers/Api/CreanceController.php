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


        $this->authorize('viewAny', Creance::class);
        
        if ($role === "global") {
            $creance = Creance::all();
        } else 
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

        $auth = ["global", "editor"];
        if (!in_array($role, $auth)) {
            abort(403, 'Unauthorized');
        }

        // $this->authorize('create', Creance::class);

        $data = $request->validated();
        $creditor_type = $data['creditor_type']; 
        $debtor_type = $data['debtor_type'];
        $anteriorite_creance = $data['anteriorite_creance'];
        $date_creance = $data['date_creance'];

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

        if ($role !== "global") {
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

        if ($date_creance > $anteriorite_creance) {
            return response([
                'message' => "la date d'anteriorite de la creance doit etre supperieur à la date de facturation"
            ], 422);              
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
        $this->authorize('view', $creance);

        

        if ($role !== "global") {
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

        $auth = ["global", "editor"];
        if (!in_array($role, $auth)) {
            abort(403, 'Unauthorized');
        }

        // $this->authorize('update', $creance);


        
        if ($role !== "global") {
            if (($creance->debtor_id !== $branch->id) and ($creance->creditor_id !== $branch->id)) {
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

        $auth = ["global", "editor"];
        if (!in_array($role, $auth)) {
            abort(403, 'Unauthorized');
        }
        // $this->authorize('delete', $creance);

        if ($role !== "global") {
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

