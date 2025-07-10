<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = Auth::user();
        return $user && ($user->hasRole('super-admin') || $user->hasRole('admin'));
    }

    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
        ];

        // Si el usuario es admin, solo puede asignar su propia company_id
        if (Auth::user()->hasRole('super-admin')) {
            $rules['company_id'] = ['nullable', 'integer', 'exists:companies,id'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.min' => 'The name must be at least 2 characters.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'The email has already been taken.',
            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.max' => 'The password may not be greater than 255 characters.',
            'company_id.integer' => 'The company ID must be an integer.',
            'company_id.exists' => 'The selected company does not exist.'
        ];
    }
} 