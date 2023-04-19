<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Creance extends Model
{
    use HasFactory;


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
