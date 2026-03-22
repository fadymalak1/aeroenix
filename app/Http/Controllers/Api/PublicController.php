<?php

namespace App\Http\Controllers\Api;

use App\Enums\MessageStatus;
use App\Enums\ProjectCategory;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Public\PublicContactRequest;
use App\Http\Requests\Public\PublicSupportRequest;
use App\Http\Resources\MessageResource;
use App\Http\Resources\PageResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\SiteSettingResource;
use App\Http\Resources\TicketResource;
use App\Models\Message;
use App\Models\Page;
use App\Models\Project;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function settings(): SiteSettingResource
    {
        $settings = SiteSetting::query()->first();

        if (! $settings) {
            $settings = SiteSetting::query()->create([
                'company_name' => null,
                'site_title' => null,
                'site_description' => null,
            ]);
        }

        return new SiteSettingResource($settings);
    }

    public function projects(Request $request): JsonResponse
    {
        $query = Project::query()->published()->latest('published_at');

        $query->search($request->query('search'));

        $category = ProjectCategory::tryFrom((string) $request->query('category', ''));
        if ($category) {
            $query->where('category', $category);
        }

        $paginator = $this->paginate($query, $request);

        return $this->paginateResponse($paginator, ProjectResource::class, $request);
    }

    public function projectBySlug(string $slug): ProjectResource
    {
        $project = Project::query()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        return new ProjectResource($project);
    }

    public function services(Request $request): JsonResponse
    {
        $query = Service::query()->active()->latest();

        $query->search($request->query('search'));

        $paginator = $this->paginate($query, $request);

        return $this->paginateResponse($paginator, ServiceResource::class, $request);
    }

    public function serviceBySlug(string $slug): ServiceResource
    {
        $service = Service::query()
            ->active()
            ->where('slug', $slug)
            ->firstOrFail();

        return new ServiceResource($service);
    }

    public function pageBySlug(string $slug): PageResource
    {
        $page = Page::query()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        return new PageResource($page);
    }

    public function contact(PublicContactRequest $request): JsonResponse
    {
        $data = $request->validated();

        $message = Message::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'subject' => $data['subject'],
            'body' => $data['body'],
            'status' => MessageStatus::Unread,
            'date' => now(),
        ]);

        return (new MessageResource($message))
            ->response()
            ->setStatusCode(201);
    }

    public function support(PublicSupportRequest $request): JsonResponse
    {
        $data = $request->validated();

        $priority = isset($data['priority'])
            ? TicketPriority::from($data['priority'])
            : TicketPriority::Medium;

        $ticket = Ticket::query()->create([
            'title' => $data['title'],
            'customer' => $data['customer'],
            'email' => $data['email'],
            'description' => $data['description'],
            'priority' => $priority,
            'status' => TicketStatus::Open,
            'date' => now(),
        ]);

        return (new TicketResource($ticket))
            ->response()
            ->setStatusCode(201);
    }
}
