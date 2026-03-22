<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canViewTicketsList();
    }

    public function view(User $user, Ticket $ticket): bool
    {
        return $user->canViewTicketsList();
    }

    public function update(User $user, Ticket $ticket): bool
    {
        return $user->canManageTickets();
    }

    public function delete(User $user, Ticket $ticket): bool
    {
        return $user->canManageTickets();
    }

    public function resolve(User $user, Ticket $ticket): bool
    {
        return $user->canManageTickets();
    }
}
