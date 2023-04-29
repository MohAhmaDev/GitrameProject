<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Finance;
use App\Http\Requests\StoreFinanceRequest;
use App\Http\Requests\UpdateFinanceRequest;
use App\Models\Filiale;
use App\Http\Resources\FinanceResource;

class FinanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $role = auth()->user()->roles->first()->name;
        $branch = auth()->user()->filiales->first();


        if ($role === "admin") {
            $finance = Finance::all();
        } 
        else {
            $id = $branch->id;
            if (!is_null($id)) {
                $filiale = Filiale::findOrFail($id);
                $finance = $filiale->finances;
            } else {
                return response([]);
            }
        }
        return FinanceResource::collection($finance);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFinanceRequest $request)
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
        $finance = Finance::create($data);
        return new FinanceResource($finance);
    }

    /**
     * Display the specified resource.
     */
    public function show(Finance $finance)
    {

        $role = auth()->user()->roles->first()->name;
        $branch = auth()->user()->filiales->first();

        if ($role !== "admin") {
            if ($finance->filiale_id !== $branch->id) {
                return response([
                    'message' => "you don't have the permission de to this action"
                ], 422);    
            }
        }

        return new FinanceResource($finance);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFinanceRequest $request, Finance $finance)
    {
        $role = auth()->user()->roles->first()->name;
        $branch = auth()->user()->filiales->first();


        $auth = ["admin", "editor"];
        if (!in_array($role, $auth)) {
            abort(403, 'Unauthorized');
        }

        if ($role !== "admin") {
            if ($finance->filiale_id !== $branch->id) {
                return response([
                    'message' => "you don't have the permission de to this action"
                ], 422);    
            }
        }

        $data = $request->validated();
        $finance->update($data);
        return new FinanceResource($finance);    
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Finance $finance)
    {
        $role = auth()->user()->roles->first()->name;
        $branch = auth()->user()->filiales->first->id;

        $auth = ["admin", "editor"];
        if (!in_array($role, $auth)) {
            abort(403, 'Unauthorized');
        }

        if ($role !== "admin") {
            if ($finance->filiale_id !== $branch->id) {
                return response([
                    'message' => "you don't have the permission de to this action"
                ], 422);    
            }
        }  
        
        $finance->delete();
        return response()->json(['message' => 'Employee deleted successfully']);
    }
}
