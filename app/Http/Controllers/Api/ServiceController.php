<?php

namespace App\Http\Controllers\Api;

use App\Enums\ServiceStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Service\StoreServiceRequest;
use App\Http\Requests\Service\UpdateServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use App\Support\PublicFile;
use App\Support\UniqueSlug;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Service::class);

        $query = Service::query()->latest();

        $query->search($request->query('search'));

        $status = ServiceStatus::tryFrom((string) $request->query('status', ''));
        if ($status) {
            $query->where('status', $status);
        }

        $paginator = $this->paginate($query, $request);

        return $this->paginateResponse($paginator, ServiceResource::class, $request);
    }

    public function show(Service $service): ServiceResource
    {
        $this->authorize('view', $service);

        return new ServiceResource($service);
    }

    public function store(StoreServiceRequest $request): JsonResponse
    {
        $this->authorize('create', Service::class);

        $data = $request->validated();
        unset($data['image_file']);

        if ($request->hasFile('image_file')) {
            $data['image'] = PublicFile::store($request->file('image_file'), 'services');
        }

        $slug = $data['slug'] ?? UniqueSlug::make(Service::class, 'slug', $data['title']);

        $service = Service::query()->create([
            'title' => $data['title'],
            'slug' => $slug,
            'description' => $data['description'],
            'price' => $data['price'],
            'image' => $data['image'] ?? null,
            'status' => ServiceStatus::from($data['status']),
        ]);

        return (new ServiceResource($service))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateServiceRequest $request, Service $service): ServiceResource
    {
        $this->authorize('update', $service);

        $data = $request->validated();
        unset($data['image_file']);

        if ($request->hasFile('image_file')) {
            PublicFile::deleteIfStored($service->image);
            $data['image'] = PublicFile::store($request->file('image_file'), 'services');
        }

        $title = $data['title'] ?? $service->title;
        $slug = array_key_exists('slug', $data)
            ? $data['slug']
            : ($request->has('title')
                ? UniqueSlug::make(Service::class, 'slug', $title, $service->id)
                : $service->slug);

        $service->fill([
            'title' => $title,
            'slug' => $slug,
            'description' => $data['description'] ?? $service->description,
            'price' => $data['price'] ?? $service->price,
            'image' => array_key_exists('image', $data) ? $data['image'] : $service->image,
            'status' => isset($data['status']) ? ServiceStatus::from($data['status']) : $service->status,
        ]);

        $service->save();

        return new ServiceResource($service->fresh());
    }

    public function destroy(Service $service): JsonResponse
    {
        $this->authorize('delete', $service);

        PublicFile::deleteIfStored($service->image);
        $service->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
