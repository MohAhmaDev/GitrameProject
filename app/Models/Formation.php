<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employe;

class Formation extends Model
{
    use HasFactory;
    
    public $fillable = [
        'id', 'employe_id', 'domaine_formation', 'diplomes_obtenues', 'intitule_formation', 'duree_formation'
        , 'montant', 'lieu_formation',
    ];


    public function employe()
    {
        return $this->belongsTo(Employe::class);
    }
}
