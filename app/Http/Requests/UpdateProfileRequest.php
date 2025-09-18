<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $userId = auth()->id();

        return [
            'name' => ['required', 'string', 'max:255', 'min:2'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($userId)
            ],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[\+]?[0-9\s\-\(\)]+$/'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'address' => ['nullable', 'string', 'max:500'],
            'occupation_status' => ['nullable', 'string', 'max:100'],
            'occupation_place' => ['nullable', 'string', 'max:255'],
            'kin_name' => ['nullable', 'string', 'max:255'],
            'kin_identity' => ['nullable', 'string', 'max:50'],
            'kin_phone' => ['nullable', 'string', 'max:20', 'regex:/^[\+]?[0-9\s\-\(\)]+$/'],
            'kin_relationship' => ['nullable', 'string', 'max:100'],
            'emergency_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact' => ['nullable', 'string', 'max:20', 'regex:/^[\+]?[0-9\s\-\(\)]+$/'],
            'emergency_email' => ['nullable', 'email', 'max:255'],
            'emergency_relationship' => ['nullable', 'string', 'max:100'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please enter your full name.',
            'name.min' => 'Name must be at least 2 characters long.',
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already in use.',
            'phone.regex' => 'Please enter a valid phone number.',
            'profile_picture.image' => 'Profile picture must be an image file.',
            'profile_picture.mimes' => 'Profile picture must be a JPEG, PNG, JPG, or GIF file.',
            'profile_picture.max' => 'Profile picture must not be larger than 2MB.',
            'address.max' => 'Address must not exceed 500 characters.',
            'occupation_status.max' => 'Occupation status must not exceed 100 characters.',
            'occupation_place.max' => 'Occupation place must not exceed 255 characters.',
            'kin_name.max' => 'Next of kin name must not exceed 255 characters.',
            'kin_identity.max' => 'Next of kin identity must not exceed 50 characters.',
            'kin_phone.regex' => 'Please enter a valid phone number for next of kin.',
            'kin_relationship.max' => 'Next of kin relationship must not exceed 100 characters.',
            'emergency_name.max' => 'Emergency contact name must not exceed 255 characters.',
            'emergency_contact.regex' => 'Please enter a valid phone number for emergency contact.',
            'emergency_email.email' => 'Please enter a valid email address for emergency contact.',
            'emergency_relationship.max' => 'Emergency contact relationship must not exceed 100 characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'full name',
            'email' => 'email address',
            'phone' => 'phone number',
            'profile_picture' => 'profile picture',
            'address' => 'address',
            'occupation_status' => 'occupation status',
            'occupation_place' => 'occupation place',
            'kin_name' => 'next of kin name',
            'kin_identity' => 'next of kin identity',
            'kin_phone' => 'next of kin phone',
            'kin_relationship' => 'next of kin relationship',
            'emergency_name' => 'emergency contact name',
            'emergency_contact' => 'emergency contact phone',
            'emergency_email' => 'emergency contact email',
            'emergency_relationship' => 'emergency contact relationship',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower(trim($this->email)),
            'name' => trim($this->name),
            'phone' => $this->phone ? preg_replace('/[^0-9+\-\(\)\s]/', '', $this->phone) : null,
            'kin_phone' => $this->kin_phone ? preg_replace('/[^0-9+\-\(\)\s]/', '', $this->kin_phone) : null,
            'emergency_contact' => $this->emergency_contact ? preg_replace('/[^0-9+\-\(\)\s]/', '', $this->emergency_contact) : null,
            'emergency_email' => $this->emergency_email ? strtolower(trim($this->emergency_email)) : null,
        ]);
    }
}
