<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StagiareResource extends JsonResource
{
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
            'filiale_id' => $this->filiale_id,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'date_naissance' => $this->date_naissance,
            'domaine_formation' => $this->domaine_formation,
            'diplomes_obtenues' => $this->diplomes_obtenues,
            'intitule_formation' => $this->intitule_formation,
            'duree_formation' => $this->duree_formation,
            'montant' => $this->montant,
            'lieu_formation' => $this->lieu_formation,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),  
        ];
    }
}
