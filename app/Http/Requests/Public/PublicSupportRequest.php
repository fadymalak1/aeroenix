<?php

namespace App\Http\Requests\Public;

use App\Enums\TicketPriority;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PublicSupportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'customer' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'description' => ['required', 'string'],
            'priority' => ['nullable', Rule::enum(TicketPriority::class)],
        ];
    }
}
