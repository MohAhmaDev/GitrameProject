<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Entreprise;

class Dette extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'id',
        'id_entreprise',
        'id_ent_debitrice',
        'nom_ent_debitrice',
        'intitule_projet',
        'num_fact',
        'num_situation',
        'date_dettes',
        'montant',
        'observations',
        'filiale_id'
    ];
    
    public function entreprises()
    {
        return $this->belongsToMany(Entreprises::class, 'id_entreprise');
    }
    public function entDebitrice()
    {
        return $this->belongsToMany(Entreprises::class, 'id_ent_debitrice');
    }
    public function filiale()
    {
        return $this->belongsTo(Filiale::class);
    }

}
