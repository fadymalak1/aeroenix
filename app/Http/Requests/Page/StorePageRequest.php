<?php

namespace App\Http\Requests\Page;

use App\Enums\PageStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:pages,slug'],
            'path' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'status' => ['required', Rule::enum(PageStatus::class)],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
        ];
    }
}
