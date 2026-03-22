<?php

namespace App\Enums;

enum UserRole: string
{
    case SystemAdministrator = 'system_administrator';
    case ContentEditor = 'content_editor';
    case SupportAgent = 'support_agent';
    case Viewer = 'viewer';

    public function label(): string
    {
        return match ($this) {
            self::SystemAdministrator => 'System Administrator',
            self::ContentEditor => 'Content Editor',
            self::SupportAgent => 'Support Agent',
            self::Viewer => 'Viewer',
        };
    }
}
