<?php

namespace App\Http\Controllers\Api;

use App\Enums\MessageStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Message\MarkMessageReadRequest;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Message::class);

        $query = Message::query()->latest('date');

        $query->search($request->query('search'));

        $status = MessageStatus::tryFrom((string) $request->query('status', ''));
        if ($status) {
            $query->where('status', $status);
        }

        $paginator = $this->paginate($query, $request);

        return $this->paginateResponse($paginator, MessageResource::class, $request);
    }

    public function markRead(MarkMessageReadRequest $request, Message $message): MessageResource
    {
        $this->authorize('markRead', $message);

        $message->forceFill(['status' => MessageStatus::Read])->save();

        return new MessageResource($message->fresh());
    }

    public function destroy(Message $message): JsonResponse
    {
        $this->authorize('delete', $message);

        $message->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
