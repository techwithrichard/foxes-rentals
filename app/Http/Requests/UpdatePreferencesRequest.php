<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePreferencesRequest extends FormRequest
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
        return [
            'locale' => ['nullable', 'string', 'in:en,es,fr,de,it,pt,ru,zh,ja,ko'],
            'timezone' => ['nullable', 'string', 'timezone'],
            'date_format' => ['nullable', 'string', 'in:Y-m-d,d-m-Y,m/d/Y,d/m/Y'],
            'time_format' => ['nullable', 'string', 'in:H:i,ga'],
            'currency' => ['nullable', 'string', 'size:3'],
            'email_notifications' => ['boolean'],
            'sms_notifications' => ['boolean'],
            'push_notifications' => ['boolean'],
            'marketing_emails' => ['boolean'],
            'two_factor_enabled' => ['boolean'],
            'session_timeout' => ['nullable', 'integer', 'min:5', 'max:480'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'locale.in' => 'Please select a valid language.',
            'timezone.timezone' => 'Please select a valid timezone.',
            'date_format.in' => 'Please select a valid date format.',
            'time_format.in' => 'Please select a valid time format.',
            'currency.size' => 'Currency must be a 3-character code.',
            'session_timeout.min' => 'Session timeout must be at least 5 minutes.',
            'session_timeout.max' => 'Session timeout must not exceed 8 hours.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'locale' => 'language',
            'timezone' => 'timezone',
            'date_format' => 'date format',
            'time_format' => 'time format',
            'currency' => 'currency',
            'email_notifications' => 'email notifications',
            'sms_notifications' => 'SMS notifications',
            'push_notifications' => 'push notifications',
            'marketing_emails' => 'marketing emails',
            'two_factor_enabled' => 'two-factor authentication',
            'session_timeout' => 'session timeout',
        ];
    }
}
