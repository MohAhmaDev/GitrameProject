<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stagiare extends Model
{
    use HasFactory;

    protected $fillable = [
        'id', 'nom', 'prenom', 'date_naissance', 'numero_securite_social', 'filiale_id',
        'domaine_formation', 'diplomes_obtenues', 'intitule_formation', 'duree_formation'
        , 'montant', 'lieu_formation'
    ];

    protected static function boot()
    {
        parent::boot();

        // generate employe_id before creating the record
        static::creating(function ($stagiare) {
            $stagiare->numero_securite_social = fake()->unique()->numerify("########");
        });
    }


    public function filiale()
    {
        return $this->belongsTo(Filiale::class);
    }

}
