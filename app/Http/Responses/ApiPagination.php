<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApiPagination
{
    /**
     * @param  class-string<JsonResource>  $resourceClass
     */
    public static function json(LengthAwarePaginator $paginator, string $resourceClass, Request $request): JsonResponse
    {
        $data = $paginator->getCollection()
            ->map(function ($item) use ($resourceClass, $request) {
                return (new $resourceClass($item))->resolve($request);
            })
            ->values()
            ->all();

        return response()->json([
            'data' => $data,
            'meta' => [
                'totalItems' => $paginator->total(),
                'itemsPerPage' => $paginator->perPage(),
                'currentPage' => $paginator->currentPage(),
                'totalPages' => $paginator->lastPage(),
            ],
        ]);
    }
}
