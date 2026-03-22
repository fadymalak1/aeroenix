<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\User;

class MessagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canViewMessagesList();
    }

    public function view(User $user, Message $message): bool
    {
        return $user->canViewMessagesList();
    }

    public function markRead(User $user, Message $message): bool
    {
        return $user->canManageMessages();
    }

    public function delete(User $user, Message $message): bool
    {
        return $user->canManageMessages();
    }
}
