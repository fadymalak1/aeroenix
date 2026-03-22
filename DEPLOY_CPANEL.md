# Deploying aeroenix on cPanel

This guide covers deploying the **aeroenix** Laravel 11 API on a typical shared hosting cPanel server. Adjust paths and domain names to match your account.

## Prerequisites

- **PHP 8.2 or newer** (Laravel 11 requirement).
- **Composer** (via SSH/Terminal, or install locally and upload `vendor/` — SSH is strongly recommended).
- **MySQL** database (created in cPanel).
- **SSH access** (recommended for `composer`, `artisan`, and permissions).

### PHP extensions

In cPanel → **Select PHP Version** → **Extensions**, ensure these are enabled (names may vary slightly by host):

- `openssl`, `pdo`, `pdo_mysql`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, `curl`, `bcmath`, `intl` (optional but useful).

Set a reasonable **`memory_limit`** (e.g. `256M`) and upload limits if you expect large file uploads for projects/services/settings.

---

## 1. Create the database

1. cPanel → **MySQL® Databases**.
2. Create a **database** (e.g. `cpaneluser_aeroenix`).
3. Create a **user** and a **strong password**.
4. **Add user to database** with **ALL PRIVILEGES**.
5. Note: **host** is often `localhost`; some hosts use `127.0.0.1` or a remote hostname — use what cPanel shows for “MySQL host”.

---

## 2. Upload the application

**Option A — Git (recommended)**  
If your host provides Git in cPanel or SSH:

```bash
cd ~/
git clone <your-repo-url> aeroenix
cd aeroenix
```

**Option B — ZIP**  
Zip the project locally (excluding `node_modules` if any, and optionally `vendor` if you will run Composer on the server), upload via **File Manager**, then extract.

---

## 3. Point the domain to Laravel’s `public` folder

The web server must serve only the **`public`** directory, not the project root.

**Recommended:** In cPanel → **Domains** (or **Addon Domains** / **Subdomains**), set the domain’s **document root** to:

```text
/home/USERNAME/aeroenix/public
```

Replace `USERNAME` and path with your actual home path and folder name.

**If you cannot change the document root** (e.g. the site must use `public_html`):

- Either move/copy the **contents** of `public/` into `public_html` and edit `public_html/index.php` so paths point to the real project folder (see Laravel docs “Deploying to shared hosting”), **or**
- Use your host’s “application root” / “Laravel” feature if they offer one.

Incorrect document root (serving the repo root) will expose files and break routing.

---

## 4. Install PHP dependencies (SSH)

From the project root (same folder as `artisan`):

```bash
cd ~/aeroenix
composer install --no-dev --optimize-autoloader
```

If Composer is not global, your host may provide a path such as `php -r "copy(...)"` or `~/bin/composer` — check your host’s documentation.

---

## 5. Environment file

1. Copy `.env.example` to `.env` (if `.env` is not already present):

   ```bash
   cp .env.example .env
   ```

2. Edit `.env` in File Manager or via SSH (`nano .env`). Set at least:

| Variable | Notes |
|----------|--------|
| `APP_NAME` | Your app name |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_URL` | `https://api.yourdomain.com` (your real HTTPS URL) |
| `APP_KEY` | Run `php artisan key:generate` (next step) |
| `DB_*` | Database name, user, password, host from cPanel |
| `CORS_ALLOWED_ORIGINS` | Comma-separated origins allowed to call the API (e.g. your React site `https://app.yourdomain.com`) |
| `SANCTUM_STATEFUL_DOMAINS` | If you use cookie-based SPA auth, include your frontend hostnames |

3. Generate the application key:

   ```bash
   php artisan key:generate
   ```

---

## 6. Database migrations (and seeders if needed)

```bash
php artisan migrate --force
```

If you use seeders in production (only if intended):

```bash
php artisan db:seed --force
```

---

## 7. Storage link and permissions

Link public storage for uploaded files (if your app uses `storage/app/public`):

```bash
php artisan storage:link
```

Set ownership/permissions so the web server user can write logs and uploads (exact user/group depend on the host — often `nobody`, `www`, or your cPanel user):

```bash
chmod -R 775 storage bootstrap/cache
```

If uploads still fail, your host may require `chown` — follow their Laravel permission docs.

---

## 8. Optimize for production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

After changing `.env` or config, clear and rebuild:

```bash
php artisan config:clear
php artisan config:cache
```

---

## 9. HTTPS and `.htaccess`

- Enable **SSL** in cPanel (**Let’s Encrypt** or your certificate).
- Force HTTPS in `.env`: `APP_URL=https://...` and consider middleware or hosting “Force HTTPS Redirect”.

The default `public/.htaccess` is suitable for Apache with `mod_rewrite` enabled. If you see **404** on all routes except `/`, enable **AllowOverride** or ask the host to allow `.htaccess` in your `public` folder.

---

## 10. Cron (optional)

This project does not define a custom schedule in the repository. If you add Laravel’s task scheduler later, add a cPanel cron entry:

```text
* * * * * cd /home/USERNAME/aeroenix && php artisan schedule:run >> /dev/null 2>&1
```

---

## 11. Post-deployment checklist

- [ ] `APP_DEBUG=false` and `APP_ENV=production` on the live server.
- [ ] Database credentials correct; migrations ran successfully.
- [ ] `CORS_ALLOWED_ORIGINS` includes your real frontend URL(s).
- [ ] API base URL matches Postman/collection (e.g. `https://yourdomain.com/api/...`).
- [ ] Test `GET /up` (Laravel health) and a public route such as `GET /api/public/settings`.
- [ ] Authenticated routes work with a Sanctum token after login.

---

## Troubleshooting

| Issue | What to check |
|--------|----------------|
| **500 error** | `storage/logs/laravel.log`, PHP version ≥ 8.2, permissions on `storage` / `bootstrap/cache`. |
| **Composer memory error** | Raise `memory_limit` in **MultiPHP INI Editor** or run `COMPOSER_MEMORY_LIMIT=-1 composer install ...`. |
| **CORS errors from browser** | `CORS_ALLOWED_ORIGINS`, correct `APP_URL`, HTTPS vs HTTP mismatch. |
| **Mixed content** | Use `https` everywhere for `APP_URL` and frontend. |

---

## Summary

1. PHP **8.2+** with required extensions.  
2. MySQL database and user in cPanel.  
3. Document root → **`public`**.  
4. `composer install --no-dev`, `.env`, `key:generate`, `migrate`.  
5. `storage:link`, permissions, `config:cache` / `route:cache`.  
6. **HTTPS**, **CORS**, and **`APP_DEBUG=false`**.

For host-specific Composer paths, PHP binaries, or permission commands, refer to your provider’s “Laravel on cPanel” documentation.
