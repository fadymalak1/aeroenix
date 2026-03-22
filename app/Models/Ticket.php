<?php

namespace App\Models;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'customer',
        'email',
        'description',
        'priority',
        'status',
        'date',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'datetime',
            'priority' => TicketPriority::class,
            'status' => TicketStatus::class,
        ];
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if ($term === null || $term === '') {
            return $query;
        }

        $like = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $term).'%';

        return $query->where(function (Builder $q) use ($like) {
            $q->where('title', 'like', $like)
                ->orWhere('customer', 'like', $like)
                ->orWhere('email', 'like', $like)
                ->orWhere('description', 'like', $like);
        });
    }
}
