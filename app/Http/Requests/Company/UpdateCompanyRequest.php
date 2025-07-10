<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
                'email',
                'max:255',
                Rule::unique('companies', 'email')->ignore($this->route('company'))
            ],
            'phone' => 'sometimes|string|max:20',
            'address' => 'sometimes|string|max:500',
            'is_active' => 'sometimes|boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'name.max' => 'El nombre de la empresa no puede exceder 255 caracteres',
            'email.email' => 'El formato del email no es válido',
            'email.max' => 'El email no puede exceder 255 caracteres',
            'email.unique' => 'El email ya está registrado',
            'phone.max' => 'El teléfono no puede exceder 20 caracteres',
            'address.max' => 'La dirección no puede exceder 500 caracteres'
        ];
    }
} 