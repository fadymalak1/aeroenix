<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UniqueSlug
{
    /**
     * @param  class-string<Model>  $modelClass
     */
    public static function make(string $modelClass, string $column, string $title, ?int $exceptId = null): string
    {
        $base = Str::slug($title) ?: 'item';
        $slug = $base;
        $i = 1;

        while (
            $modelClass::query()
                ->where($column, $slug)
                ->when($exceptId !== null, fn ($q) => $q->where('id', '!=', $exceptId))
                ->exists()
        ) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
