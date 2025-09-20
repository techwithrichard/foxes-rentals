<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HouseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('create house') || auth()->user()->can('edit house');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'property_id' => 'required|exists:properties,id',
            'rent' => 'required|numeric|min:0|max:999999',
            'deposit' => 'nullable|numeric|min:0|max:999999',
            'landlord_id' => 'required|exists:users,id',
            'status' => 'required|string|in:active,inactive,maintenance',
            'is_vacant' => 'boolean',
            'bedrooms' => 'nullable|integer|min:0|max:20',
            'bathrooms' => 'nullable|integer|min:0|max:20',
            'size' => 'nullable|numeric|min:0|max:99999',
            'house_type_id' => 'nullable|exists:house_types,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'House name is required.',
            'name.max' => 'House name cannot exceed 255 characters.',
            'description.max' => 'House description cannot exceed 1000 characters.',
            'property_id.required' => 'Property selection is required.',
            'property_id.exists' => 'Selected property does not exist.',
            'rent.required' => 'Rent amount is required.',
            'rent.numeric' => 'Rent amount must be a valid number.',
            'rent.min' => 'Rent amount cannot be negative.',
            'rent.max' => 'Rent amount cannot exceed 999,999.',
            'deposit.numeric' => 'Deposit amount must be a valid number.',
            'deposit.min' => 'Deposit amount cannot be negative.',
            'deposit.max' => 'Deposit amount cannot exceed 999,999.',
            'landlord_id.required' => 'Landlord selection is required.',
            'landlord_id.exists' => 'Selected landlord does not exist.',
            'status.required' => 'House status is required.',
            'status.in' => 'Invalid house status selected.',
            'bedrooms.integer' => 'Number of bedrooms must be a whole number.',
            'bedrooms.min' => 'Number of bedrooms cannot be negative.',
            'bedrooms.max' => 'Number of bedrooms cannot exceed 20.',
            'bathrooms.integer' => 'Number of bathrooms must be a whole number.',
            'bathrooms.min' => 'Number of bathrooms cannot be negative.',
            'bathrooms.max' => 'Number of bathrooms cannot exceed 20.',
            'size.numeric' => 'House size must be a valid number.',
            'size.min' => 'House size cannot be negative.',
            'size.max' => 'House size cannot exceed 99,999.',
            'house_type_id.exists' => 'Selected house type does not exist.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'house name',
            'description' => 'house description',
            'property_id' => 'property',
            'rent' => 'rent amount',
            'deposit' => 'deposit amount',
            'landlord_id' => 'landlord',
            'status' => 'house status',
            'is_vacant' => 'vacancy status',
            'bedrooms' => 'number of bedrooms',
            'bathrooms' => 'number of bathrooms',
            'size' => 'house size',
            'house_type_id' => 'house type',
        ];
    }
}
