# Data Model: TechBits eCommerce Application

**Branch**: `001-techbits-ecommerce-spec` | **Date**: 2026-05-31

All tables use MariaDB. All monetary values stored as integers (LKR, whole rupees). All
timestamps stored in UTC; displayed in Asia/Colombo (UTC+5:30).

---

## Entity Relationship Overview

```
users ──< otps
users ──< cart_items >── products
users ──< addresses
users ──< orders ──< order_items
                  ──< order_status_logs
categories ──< products ──< product_images
                         ──< product_specs
contact_messages (standalone)
sessions (Laravel managed)
password_reset_tokens (standalone, by email)
```

---

## Table Definitions

### `users`

| Column | Type | Constraints | Notes |
|---|---|---|---|
| id | bigint unsigned | PK, AUTO_INCREMENT | |
| name | varchar(100) | NOT NULL | 2–100 chars |
| email | varchar(254) | NOT NULL, UNIQUE | case-insensitive compare; stored lowercase |
| phone | varchar(20) | NOT NULL | normalized +94XXXXXXXXX |
| password | varchar(255) | NOT NULL | bcrypt hash |
| role | enum('customer','admin') | NOT NULL, DEFAULT 'customer' | |
| status | enum('unverified','active','inactive') | NOT NULL, DEFAULT 'unverified' | customer only; admin treated as always active |
| failed_login_attempts | tinyint unsigned | NOT NULL, DEFAULT 0 | credential failures; resets on success |
| locked_until | timestamp | NULL | null = not locked |
| remember_token | varchar(100) | NULL | Laravel remember-me |
| created_at | timestamp | NULL | |
| updated_at | timestamp | NULL | |

**Eloquent Model**: `App\Models\User`
**Business rules**: email stored/compared case-insensitively; one account per email (including Inactive/Unverified); status=unverified expires after 24h (cleanup); admin has no self-registration.

---

### `sessions`

Laravel managed — created by `php artisan session:table`. Standard schema:

| Column | Type |
|---|---|
| id | varchar(255) PK |
| user_id | bigint unsigned NULL |
| ip_address | varchar(45) NULL |
| user_agent | text NULL |
| payload | longtext NOT NULL |
| last_activity | int NOT NULL |

---

### `categories`

| Column | Type | Constraints | Notes |
|---|---|---|---|
| id | bigint unsigned | PK, AUTO_INCREMENT | |
| name | varchar(50) | NOT NULL, UNIQUE | case-insensitive unique enforced in app |
| created_at | timestamp | NULL | |
| updated_at | timestamp | NULL | |

**Eloquent Model**: `App\Models\Category`
**Business rules**: 2–50 chars; unique case-insensitively (enforced at app level + DB unique on lowercased name or via validation); cannot delete while products exist; renaming propagates display only (past order snapshots unaffected).

---

### `products`

| Column | Type | Constraints | Notes |
|---|---|---|---|
| id | bigint unsigned | PK, AUTO_INCREMENT | |
| category_id | bigint unsigned | NOT NULL, FK → categories.id | |
| name | varchar(200) | NOT NULL | 3–200 chars |
| sku | varchar(50) | NOT NULL, UNIQUE | 3–50; alphanumeric + dash/underscore; unique incl. soft-deleted |
| short_description | varchar(200) | NOT NULL | 10–200 chars |
| description | text | NOT NULL | 10–5000 chars; plain text |
| price | int unsigned | NOT NULL | LKR, 1–9,999,999 |
| stock | int unsigned | NOT NULL, DEFAULT 0 | 0–99,999 |
| is_featured | tinyint(1) | NOT NULL, DEFAULT 0 | boolean |
| is_active | tinyint(1) | NOT NULL, DEFAULT 1 | boolean |
| deleted_at | timestamp | NULL | SoftDeletes — null = not deleted |
| created_at | timestamp | NULL | |
| updated_at | timestamp | NULL | |

**Eloquent Model**: `App\Models\Product` (uses `SoftDeletes`)
**Business rules**: Soft delete used to preserve SKU uniqueness constraint after deletion. Storefront queries always use default scope (excludes soft-deleted). Admin shows only non-deleted products. Cart logic uses `withTrashed()` to detect deleted product references. ≥1 image required before `is_active` can be true.

**State transitions**:
- Active (is_active=1, deleted_at=null) → visible on storefront
- Inactive (is_active=0, deleted_at=null) → hidden from storefront, accessible to admin
- Deleted (deleted_at IS NOT NULL) → hidden everywhere, SKU reserved

---

### `product_images`

| Column | Type | Constraints | Notes |
|---|---|---|---|
| id | bigint unsigned | PK, AUTO_INCREMENT | |
| product_id | bigint unsigned | NOT NULL, FK → products.id ON DELETE CASCADE | |
| path | varchar(255) | NOT NULL | relative to storage/app/public |
| is_primary | tinyint(1) | NOT NULL, DEFAULT 0 | only one primary per product (app-enforced) |
| sort_order | tinyint unsigned | NOT NULL, DEFAULT 0 | display order |
| created_at | timestamp | NULL | |
| updated_at | timestamp | NULL | |

**Eloquent Model**: `App\Models\ProductImage`
**Business rules**: 1–5 images per product; JPG/JPEG/PNG/WebP; ≤2MB each; exactly one primary per product (enforced in controller: clear all, set chosen).

---

### `product_specs`

| Column | Type | Constraints | Notes |
|---|---|---|---|
| id | bigint unsigned | PK, AUTO_INCREMENT | |
| product_id | bigint unsigned | NOT NULL, FK → products.id ON DELETE CASCADE | |
| spec_key | varchar(50) | NOT NULL | 1–50 chars |
| spec_value | varchar(200) | NOT NULL | 1–200 chars |
| sort_order | tinyint unsigned | NOT NULL, DEFAULT 0 | |

**Eloquent Model**: `App\Models\ProductSpec`
**Business rules**: 0–30 pairs per product; managed as a replace-all set on product save (delete all then re-insert).

---

### `cart_items`

Stores the customer (authenticated) cart only. Guest cart is stored in the Laravel session.

| Column | Type | Constraints | Notes |
|---|---|---|---|
| id | bigint unsigned | PK, AUTO_INCREMENT | |
| user_id | bigint unsigned | NOT NULL, FK → users.id ON DELETE CASCADE | |
| product_id | bigint unsigned | NOT NULL, FK → products.id ON DELETE CASCADE | |
| quantity | smallint unsigned | NOT NULL | 1 to min(10, stock) |
| created_at | timestamp | NULL | |
| updated_at | timestamp | NULL | |

**Unique index**: `(user_id, product_id)` — one line per product per customer.

**Eloquent Model**: `App\Models\CartItem`
**Business rules**: Max 50 unique lines per customer. Quantity capped at min(10, current stock) on every cart page load and before insert/update. Cart page proactively warns + caps if stock has dropped below cart qty.

---

### `addresses`

| Column | Type | Constraints | Notes |
|---|---|---|---|
| id | bigint unsigned | PK, AUTO_INCREMENT | |
| user_id | bigint unsigned | NOT NULL, FK → users.id ON DELETE CASCADE | |
| label | varchar(30) | NULL | optional, 1–30 chars |
| recipient | varchar(100) | NOT NULL | 2–100 chars |
| line1 | varchar(200) | NOT NULL | 3–200 chars |
| line2 | varchar(200) | NULL | 0–200 chars optional |
| city | varchar(50) | NOT NULL | 2–50 chars |
| district | varchar(50) | NOT NULL | one of 25 Sri Lankan districts |
| postal_code | varchar(5) | NOT NULL | exactly 5 digits |
| phone | varchar(20) | NOT NULL | normalized +94XXXXXXXXX |
| is_default | tinyint(1) | NOT NULL, DEFAULT 0 | exactly one default per customer (app-enforced) |
| created_at | timestamp | NULL | |
| updated_at | timestamp | NULL | |

**Eloquent Model**: `App\Models\Address`
**Business rules**: Max 10 per customer. First address auto-default. Deleting default auto-promotes most recently added remaining. Inline checkout add → permanently saved here, subject to 10-limit.

---

### `orders`

| Column | Type | Constraints | Notes |
|---|---|---|---|
| id | bigint unsigned | PK, AUTO_INCREMENT | |
| user_id | bigint unsigned | NOT NULL, FK → users.id | no cascade — orders preserved if customer deactivated |
| order_number | varchar(12) | NOT NULL, UNIQUE | TB-NNNNNN; set post-insert using order.id |
| status | enum('pending','processing','shipped','delivered','cancelled') | NOT NULL, DEFAULT 'pending' | |
| subtotal | int unsigned | NOT NULL | LKR |
| shipping_fee | int unsigned | NOT NULL, DEFAULT 500 | LKR flat |
| total | int unsigned | NOT NULL | subtotal + shipping_fee |
| shipping_address | json | NOT NULL | full address snapshot at placement |
| payment_cardholder | varchar(100) | NOT NULL | |
| payment_last4 | varchar(4) | NOT NULL | last 4 digits of card |
| payment_expiry | varchar(7) | NOT NULL | MM/YY |
| placed_at | timestamp | NOT NULL | |
| created_at | timestamp | NULL | |
| updated_at | timestamp | NULL | |

**Eloquent Model**: `App\Models\Order`
**Business rules**: Status transitions: pending→processing→shipped→delivered; any non-delivered→cancelled. No backward transitions. `shipping_address` JSON snapshot preserves address even if later edited/deleted by customer.

**State transitions**:
```
pending ──→ processing ──→ shipped ──→ delivered
   │              │            │
   └──────────────┴────────────┴──→ cancelled (auto-restores stock)
```

---

### `order_items`

| Column | Type | Constraints | Notes |
|---|---|---|---|
| id | bigint unsigned | PK, AUTO_INCREMENT | |
| order_id | bigint unsigned | NOT NULL, FK → orders.id ON DELETE CASCADE | |
| product_id | bigint unsigned | NULL | reference only; nullable if product deleted |
| product_name | varchar(200) | NOT NULL | snapshot |
| product_sku | varchar(50) | NOT NULL | snapshot |
| unit_price | int unsigned | NOT NULL | LKR snapshot |
| quantity | smallint unsigned | NOT NULL | |
| line_total | int unsigned | NOT NULL | unit_price × quantity |
| product_image_path | varchar(255) | NULL | primary image path snapshot |

**Eloquent Model**: `App\Models\OrderItem`
**Business rules**: Snapshot data never changes after order placement. `product_id` is nullable to survive hard-reference scenarios, but actual display data comes from snapshot columns.

---

### `order_status_logs`

| Column | Type | Constraints | Notes |
|---|---|---|---|
| id | bigint unsigned | PK, AUTO_INCREMENT | |
| order_id | bigint unsigned | NOT NULL, FK → orders.id ON DELETE CASCADE | |
| from_status | varchar(20) | NULL | null for initial 'pending' entry |
| to_status | varchar(20) | NOT NULL | |
| created_at | timestamp | NOT NULL | timestamp of the status change |

**Eloquent Model**: `App\Models\OrderStatusLog`
**Business rules**: Append-only. One entry per status change including initial placement (from=null, to=pending). Displayed as timeline in A8.

---

### `otps`

| Column | Type | Constraints | Notes |
|---|---|---|---|
| id | bigint unsigned | PK, AUTO_INCREMENT | |
| user_id | bigint unsigned | NOT NULL, FK → users.id ON DELETE CASCADE | |
| code | varchar(64) | NOT NULL | SHA-256 hash of the 6-digit code |
| purpose | enum('registration','login') | NOT NULL | |
| expires_at | timestamp | NOT NULL | now() + 10 minutes |
| attempts | tinyint unsigned | NOT NULL, DEFAULT 0 | incremented on wrong entry; invalidated at 5 |
| invalidated_at | timestamp | NULL | set when OTP is invalidated (used/expired/resent/screen-closed) |
| created_at | timestamp | NOT NULL | |

**Eloquent Model**: `App\Models\Otp`
**Business rules**: On resend or new login attempt, prior OTP for same user+purpose gets `invalidated_at=now()`. Resend allowed only if `created_at < now() - 60s`. Closing the login OTP screen: controller/middleware sets `invalidated_at` on the pending OTP and clears session pending state.

---

### `password_reset_tokens`

| Column | Type | Constraints | Notes |
|---|---|---|---|
| email | varchar(254) | PK | normalized/lowercased |
| token | varchar(255) | NOT NULL | hashed secure-random token |
| created_at | timestamp | NULL | expiry checked: created_at + 1 hour |

**Business rules**: Single record per email (upsert on new request — replaces old token, invalidating it). Single-use: deleted on successful password reset. Only sent to Active accounts.

---

### `contact_messages`

| Column | Type | Constraints | Notes |
|---|---|---|---|
| id | bigint unsigned | PK, AUTO_INCREMENT | |
| sender_name | varchar(100) | NOT NULL | 2–100 chars |
| sender_email | varchar(254) | NOT NULL | |
| subject | varchar(150) | NOT NULL | 3–150 chars |
| message | text | NOT NULL | 10–2000 chars |
| is_read | tinyint(1) | NOT NULL, DEFAULT 0 | |
| created_at | timestamp | NULL | |
| updated_at | timestamp | NULL | |

**Eloquent Model**: `App\Models\ContactMessage`
**Business rules**: No rate limiting on submission. Auto-marked read when admin opens A11. Admin can re-mark unread.

---

## Key Relationships Summary

| From | Relationship | To | Via |
|---|---|---|---|
| User | hasMany | Otp | user_id |
| User | hasMany | CartItem | user_id |
| User | hasMany | Address | user_id |
| User | hasMany | Order | user_id |
| Category | hasMany | Product | category_id |
| Product | hasMany | ProductImage | product_id |
| Product | hasMany | ProductSpec | product_id |
| Product | hasMany | CartItem | product_id |
| Product | hasMany | OrderItem | product_id |
| Order | hasMany | OrderItem | order_id |
| Order | hasMany | OrderStatusLog | order_id |
| Order | belongsTo | User | user_id |

---

## Seeding Plan

| Seeder | Content |
|---|---|
| CategorySeeder | 10 categories from SCOPE.md §8.1 |
| ProductSeeder | ~40 products (3–5/category); ≥5 featured; ≥3 stock=0; ≥3 stock 1–5; ≥1 inactive; ≥5 multi-image; price spread LKR 800–250,000 |
| AdminSeeder | 1 admin user: role=admin, status=active, email=admin@techbits.lk, password (documented in handover, not committed) |
