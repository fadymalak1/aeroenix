<?php

namespace App\Http\Requests\Project;

use App\Enums\ProjectCategory;
use App\Enums\ProjectStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:projects,slug'],
            'client' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::enum(ProjectCategory::class)],
            'description' => ['required', 'string'],
            'thumbnail' => ['nullable', 'string', 'max:2048'],
            'thumbnail_file' => ['nullable', 'file', 'image', 'max:5120'],
            'status' => ['required', Rule::enum(ProjectStatus::class)],
            'published_at' => ['nullable', 'date'],
        ];
    }
}
