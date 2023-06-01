<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Formation;
use App\Models\Retraiter;

class Employe extends Model
{
    use HasFactory;


    protected $fillable = [
        'nom', 'prenom', 'fonction', 'sexe', 'date_naissance', 'date_recrutement',
        'contract', 'temp_occuper', 'handicape', 'date_retraite', 'categ_sociopro',
        'position','observation', 'filiale_id'
    ];

    protected static function boot()
    {
        parent::boot();

        // generate employe_id before creating the record
        static::creating(function ($employe) {
            $employe->numero_securite_social = fake()->unique()->numerify("########");
        });
    }


    public function filiale()
    {
        return $this->belongsTo(Filiale::class);
    }

    public function formations() 
    {
        return $this->hasMany(Formation::class);
    }

    
}
