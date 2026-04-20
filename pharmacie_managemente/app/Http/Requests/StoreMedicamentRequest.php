<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreMedicamentRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
            'categorie_id' => ['required', 'exists:categories,id'],
            'nom' => ['required', 'string', 'max:255'],
            'code_barre' => ['required', 'string', 'unique:medicaments'],
            'description' => ['nullable', 'string'],
            'fabricant' => ['required', 'string'],
            'forme_dosage' => ['required', 'string'],
            'ordonnance_requise' => 'boolean',
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'numero_lot' => ['required', 'string'],
            'quantite' => ['required', 'integer', 'min:1'],
            'seuil_minimum' => ['required', 'integer', 'min:1'],
            'prix_achat' => ['required', 'numeric', 'min:0'],
            'prix_vente' => ['required', 'numeric', 'min:0'],
            'date_expiration' => ['required', 'date', 'after:today'],
        ];
    }
}
