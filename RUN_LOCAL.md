# Run the aeroenix API locally

## Requirements

- PHP 8.2+ with extensions: `openssl`, `pdo_mysql`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, `gd` (for image validation)
- Composer
- MySQL 8+ (or compatible)

## 1. Install dependencies

```bash
cd c:\laragon\www\areonix
composer install
```

## 2. Environment

```bash
copy .env.example .env
php artisan key:generate
```

Edit `.env`:

- `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` for your MySQL database
- `APP_URL` — base URL of this API (e.g. `http://aeroenix.test` or `http://127.0.0.1:8000`)
- `CORS_ALLOWED_ORIGINS` — comma-separated React origins (e.g. `http://localhost:3000,http://localhost:5173`)

Create the MySQL database (empty) before migrating. The default database name in `.env.example` is `aeroenix` (adjust if your local folder or DB name differs).

## 3. Storage link (required for file URLs)

Uploaded files are stored on the `public` disk. Link the storage directory to the web root:

```bash
php artisan storage:link
```

## 4. Migrate and seed

```bash
php artisan migrate --seed
```

Default administrator (change the password in production):

- **Email:** `admin@example.com`
- **Password:** `password`

## 5. Serve the application

**Laragon:** point the virtual host document root to the `public` folder, or use `php artisan serve`:

```bash
php artisan serve
```

API base path: `/api` (e.g. `http://127.0.0.1:8000/api/auth/login`).

## 6. Postman

Import `postman/aeroenix-API.postman_collection.json` and set the `base_url` variable (e.g. `http://127.0.0.1:8000/api`).

After logging in via `POST /auth/login`, copy the `token` into the collection’s `token` variable for authenticated requests.

## 7. React frontend

Send API requests with:

- `Accept: application/json`
- `Authorization: Bearer {token}` for dashboard routes

Ensure your frontend origin is listed in `CORS_ALLOWED_ORIGINS`.

## 8. Dashboard notifications

Notifications use Laravel’s database channel (`notifications` table). Each user only sees their own rows.

- `GET /api/notifications` — paginated list (same `data` + `meta` shape as other list endpoints). Optional query: `unread=1` for unread only.
- `POST /api/notifications/mark-read` — JSON body either `{ "all": true }` to mark everything read, or `{ "ids": ["uuid", ...] }` for specific notification IDs. Requires `Authorization: Bearer {token}`.

To create notifications from your own code (e.g. after an event):

```php
$user->notify(new \App\Notifications\DashboardNotification('Title', 'Body', $optionalActionUrl));
```

After migrating, `php artisan migrate --seed` inserts a sample welcome notification for the default admin (if none exist).

### Real-time updates (optional)

This API does not include WebSockets. For live updates without polling, you can add **Laravel Reverb** (or Pusher/Ably) and broadcast events when notifications are created; the React app would subscribe via **Laravel Echo**. That is an optional follow-up to the REST endpoints above.
