<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStagiareRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            "nom" => 'required|string|max:55',
            "prenom" => 'required|string|max:55',
            "date_naissance" => ['required','date','after:1960-01-01', 'before:2005-01-01'],
            'domaine_formation' => 'required|string|max:55',
            'diplomes_obtenues' => 'required|string|max:55',
            'intitule_formation' => 'required|string|max:55',
            'duree_formation' => 'required|numeric',
            'montant' => ['numeric', 'min:10000'],     
            'lieu_formation' => 'required|string|max:55',
        ];
    }
}
