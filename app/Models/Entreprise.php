<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Dette;
use App\Models\Creance;

class Entreprise extends Model
{
    use HasFactory;

    protected $fillable = [
        "nom_entreprise", "groupe", "secteur", "nationalite", "adresse",
        "num_tel_entr", "adress_emil_entr", "status_juridique"
    ];

    public function dettes_creditor()
    {
        return $this->morphMany(Dette::class, 'creditor');
    }

    public function dettes_debtor()
    {
        return $this->morphMany(Dette::class, 'debtor');
    }

    public function creances_creditor()
    {
        return $this->morphMany(Creance::class, 'creditor');
    }

    public function creances_debtor()
    {
        return $this->morphMany(Creance::class, 'debtor');
    }
}
