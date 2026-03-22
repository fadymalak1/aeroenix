<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_name' => ['nullable', 'string', 'max:255'],
            'site_title' => ['nullable', 'string', 'max:255'],
            'site_description' => ['nullable', 'string'],
            'logo' => ['nullable', 'string', 'max:2048'],
            'logo_file' => ['nullable', 'file', 'image', 'max:5120'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'facebook_url' => ['nullable', 'string', 'max:2048'],
            'linkedin_url' => ['nullable', 'string', 'max:2048'],
            'instagram_url' => ['nullable', 'string', 'max:2048'],
            'twitter_url' => ['nullable', 'string', 'max:2048'],
            'footer_text' => ['nullable', 'string'],
        ];
    }
}
