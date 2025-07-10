<?php

namespace App\Http\Requests\Plan;

use Illuminate\Foundation\Http\FormRequest;

class CreatePlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'monthly_price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'user_limit' => ['required', 'integer', 'min:1', 'max:1000000'],
            'features' => ['required', 'array', 'min:1'],
            'features.*' => ['required', 'string', 'min:1', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The plan name field is required.',
            'name.min' => 'The plan name must be at least 2 characters.',
            'name.max' => 'The plan name may not be greater than 255 characters.',
            'monthly_price.required' => 'The monthly price field is required.',
            'monthly_price.numeric' => 'The monthly price must be a number.',
            'monthly_price.min' => 'The monthly price must be at least 0.',
            'monthly_price.max' => 'The monthly price may not be greater than 999,999.99.',
            'user_limit.required' => 'The user limit field is required.',
            'user_limit.integer' => 'The user limit must be an integer.',
            'user_limit.min' => 'The user limit must be at least 1.',
            'user_limit.max' => 'The user limit may not be greater than 1,000,000.',
            'features.required' => 'The features field is required.',
            'features.array' => 'The features must be an array.',
            'features.min' => 'The features must have at least 1 item.',
            'features.*.required' => 'Each feature is required.',
            'features.*.string' => 'Each feature must be a string.',
            'features.*.min' => 'Each feature must be at least 1 character.',
            'features.*.max' => 'Each feature may not be greater than 255 characters.',
        ];
    }
} 