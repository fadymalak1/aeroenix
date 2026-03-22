<?php

namespace App\Http\Requests\Ticket;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'customer' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'email', 'max:255'],
            'description' => ['sometimes', 'required', 'string'],
            'priority' => ['sometimes', 'required', Rule::enum(TicketPriority::class)],
            'status' => ['sometimes', 'required', Rule::enum(TicketStatus::class)],
            'date' => ['nullable', 'date'],
        ];
    }
}
