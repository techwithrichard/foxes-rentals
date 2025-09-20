<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PropertyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('create property') || auth()->user()->can('edit property');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $propertyId = $this->route('property') ? $this->route('property')->id : null;
        
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|string|in:house,bungalow,apartment,commercial,industrial,land',
            'rent' => 'required|numeric|min:0|max:999999',
            'deposit' => 'nullable|numeric|min:0|max:999999',
            'landlord_id' => 'required|exists:users,id',
            'commission' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|string|in:active,inactive,maintenance',
            'is_vacant' => 'boolean',
            'electricity_id' => 'nullable|string|max:255',
            
            // Address fields
            'address.street' => 'nullable|string|max:255',
            'address.city' => 'nullable|string|max:100',
            'address.state' => 'nullable|string|max:100',
            'address.postal_code' => 'nullable|string|max:20',
            'address.country' => 'nullable|string|max:100',
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
            'name.required' => 'Property name is required.',
            'name.max' => 'Property name cannot exceed 255 characters.',
            'type.required' => 'Property type is required.',
            'type.in' => 'Invalid property type selected.',
            'rent.required' => 'Rent amount is required.',
            'rent.numeric' => 'Rent amount must be a valid number.',
            'rent.min' => 'Rent amount cannot be negative.',
            'rent.max' => 'Rent amount cannot exceed 999,999.',
            'landlord_id.required' => 'Landlord selection is required.',
            'landlord_id.exists' => 'Selected landlord does not exist.',
            'commission.numeric' => 'Commission must be a valid number.',
            'commission.min' => 'Commission cannot be negative.',
            'commission.max' => 'Commission cannot exceed 100%.',
            'status.required' => 'Property status is required.',
            'status.in' => 'Invalid property status selected.',
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
            'name' => 'property name',
            'description' => 'property description',
            'type' => 'property type',
            'rent' => 'rent amount',
            'deposit' => 'deposit amount',
            'landlord_id' => 'landlord',
            'commission' => 'commission percentage',
            'status' => 'property status',
            'is_vacant' => 'vacancy status',
            'electricity_id' => 'electricity meter ID',
            'address.street' => 'street address',
            'address.city' => 'city',
            'address.state' => 'state/province',
            'address.postal_code' => 'postal code',
            'address.country' => 'country',
        ];
    }
}
