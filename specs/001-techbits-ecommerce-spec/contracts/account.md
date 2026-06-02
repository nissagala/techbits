# Form Contracts: Customer Account (S13–S19)

All routes guarded by `EnsureCustomer` middleware (authenticated, role=customer, status=active).

---

## POST /account/profile (S14 — Update Profile)

| Field | Rules |
|---|---|
| name | required, string, min:2, max:100, regex:/^[a-zA-Z\s.\-]+$/ |
| phone | required, string, custom:SriLankanPhone |

Email is read-only — not accepted in this request.

---

## POST /account/password (S15 — Change Password)

| Field | Rules |
|---|---|
| current_password | required, string — must match Auth::user()->password via Hash::check |
| password | required, string, min:8, max:64, regex:/[a-zA-Z]/, regex:/[0-9]/ |
| password_confirmation | required, same:password |

**On success**: Update password, stay logged in, flash success message.

---

## POST /account/addresses (S17 — Add Address)
## PUT /account/addresses/{address} (S17 — Edit Address)

| Field | Rules |
|---|---|
| label | nullable, string, max:30 |
| recipient | required, string, min:2, max:100 |
| line1 | required, string, min:3, max:200 |
| line2 | nullable, string, max:200 |
| city | required, string, min:2, max:50 |
| district | required, string, in:25-districts-list |
| postal_code | required, string, digits:5 |
| phone | required, string, custom:SriLankanPhone |
| is_default | boolean, optional |

**Guard**: Customer must have fewer than 10 addresses for POST (create). For PUT (edit) the address must belong to Auth::user().
**First address**: automatically set as default regardless of `is_default` field.

---

## DELETE /account/addresses/{address}

Authorization: address must belong to Auth::user().
**If deleting default**: auto-promote most recently added remaining address to default.
Requires confirmation modal on front end.

---

## POST /account/addresses/{address}/default

Authorization: address must belong to Auth::user().
Sets target as default, clears `is_default` on all others for this user.
