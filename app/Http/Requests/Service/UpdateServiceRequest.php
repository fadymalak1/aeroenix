<?php

namespace App\Http\Requests\Service;

use App\Enums\ServiceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $service = $this->route('service');

        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('services', 'slug')->ignore($service?->id)],
            'description' => ['sometimes', 'required', 'string'],
            'price' => ['sometimes', 'required', 'string', 'max:255'],
            'image' => ['nullable', 'string', 'max:2048'],
            'image_file' => ['nullable', 'file', 'image', 'max:5120'],
            'status' => ['sometimes', 'required', Rule::enum(ServiceStatus::class)],
        ];
    }
}
