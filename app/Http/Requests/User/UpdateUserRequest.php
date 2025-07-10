<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = Auth::user();
        return $user && ($user->hasRole('super-admin') || $user->hasRole('admin'));
    }

    public function rules(): array
    {
        $userId = $this->route('user');
        
        $rules = [
            'name' => ['sometimes', 'string', 'min:2', 'max:255'],
            'email' => [
                'sometimes',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId)
            ],
            'password' => ['sometimes', 'string', 'min:8', 'max:255'],
            'company_id' => ['sometimes', 'nullable', 'integer', 'exists:companies,id'],
        ];

        // Si el usuario es admin, solo puede asignar su propia company_id
        if (Auth::user()->hasRole('admin')) {
            $rules['company_id'] = ['sometimes', 'nullable', 'integer', 'exists:companies,id', 'in:' . Auth::user()->company_id];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.string' => 'The name must be a string.',
            'name.min' => 'The name must be at least 2 characters.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'The email has already been taken.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.max' => 'The password may not be greater than 255 characters.',
            'company_id.integer' => 'The company ID must be an integer.',
            'company_id.exists' => 'The selected company does not exist.',
            'company_id.in' => 'You can only assign users to your own company.',
        ];
    }
} 