<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiPagination;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

abstract class Controller
{
    use AuthorizesRequests;

    protected function perPage(Request $request): int
    {
        $limit = (int) $request->query('limit', 15);

        return max(1, min($limit, 100));
    }

    protected function paginate(Builder $query, Request $request): LengthAwarePaginator
    {
        return $query->paginate($this->perPage($request))->withQueryString();
    }

    /**
     * @param  class-string<JsonResource>  $resourceClass
     */
    protected function paginateResponse(LengthAwarePaginator $paginator, string $resourceClass, Request $request): JsonResponse
    {
        return ApiPagination::json($paginator, $resourceClass, $request);
    }
}
