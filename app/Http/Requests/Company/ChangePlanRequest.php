<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class ChangePlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'plan_id' => 'required|integer|exists:plans,id'
        ];
    }

    public function messages(): array
    {
        return [
            'plan_id.required' => 'El plan es obligatorio',
            'plan_id.exists' => 'El plan seleccionado no existe'
        ];
    }
} 