<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class CreateCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:companies,email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'is_active' => 'boolean',
            'plan_id' => 'required|integer|exists:plans,id',
            'admin_email' => 'required|email|max:255|unique:users,email',
            'admin_password' => 'required|string|min:8|max:255',
            'admin_name' => 'nullable|string|min:2|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la empresa es obligatorio',
            'name.max' => 'El nombre de la empresa no puede exceder 255 caracteres',
            'email.required' => 'El email de la empresa es obligatorio',
            'email.email' => 'El formato del email no es válido',
            'email.max' => 'El email no puede exceder 255 caracteres',
            'email.unique' => 'El email ya está registrado',
            'phone.required' => 'El teléfono de la empresa es obligatorio',
            'phone.max' => 'El teléfono no puede exceder 20 caracteres',
            'address.required' => 'La dirección de la empresa es obligatoria',
            'address.max' => 'La dirección no puede exceder 500 caracteres',
            'plan_id.required' => 'El plan es obligatorio',
            'plan_id.exists' => 'El plan seleccionado no existe',
            'admin_email.required' => 'El email del admin es obligatorio',
            'admin_email.email' => 'El formato del email del admin no es válido',
            'admin_email.max' => 'El email del admin no puede exceder 255 caracteres',
            'admin_email.unique' => 'El email del admin ya está registrado',
            'admin_password.required' => 'La contraseña del admin es obligatoria',
            'admin_password.min' => 'La contraseña del admin debe tener al menos 8 caracteres',
            'admin_password.max' => 'La contraseña del admin no puede exceder 255 caracteres',
            'admin_name.min' => 'El nombre del admin debe tener al menos 2 caracteres',
            'admin_name.max' => 'El nombre del admin no puede exceder 255 caracteres',
        ];
    }
} 