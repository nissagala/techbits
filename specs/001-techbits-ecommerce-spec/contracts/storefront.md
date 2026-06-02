# Form Contracts: Storefront (S1–S5, S29)

## GET / (S1 — Home)

No form. Query: Featured products (up to 8, per §10.13 logic), all 10 categories.

---

## GET /category/{category:slug-or-id} (S2)

| Query param | Type | Default | Notes |
|---|---|---|---|
| in_stock | boolean | false | filter out stock=0 |
| sort | string | newest | newest / price_asc / price_desc / name_az |
| page | integer | 1 | 12 per page |

Response: paginated Active products in category, excluding soft-deleted.

---

## GET /search (S3)

| Query param | Type | Validation | Notes |
|---|---|---|---|
| q | string | min:2, max:100 | partial match on name, case-insensitive |
| category | integer | optional, exists:categories | filter by category |
| in_stock | boolean | optional | filter stock > 0 |
| sort | string | newest / price_asc / price_desc / name_az | |
| page | integer | 1 | 12 per page |

If `q` < 2 chars: show short-query hint, no results grid.

---

## POST /cart (Add to cart)

| Field | Rules |
|---|---|
| product_id | required, exists:products,id (active, not soft-deleted) |
| quantity | required, integer, min:1 |

**Guest**: Add/increment in session array; cap at min(10, stock).
**Customer**: Upsert `cart_items`; cap at min(10, stock).
Response: JSON `{success, cart_count, message}` (AJAX); or redirect with toast.

---

## PATCH /cart/{cartItem} (Update quantity)

| Field | Rules |
|---|---|
| quantity | required, integer, min:1, max:min(10, product.stock) |

---

## DELETE /cart/{cartItem} (Remove line)

No body. Authorization: guest owns session line / customer owns DB line.

---

## POST /contact (S29)

| Field | Rules |
|---|---|
| name | required, string, min:2, max:100 — pre-filled + read-only for logged-in users |
| email | required, email:rfc, max:254 — pre-filled + read-only for logged-in users |
| subject | required, string, min:3, max:150 |
| message | required, string, min:10, max:2000 |

**No rate limiting** (intentional per SCOPE.md §10.12).
**On success**: Store in `contact_messages`, send T6 email to admin, redirect with success flash, form cleared.
