<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Entreprise;
use App\Models\Filiale;

class CreanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $role = null;
        $filiale = $request->user()->filiales()->first();
        $filiale_name = $filiale ? $filiale->nom_filiale : null;

        $firme_c = $this->creditor_type;
        $id_c = $this->creditor_id;
        if ($firme_c === Entreprise::class) {
            $creditor = Entreprise::where('id', $id_c)->firstOrFail()->nom_entreprise;
        } elseif ($firme_c === Filiale::class) {
            $creditor = Filiale::where('id', $id_c)->firstOrFail()->nom_filiale;
        } else {
           $creditor = NULL; 
        }

        $firme_d = $this->debtor_type;
        $id_d = $this->debtor_id;
        if ($firme_d === Entreprise::class) {
            $debtor = Entreprise::where('id', $id_d)->firstOrFail()->nom_entreprise;
        } elseif ($firme_d === Filiale::class) {
            $debtor = Filiale::where('id', $id_d)->firstOrFail()->nom_filiale;
        } else {
           $debtor = NULL; 
        }

        if (!is_null($filiale_name)) {
            if ($filiale_name === $creditor) {
                $role = "creditor";
            } elseif ($filiale_name === $debtor) {
                $role = "debtor";
            }
        }

        return [
            'id' => $this->id,
            'intitule_projet' => $this->intitule_projet,
            'num_fact' => $this->num_fact,
            'num_situation' => $this->num_situation,
            'anteriorite_creance' => $this->anteriorite_creance,
            'date_creance' => $this->date_creance,
            'montant' => $this->montant,
            'observations' => $this->observations,
            'creditor' => $creditor,
            'debtor' => $debtor,
            'creditor_type' => $this->creditor_type === Filiale::class ? "filiale" : "entreprise",
            'debtor_type' => $this->debtor_type === Filiale::class ? "filiale" : "entreprise",
            'debtor_id' => $this->debtor_id,        
            'creditor_id' => $this->creditor_id,
            'role' => is_null($role) ? "" : $role,
        ];
    }
}
