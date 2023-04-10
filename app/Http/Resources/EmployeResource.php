<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeResource extends JsonResource
{
    public static $warp = false;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'numero_securite_social' => $this->numero_securite_social,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'fonction' => $this->fonction,
            'sexe' => $this->sexe,
            'date_naissance' => $this->date_naissance,
            'date_recrutement' => $this->date_recrutement,
            'contract' => $this->contract,
            'temp_occuper' => $this->temp_occuper,
            'handicape' => $this->handicape,    
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),  
        ];

    }
}
