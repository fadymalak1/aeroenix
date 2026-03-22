<?php

namespace App\Http\Controllers\Api;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Ticket\ResolveTicketRequest;
use App\Http\Requests\Ticket\UpdateTicketRequest;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Ticket::class);

        $query = Ticket::query()->latest('date');

        $query->search($request->query('search'));

        $priority = TicketPriority::tryFrom((string) $request->query('priority', ''));
        if ($priority) {
            $query->where('priority', $priority);
        }

        $status = TicketStatus::tryFrom((string) $request->query('status', ''));
        if ($status) {
            $query->where('status', $status);
        }

        $paginator = $this->paginate($query, $request);

        return $this->paginateResponse($paginator, TicketResource::class, $request);
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket): TicketResource
    {
        $this->authorize('update', $ticket);

        $data = $request->validated();

        if (array_key_exists('title', $data)) {
            $ticket->title = $data['title'];
        }
        if (array_key_exists('customer', $data)) {
            $ticket->customer = $data['customer'];
        }
        if (array_key_exists('email', $data)) {
            $ticket->email = $data['email'];
        }
        if (array_key_exists('description', $data)) {
            $ticket->description = $data['description'];
        }
        if (array_key_exists('priority', $data)) {
            $ticket->priority = TicketPriority::from($data['priority']);
        }
        if (array_key_exists('status', $data)) {
            $ticket->status = TicketStatus::from($data['status']);
        }
        if (array_key_exists('date', $data)) {
            $ticket->date = $data['date'];
        }

        $ticket->save();

        return new TicketResource($ticket->fresh());
    }

    public function resolve(ResolveTicketRequest $request, Ticket $ticket): TicketResource
    {
        $this->authorize('resolve', $ticket);

        $ticket->forceFill(['status' => TicketStatus::Resolved])->save();

        return new TicketResource($ticket->fresh());
    }

    public function destroy(Ticket $ticket): JsonResponse
    {
        $this->authorize('delete', $ticket);

        $ticket->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
