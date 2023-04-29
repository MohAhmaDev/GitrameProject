<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Finance;
use App\Models\Stagiare;

class Filiale extends Model
{
    use HasFactory;


    public function employes() 
    {
        return $this->hasMany(Employe::class);
    }

    public function stagiares()
    {
        return $this->hasMany(Stagiare::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function finances()
    {
        return $this->hasMany(Finance::class);
    }

    public function dettes_creditor()
    {
        return $this->morphMany(Dette::class, 'creditor');
    }

    public function dettes_debtor()
    {
        return $this->morphMany(Dette::class, 'debtor');
    }


}
