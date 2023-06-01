<?php

namespace App\Http\Resources;

use App\Models\Employe;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $employe_name = Employe::find($this->employe_id)->nom;

        return [
            'id' => $this->id,
            'employe' => $employe_name,
            'employe_id' => $this->employe_id,
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
