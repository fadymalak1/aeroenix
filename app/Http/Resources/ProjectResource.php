<?php

namespace App\Http\Resources;

use App\Support\PublicFile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'client' => $this->client,
            'category' => $this->category->value,
            'description' => $this->description,
            'thumbnail' => PublicFile::url($this->thumbnail),
            'status' => $this->status->value,
            'published_at' => $this->published_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
