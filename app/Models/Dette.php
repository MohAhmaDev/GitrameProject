<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dette extends Model
{
    use HasFactory;
    public $fillable = ['intitule_projet', 'num_fact', 'num_situation',
     'date_dettes', 'montant', 'observations', 'debtor_id', 'debtor_type',
     'creditor_id', 'creditor_type'];

    public function creditor()
    {
        return $this->morphTo();
    }


    public function debtor()
    {
        return $this->morphTo();
    }
}
