# Form Contracts: Admin (A1–A11)

All routes under `/tb-backroom-engine/*` prefix. All except A1 guarded by `EnsureAdmin` middleware.

---

## POST /tb-backroom-engine/login (A1)

| Field | Rules |
|---|---|
| email | required, email:rfc, max:254 |
| password | required, string |

**On success**: `Auth::guard('admin')->login($user)` (or use a session flag to mark admin session).
No OTP, no forgot-password, no remember-me.
**On failure**: Generic "Invalid email or password."

---

## POST /tb-backroom-engine/products (A4 — Create Product)
## PUT /tb-backroom-engine/products/{id} (A5 — Edit Product)

| Field | Rules |
|---|---|
| name | required, string, min:3, max:200 |
| sku | required, string, min:3, max:50, regex:/^[a-zA-Z0-9\-_]+$/, unique:products,sku (for create; unique ignore self for edit; includes soft-deleted rows) |
| category_id | required, exists:categories,id |
| short_description | required, string, min:10, max:200 |
| description | required, string, min:10, max:5000 |
| price | required, integer, min:1, max:9999999 |
| stock | required, integer, min:0, max:99999 |
| is_featured | boolean, optional, default:false |
| is_active | boolean, optional, default:false |
| images[] | array, required_if:is_active,true — each: mimes:jpg,jpeg,png,webp, max:2048 (2MB) |
| primary_image_index | integer, optional — index into images[] array |
| specs[N][key] | string, max:50 |
| specs[N][value] | string, max:200 |

**Spec count**: Total spec pairs must be ≤30. Empty key/value pairs are discarded.
**Image rules**: At least 1 image must exist (existing + new uploads combined) if `is_active=true`.
On edit: existing images retained unless explicitly deleted. Deletion via separate DELETE endpoint.
**SKU uniqueness**: `unique:products,sku,{id},id,deleted_at,NULL` — check non-deleted products only
for edit; for create, check all including soft-deleted (no reuse after deletion).

---

## DELETE /tb-backroom-engine/products/{id}/images/{imageId} (Remove product image)

Authorization: imageId must belong to product. Cannot delete if it would leave 0 images and product is_active=true.

---

## POST /tb-backroom-engine/categories (A6 — Create Category)

| Field | Rules |
|---|---|
| name | required, string, min:2, max:50, unique case-insensitive |

---

## PUT /tb-backroom-engine/categories/{id} (A6 — Rename Category)

| Field | Rules |
|---|---|
| name | required, string, min:2, max:50, unique case-insensitive (ignore self) |

---

## DELETE /tb-backroom-engine/categories/{id} (A6)

**Guard**: Category must have 0 products. If products exist → 422 with message "Cannot delete a category that contains products."

---

## POST /tb-backroom-engine/orders/{id}/advance (A8 — Advance Order Status)

No body. Advances status by one step (pending→processing→shipped→delivered).
**Guard**: Order must not be in delivered or cancelled state.
On success: update status, create `order_status_logs` entry, send T5 email.

---

## POST /tb-backroom-engine/orders/{id}/cancel (A8 — Cancel Order)

| Field | Rules |
|---|---|
| confirm | required, boolean, true |

**Guard**: Order must not be in delivered state.
On success: set status=cancelled, restore all line-item stock (`product.stock += order_item.quantity`
for each line where `product_id IS NOT NULL`), create log entry, send T5 Cancelled email.

---

## POST /tb-backroom-engine/customers/{id}/toggle (A9 — Toggle Customer Status)

No body (or confirm=true for deactivation).
Toggles between active↔inactive. Requires confirmation modal on front end for deactivation.
**Guard**: Target must have role=customer.

---

## POST /tb-backroom-engine/messages/{id}/unread (A11 — Mark Message Unread)

No body. Sets `is_read=false` on the message.
