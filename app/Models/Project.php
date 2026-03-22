<?php

namespace App\Models;

use App\Enums\ProjectCategory;
use App\Enums\ProjectStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'client',
        'category',
        'description',
        'thumbnail',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'status' => ProjectStatus::class,
            'category' => ProjectCategory::class,
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', ProjectStatus::Published)
            ->whereNotNull('published_at');
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if ($term === null || $term === '') {
            return $query;
        }

        $like = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $term).'%';

        return $query->where(function (Builder $q) use ($like) {
            $q->where('title', 'like', $like)
                ->orWhere('client', 'like', $like);
        });
    }
}
