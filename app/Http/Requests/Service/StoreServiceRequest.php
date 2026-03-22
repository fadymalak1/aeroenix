<?php

namespace App\Http\Requests\Service;

use App\Enums\ServiceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:services,slug'],
            'description' => ['required', 'string'],
            'price' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'string', 'max:2048'],
            'image_file' => ['nullable', 'file', 'image', 'max:5120'],
            'status' => ['required', Rule::enum(ServiceStatus::class)],
        ];
    }
}
