<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDetteRequest extends FormRequest
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
            'intitule_projet' => 'required|string|max:55',
            'num_fact' => 'required|string|max:55',
            'num_situation' => 'required|string|max:55',
            'date_dettes' => ['required','date','before:2023-04-22'],
            'observations' => 'string',
            'creditor_type' => [
                'required',
                'string',
                Rule::in(['filiale', 'entreprise'])
            ],
            'creditor_id' => 'required|integer',
            'debtor_type' => [
                'required',
                'string',
                Rule::in(['filiale', 'entreprise'])
            ],
            'debtor_id' => 'required|integer',
            'montant' => ['numeric', 'min:10000'],
            'montant_encaissement' => 'numeric',
            'regler' => 'boolean'
        ];
    }
}
