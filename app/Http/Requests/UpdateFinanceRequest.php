<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFinanceRequest extends FormRequest
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
            'type_activite' => [
                'required',
                'string',
                Rule::in(['vente', 'consomation', 'autre'])
            ],
            'activite' => 'required|string|max:55',
            'date_activite' => ['required','date','before:2023-04-22'],
            'privision' => ['numeric', 'min:10000'],
            'realisation' => ['numeric', 'min:10000'],
            'compte_scf' => 'required|string|max:55',
            "filiale_id" => 'required|integer'
        ];
    }
}
