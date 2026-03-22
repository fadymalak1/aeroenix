<?php

namespace App\Models;

use App\Enums\MessageStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'subject',
        'body',
        'status',
        'date',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'datetime',
            'status' => MessageStatus::class,
        ];
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if ($term === null || $term === '') {
            return $query;
        }

        $like = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $term).'%';

        return $query->where(function (Builder $q) use ($like) {
            $q->where('name', 'like', $like)
                ->orWhere('email', 'like', $like)
                ->orWhere('subject', 'like', $like)
                ->orWhere('body', 'like', $like);
        });
    }
}
