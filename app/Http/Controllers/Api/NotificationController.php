<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Notification\MarkNotificationsReadRequest;
use App\Http\Resources\NotificationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = $request->user()->notifications()->orderByDesc('created_at');

        if ($request->boolean('unread')) {
            $query->whereNull('read_at');
        }

        $paginator = $query->paginate($this->perPage($request))->withQueryString();

        return $this->paginateResponse($paginator, NotificationResource::class, $request);
    }

    public function markRead(MarkNotificationsReadRequest $request): JsonResponse
    {
        $user = $request->user();

        if ($request->boolean('all')) {
            $user->notifications()->whereNull('read_at')->update(['read_at' => now()]);

            return response()->json([
                'message' => 'All notifications marked as read.',
            ]);
        }

        $ids = $request->input('ids', []);

        $updated = $user->notifications()
            ->whereIn('id', $ids)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'message' => 'Notifications marked as read.',
            'updated_count' => $updated,
        ]);
    }
}
