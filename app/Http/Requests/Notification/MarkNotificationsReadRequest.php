<?php

namespace App\Http\Requests\Notification;

use Illuminate\Foundation\Http\FormRequest;

class MarkNotificationsReadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'all' => ['sometimes', 'boolean'],
            'ids' => ['sometimes', 'array'],
            'ids.*' => ['uuid'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->boolean('all')) {
                return;
            }

            $ids = $this->input('ids');
            if (! is_array($ids) || $ids === []) {
                $validator->errors()->add('ids', 'Provide a non-empty ids array or set all to true.');
            }
        });
    }
}
