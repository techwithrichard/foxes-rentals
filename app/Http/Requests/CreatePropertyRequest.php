<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePropertyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('create property');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|min:3',
            'description' => 'nullable|string|max:1000',
            'type' => 'nullable|string|max:100',
            'is_multi_unit' => 'boolean',
            'rent' => 'required|numeric|min:0|max:999999999.99',
            'deposit' => 'nullable|numeric|min:0|max:999999999.99',
            'is_vacant' => 'boolean',
            'status' => 'required|in:active,inactive,maintenance',
            'landlord_id' => 'nullable|exists:users,id',
            'commission' => 'required|numeric|min:0|max:100',
            'electricity_id' => 'nullable|string|max:50',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Property name is required.',
            'name.min' => 'Property name must be at least 3 characters.',
            'rent.required' => 'Rent amount is required.',
            'rent.min' => 'Rent amount must be greater than 0.',
            'rent.max' => 'Rent amount is too large.',
            'status.required' => 'Property status is required.',
            'status.in' => 'Invalid property status.',
            'landlord_id.exists' => 'Selected landlord does not exist.',
            'commission.required' => 'Commission rate is required.',
            'commission.max' => 'Commission rate cannot exceed 100%.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'property name',
            'rent' => 'rent amount',
            'deposit' => 'deposit amount',
            'landlord_id' => 'landlord',
            'electricity_id' => 'electricity ID',
        ];
    }
}
