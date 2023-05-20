<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public static $warp = false;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    
    public function toArray(Request $request): array
    {
        $role = $this->roles()->first();
        $roleName = $role ? $role->name : null;

        $filiale = $this->filiales()->first();
        $filiale_id = $filiale ? $filiale->id : null;
        $filiale_name = $filiale ? $filiale->nom_filiale : "non";

        $admission = $this->admissions()->first();
        $admission_id = $admission ? $admission->id : null;
        $admission_table = $admission ? $admission->table : "non";

        
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'role' => $roleName,
            'filiale' => ["id" => $filiale_id, "name" => $filiale_name],
            'admission' => ["id" => $admission_id, "table" => $admission_table]
        ];
    }

}
