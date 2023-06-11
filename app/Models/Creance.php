<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Creance extends Model
{
    use HasFactory;
    
    public $fillable = ['intitule_projet', 'num_fact', 'num_situation',
     'anteriorite_creance', 'date_creance', 'montant', 'observations', 'debtor_id'
     , 'debtor_type','creditor_id', 'creditor_type', 'montant_encaissement', 'regler'];

    public function creditor()
    {
        return $this->morphTo();
    }

    public function debtor()
    {
        return $this->morphTo();
    }
}
