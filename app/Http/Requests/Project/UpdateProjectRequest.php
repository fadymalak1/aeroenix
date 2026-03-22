<?php

namespace App\Http\Requests\Project;

use App\Enums\ProjectCategory;
use App\Enums\ProjectStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $project = $this->route('project');

        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('projects', 'slug')->ignore($project?->id)],
            'client' => ['sometimes', 'required', 'string', 'max:255'],
            'category' => ['sometimes', 'required', Rule::enum(ProjectCategory::class)],
            'description' => ['sometimes', 'required', 'string'],
            'thumbnail' => ['nullable', 'string', 'max:2048'],
            'thumbnail_file' => ['nullable', 'file', 'image', 'max:5120'],
            'status' => ['sometimes', 'required', Rule::enum(ProjectStatus::class)],
            'published_at' => ['nullable', 'date'],
        ];
    }
}
