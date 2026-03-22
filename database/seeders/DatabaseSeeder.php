<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\SiteSetting;
use App\Models\User;
use App\Notifications\DashboardNotification;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'System Administrator',
                'password' => 'password',
                'role' => UserRole::SystemAdministrator,
                'status' => UserStatus::Active,
            ]
        );

        if ($admin->notifications()->count() === 0) {
            $admin->notify(new DashboardNotification(
                'Welcome to aeroenix',
                'Your dashboard notifications are connected. Mark them as read from the notification dropdown.',
            ));
        }

        if (! SiteSetting::query()->exists()) {
            SiteSetting::query()->create([
                'company_name' => 'aeroenix',
                'site_title' => 'aeroenix',
                'site_description' => 'Software company',
            ]);
        }
    }
}
