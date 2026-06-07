# Quickstart: TechBits Development Setup

**Branch**: `001-techbits-ecommerce-spec` | **Date**: 2026-05-31

This guide gets a fresh developer environment running TechBits locally.

---

## Prerequisites

- PHP 8.4+ with extensions: pdo_mysql, mbstring, openssl, tokenizer, xml, ctype, json, bcmath, fileinfo, gd
- Composer 2.x
- MariaDB 10.6+
- A web server (Laravel's built-in `php artisan serve` is sufficient for development)
- An outbound email option (Mailtrap free account recommended, or `MAIL_MAILER=log` to write emails to log file)

---

## 1. Create the Laravel Project

```bash
composer create-project laravel/laravel techbits
cd techbits
```

---

## 2. Configure Environment

Copy `.env.example` to `.env` and set:

```dotenv
APP_NAME=TechBits
APP_URL=http://localhost:8000
APP_TIMEZONE=Asia/Colombo

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=techbits
DB_USERNAME=<your_db_user>
DB_PASSWORD=<your_db_password>

SESSION_DRIVER=database
SESSION_LIFETIME=30

MAIL_MAILER=log          # use 'smtp' + Mailtrap credentials for actual email delivery
MAIL_FROM_ADDRESS=noreply@techbits.lk
MAIL_FROM_NAME=TechBits
```

---

## 3. Create the Database

```sql
CREATE DATABASE techbits CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

## 4. Generate App Key

```bash
php artisan key:generate
```

---

## 5. Run Migrations and Seed

```bash
php artisan migrate
php artisan db:seed
```

This runs: CategorySeeder (10 categories), ProductSeeder (~40 products with images),
AdminSeeder (1 admin account — see handover doc for initial password).

---

## 6. Link Storage

```bash
php artisan storage:link
```

This symlinks `public/storage` → `storage/app/public` so product images are web-accessible.

---

## 7. Start Development Server

```bash
php artisan serve
```

Visit `http://localhost:8000` for the storefront.
Visit `http://localhost:8000/tb-backroom-engine` for the admin panel.

---

## Key Artisan Commands

| Command | Purpose |
|---|---|
| `php artisan migrate:fresh --seed` | Wipe and re-seed the database |
| `php artisan make:migration create_xxx_table` | Create a new migration |
| `php artisan make:model ModelName -m` | Create model + migration |
| `php artisan make:controller XxxController` | Create a controller |
| `php artisan make:request XxxRequest` | Create a Form Request |
| `php artisan make:mail XxxMail --markdown=emails.xxx` | Create a Mailable |
| `php artisan route:list` | List all registered routes |
| `php artisan config:clear` | Clear config cache after .env changes |

---

## Validation Checklist (post-setup)

- [ ] Home page (S1) loads with product grid
- [ ] Register a new customer account → OTP email appears in `storage/logs/laravel.log`
- [ ] Log in with OTP → session established, cart badge visible
- [ ] Add product to cart → badge increments
- [ ] Complete 3-step checkout → order appears in My Orders (S18)
- [ ] Admin login at `/tb-backroom-engine` → dashboard shows counters
- [ ] Admin can add a product, toggle featured, verify it appears on home page
- [ ] Admin can advance an order from Pending → Processing
