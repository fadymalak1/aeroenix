<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'last_login',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login' => 'datetime',
            'role' => UserRole::class,
            'status' => UserStatus::class,
        ];
    }

    public function isActive(): bool
    {
        return $this->status === UserStatus::Active;
    }

    public function isViewer(): bool
    {
        return $this->role === UserRole::Viewer;
    }

    public function canManageContent(): bool
    {
        return in_array($this->role, [UserRole::SystemAdministrator, UserRole::ContentEditor], true);
    }

    public function canManageMessages(): bool
    {
        return in_array($this->role, [UserRole::SystemAdministrator, UserRole::SupportAgent], true);
    }

    public function canManageTickets(): bool
    {
        return in_array($this->role, [UserRole::SystemAdministrator, UserRole::SupportAgent], true);
    }

    public function canViewMessagesList(): bool
    {
        return in_array($this->role, [
            UserRole::SystemAdministrator,
            UserRole::SupportAgent,
            UserRole::Viewer,
        ], true);
    }

    public function canViewTicketsList(): bool
    {
        return in_array($this->role, [
            UserRole::SystemAdministrator,
            UserRole::SupportAgent,
            UserRole::Viewer,
        ], true);
    }

    public function canManageUsers(): bool
    {
        return $this->role === UserRole::SystemAdministrator;
    }

    public function canUpdateSettings(): bool
    {
        return $this->role === UserRole::SystemAdministrator;
    }
}
