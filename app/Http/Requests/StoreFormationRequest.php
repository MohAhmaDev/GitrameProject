<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFormationRequest extends FormRequest
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
            "employe_id" => 'required|integer',
            'domaine_formation' => 'required|string|max:55',
            'diplomes_obtenues' => 'required|string|max:55',
            'intitule_formation' => 'required|string|max:55',
            'duree_formation' => 'required|numeric',
            'montant' => ['numeric', 'min:10000'],     
            'lieu_formation' => 'required|string|max:55',
        ];
    }
}
