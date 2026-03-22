<?php

namespace App\Http\Controllers\Api;

use App\Enums\ProjectCategory;
use App\Enums\ProjectStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Support\PublicFile;
use App\Support\UniqueSlug;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Project::class);

        $query = Project::query()->latest();

        $query->search($request->query('search'));

        $status = ProjectStatus::tryFrom((string) $request->query('status', ''));
        if ($status) {
            $query->where('status', $status);
        }

        $category = ProjectCategory::tryFrom((string) $request->query('category', ''));
        if ($category) {
            $query->where('category', $category);
        }

        $paginator = $this->paginate($query, $request);

        return $this->paginateResponse($paginator, ProjectResource::class, $request);
    }

    public function show(Project $project): ProjectResource
    {
        $this->authorize('view', $project);

        return new ProjectResource($project);
    }

    public function store(StoreProjectRequest $request): JsonResponse
    {
        $this->authorize('create', Project::class);

        $data = $request->validated();
        unset($data['thumbnail_file']);

        if ($request->hasFile('thumbnail_file')) {
            $data['thumbnail'] = PublicFile::store($request->file('thumbnail_file'), 'projects');
        }

        $slug = $data['slug'] ?? UniqueSlug::make(Project::class, 'slug', $data['title']);

        $status = ProjectStatus::from($data['status']);
        $publishedAt = $data['published_at'] ?? null;
        if ($status === ProjectStatus::Published && $publishedAt === null) {
            $publishedAt = now();
        }

        $project = Project::query()->create([
            'title' => $data['title'],
            'slug' => $slug,
            'client' => $data['client'],
            'category' => ProjectCategory::from($data['category']),
            'description' => $data['description'],
            'thumbnail' => $data['thumbnail'] ?? null,
            'status' => $status,
            'published_at' => $publishedAt,
        ]);

        return (new ProjectResource($project))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateProjectRequest $request, Project $project): ProjectResource
    {
        $this->authorize('update', $project);

        $data = $request->validated();
        unset($data['thumbnail_file']);

        if ($request->hasFile('thumbnail_file')) {
            PublicFile::deleteIfStored($project->thumbnail);
            $data['thumbnail'] = PublicFile::store($request->file('thumbnail_file'), 'projects');
        }

        $title = $data['title'] ?? $project->title;
        $slug = array_key_exists('slug', $data)
            ? $data['slug']
            : ($request->has('title')
                ? UniqueSlug::make(Project::class, 'slug', $title, $project->id)
                : $project->slug);

        $status = isset($data['status']) ? ProjectStatus::from($data['status']) : $project->status;
        $publishedAt = array_key_exists('published_at', $data) ? $data['published_at'] : $project->published_at;

        if ($status === ProjectStatus::Published && $publishedAt === null) {
            $publishedAt = now();
        }

        $project->fill([
            'title' => $title,
            'slug' => $slug,
            'client' => $data['client'] ?? $project->client,
            'category' => isset($data['category']) ? ProjectCategory::from($data['category']) : $project->category,
            'description' => $data['description'] ?? $project->description,
            'thumbnail' => array_key_exists('thumbnail', $data) ? $data['thumbnail'] : $project->thumbnail,
            'status' => $status,
            'published_at' => $publishedAt,
        ]);

        $project->save();

        return new ProjectResource($project->fresh());
    }

    public function destroy(Project $project): JsonResponse
    {
        $this->authorize('delete', $project);

        PublicFile::deleteIfStored($project->thumbnail);
        $project->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
