<?php

namespace App\Http\Controllers\Api;

use App\Enums\PageStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Page\StorePageRequest;
use App\Http\Requests\Page\UpdatePageRequest;
use App\Http\Resources\PageResource;
use App\Models\Page;
use App\Support\UniqueSlug;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Page::class);

        $query = Page::query()->latest('updated_at');

        $query->search($request->query('search'));

        $status = PageStatus::tryFrom((string) $request->query('status', ''));
        if ($status) {
            $query->where('status', $status);
        }

        $paginator = $this->paginate($query, $request);

        return $this->paginateResponse($paginator, PageResource::class, $request);
    }

    public function show(Page $page): PageResource
    {
        $this->authorize('view', $page);

        return new PageResource($page);
    }

    public function store(StorePageRequest $request): JsonResponse
    {
        $this->authorize('create', Page::class);

        $data = $request->validated();

        $slug = $data['slug'] ?? UniqueSlug::make(Page::class, 'slug', $data['title']);

        $page = Page::query()->create([
            'title' => $data['title'],
            'slug' => $slug,
            'path' => $data['path'],
            'content' => $data['content'],
            'status' => PageStatus::from($data['status']),
            'last_modified' => now(),
            'meta_title' => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
        ]);

        return (new PageResource($page))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdatePageRequest $request, Page $page): PageResource
    {
        $this->authorize('update', $page);

        $data = $request->validated();

        $title = $data['title'] ?? $page->title;
        $slug = array_key_exists('slug', $data)
            ? $data['slug']
            : ($request->has('title')
                ? UniqueSlug::make(Page::class, 'slug', $title, $page->id)
                : $page->slug);

        $content = $data['content'] ?? $page->content;
        $lastModified = $page->last_modified;
        if (array_key_exists('content', $data) && $data['content'] !== $page->content) {
            $lastModified = now();
        }

        $page->fill([
            'title' => $title,
            'slug' => $slug,
            'path' => $data['path'] ?? $page->path,
            'content' => $content,
            'status' => isset($data['status']) ? PageStatus::from($data['status']) : $page->status,
            'last_modified' => $lastModified,
            'meta_title' => $data['meta_title'] ?? $page->meta_title,
            'meta_description' => $data['meta_description'] ?? $page->meta_description,
        ]);

        $page->save();

        return new PageResource($page->fresh());
    }

    public function destroy(Page $page): JsonResponse
    {
        $this->authorize('delete', $page);

        $page->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
