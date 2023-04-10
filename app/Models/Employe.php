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
        'contract', 'temp_occuper', 'handicape'
    ];

    protected static function boot()
    {
        parent::boot();

        // generate employe_id before creating the record
        static::creating(function ($employe) {
            $employe->numero_securite_social = fake()->unique()->numerify("employe-#######");
        });
    }

    public function formation() 
    {
        return $this->hasOne(Formation::class);
    }

    public function retraiter() 
    {
        return $this->hasOne(Retraiter::class);
    }


    
}
