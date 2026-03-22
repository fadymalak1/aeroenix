<?php

namespace App\Models;

use App\Enums\PageStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'path',
        'content',
        'status',
        'last_modified',
        'meta_title',
        'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'last_modified' => 'datetime',
            'status' => PageStatus::class,
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', PageStatus::Published);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if ($term === null || $term === '') {
            return $query;
        }

        $like = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $term).'%';

        return $query->where(function (Builder $q) use ($like) {
            $q->where('title', 'like', $like)
                ->orWhere('slug', 'like', $like)
                ->orWhere('path', 'like', $like);
        });
    }
}
