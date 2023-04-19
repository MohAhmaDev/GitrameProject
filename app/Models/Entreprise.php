<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Finance;
use App\Models\Filiale;
use App\Models\Dette;

class Entreprise extends Model
{
    protected $table = 'entreprises';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id', 'nom_entreprise', 'groupe', 'secteur', 'nationalite', 'filiale_id'
    ];

    public function filiale()
    {
        return $this->belongsTo(Filiale::class);
    }

    public function dettes() 
    {
        return $this->belongsToMany(Dette::class);
    }

    public function creances() 
    {
        return $this->belongsToMany(Creance::class);
    }

    public function finace()
    {
        return $this->hasOne(Finance::class);
    }

}
