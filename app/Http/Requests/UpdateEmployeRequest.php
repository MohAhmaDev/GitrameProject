<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeRequest extends FormRequest
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
            "fonction" => 'required|string|max:55',
            "sexe" => [
                'required',
                Rule::in(['Femme', 'Homme'])
            ],
            "date_naissance" => 'required|date|after:01-01-1960',
            "date_recrutement" => 'required|date|after:01-01-2016',
            "contract" => [
                'required',
                Rule::in(['CDI', 'CDD'])
            ],
            "temp_occuper" => [
                'required',
                Rule::in(['Temps plein', 'Temps partiel'])
            ],
            "handicape" => 'boolean|required',            
        ];
    }
}
