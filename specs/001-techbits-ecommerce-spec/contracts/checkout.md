# Form Contracts: Checkout (S20–S23)

All routes guarded by `EnsureCustomer`. Empty cart → redirect to /cart with message.
Checkout state tracked in session: `checkout.address_id`, `checkout.payment`.

---

## POST /checkout/shipping (S20 — Save Shipping Address)

| Field | Rules |
|---|---|
| address_id | required_without:new_address, exists:addresses,id (must belong to Auth::user()) |
| new_address.label | nullable, string, max:30 |
| new_address.recipient | required_with:new_address, string, min:2, max:100 |
| new_address.line1 | required_with:new_address, string, min:3, max:200 |
| new_address.line2 | nullable, string, max:200 |
| new_address.city | required_with:new_address, string, min:2, max:50 |
| new_address.district | required_with:new_address, string, in:25-districts-list |
| new_address.postal_code | required_with:new_address, string, digits:5 |
| new_address.phone | required_with:new_address, string, custom:SriLankanPhone |
| new_address.is_default | boolean, optional |

**If `new_address` fields present**: Save new address to `addresses` table (subject to 10-limit),
use the new address's ID for checkout. Error if customer already has 10 addresses.
**Store in session**: `checkout.address_id = resolved_address_id`.
**Redirect**: → GET /checkout/payment.

---

## POST /checkout/payment (S21 — Save Payment Details)

| Field | Rules |
|---|---|
| cardholder | required, string, min:2, max:100 |
| card_number | required, string — strip spaces, digits:13-19, custom:Luhn check |
| expiry | required, string, regex:/^\d{2}\/\d{2}$/, custom:not-in-past (MM/YY) |
| cvv | required, string, digits_between:3,4 |

**Store in session** (partial, no CVV): `checkout.payment = {last4, cardholder, expiry}`.
CVV is **never stored** — validated and discarded.
**Redirect**: → GET /checkout/review.

---

## POST /checkout/place (S22 — Place Order)

No additional form fields. All data comes from session (`checkout.address_id`, `checkout.payment`).

**Precondition checks** (in order):
1. Cart not empty.
2. All cart lines: product is active (not soft-deleted), sufficient stock.
3. `checkout.address_id` is valid and belongs to Auth::user().
4. `checkout.payment` is present in session.

**On success**:
- Decrement stock for each line item.
- Create `order` row (status=pending).
- Set `order_number = 'TB-' . str_pad($order->id, 6, '0', STR_PAD_LEFT)`.
- Create `order_items` (snapshot: name, sku, unit_price, qty, line_total, primary image path).
- Create `order_status_logs` entry (from=null, to=pending).
- Snapshot `shipping_address` as JSON on the order.
- Clear customer cart (`CartItem::where('user_id',...)->delete()`).
- Clear session checkout state.
- Queue confirmation email T4.
- Redirect → GET /checkout/confirmation/{order}.

**On stock conflict**: No order created. Flash error identifying the problematic line(s). Redirect → /cart.

---

## GET /checkout/confirmation/{order} (S23)

Authorization: order must belong to Auth::user(). Displays order summary read-only.
