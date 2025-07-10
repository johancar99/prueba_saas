<?php

namespace App\Http\Requests\Plan;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'min:2', 'max:255'],
            'monthly_price' => ['sometimes', 'numeric', 'min:0', 'max:999999.99'],
            'user_limit' => ['sometimes', 'integer', 'min:1', 'max:1000000'],
            'features' => ['sometimes', 'array', 'min:1'],
            'features.*' => ['required_with:features', 'string', 'min:1', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'The plan name must be a string.',
            'name.min' => 'The plan name must be at least 2 characters.',
            'name.max' => 'The plan name may not be greater than 255 characters.',
            'monthly_price.numeric' => 'The monthly price must be a number.',
            'monthly_price.min' => 'The monthly price must be at least 0.',
            'monthly_price.max' => 'The monthly price may not be greater than 999,999.99.',
            'user_limit.integer' => 'The user limit must be an integer.',
            'user_limit.min' => 'The user limit must be at least 1.',
            'user_limit.max' => 'The user limit may not be greater than 1,000,000.',
            'features.array' => 'The features must be an array.',
            'features.min' => 'The features must have at least 1 item.',
            'features.*.required_with' => 'Each feature is required when features are provided.',
            'features.*.string' => 'Each feature must be a string.',
            'features.*.min' => 'Each feature must be at least 1 character.',
            'features.*.max' => 'Each feature may not be greater than 255 characters.',
        ];
    }
} 