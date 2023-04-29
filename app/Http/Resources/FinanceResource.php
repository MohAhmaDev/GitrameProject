<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FinanceResource extends JsonResource
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
            'type_activite' => $this->type_activite,
            'activite' => $this->activite,
            'date_activite' => $this->date_activite,
            'privision' => $this->privision,
            'realisation' => $this->realisation,
            'compte_scf' => $this->compte_scf,
            'filiale_id' => $this->filiale_id,           
        ];
    }
}
