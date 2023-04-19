<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Filiale extends Model
{
    use HasFactory;


    public function employes() 
    {
        return $this->hasMany(Employe::class);
    }

    public function entreprises()
    {
        return $this->hasMany(Entreprise::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function dettes() 
    {
        return $this->hasMany(Dette::class);
    }

    public function stagiaires()
    {
        return $this->hasMany(Stagiaire::class);
    }

}
