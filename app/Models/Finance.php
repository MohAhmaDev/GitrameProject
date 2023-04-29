<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Filiale;

class Finance extends Model
{
    use HasFactory;

    public $fillable = ['id', 'filiale_id', 'activite', 'type_activite',
    'date_activite', 'compte_scf', 'privision', 'realisation', '	created_at',
    'updated_at'];

    public function filiale()
    {
        return $this->belongsTo(Filiale::class);
    }
}
