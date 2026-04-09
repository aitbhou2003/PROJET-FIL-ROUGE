<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreRegisterRequest extends FormRequest
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
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users', 'email'],
            'password' => ['required', 'string', 'confirmed', 'min:6'],
            'role_id' => ['required', 'exists:roles', 'id'],
            'is_actif' => ['boolean'],

        ];
    }

    public function messages(): array
    {
        return [
            'firstname.required' => 'Le prénom est obligatoire',
            'lastname.required' => 'Le nom est obligatoire',
            'email.required' => "L'email est obligatoire",
            'email.unique' => 'Cet email est déjà utilisé',
            'password.confirmed' => 'Les mots de passe ne correspondent pas',
            'role_id.required' => 'Le rôle est obligatoire',
            'role_id.exists' => 'Ce rôle n\'existe pas',
        ];
    }
}
