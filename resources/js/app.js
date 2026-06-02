/* TechBits — shared application JavaScript */

/* ── Toast notifications ─────────────────────────────────────────── */
function showToast(msg, type = '') {
    const container = document.getElementById('toastContainer');
    if (!container) return;
    const toast = document.createElement('div');
    toast.className = 'toast' + (type ? ' toast-' + type : '');
    toast.textContent = msg;
    container.appendChild(toast);
    setTimeout(() => toast.remove(), 3500);
}

/* Auto-dismiss flash alert banners after 5 s */
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(el => el.remove());
    }, 5000);
});

/* ── Confirmation modals ─────────────────────────────────────────── */
function openModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.add('active');
}
function closeModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.remove('active');
}
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', e => {
            if (e.target === overlay) overlay.classList.remove('active');
        });
    });
});

/* ── Quantity selector ───────────────────────────────────────────── */
function adjustQty(button, delta, min, max) {
    const row   = button.closest('.qty-selector');
    const input = row ? row.querySelector('input[type="number"]') : null;
    if (!input) return;
    let val = parseInt(input.value, 10) + delta;
    val = Math.max(min ?? 1, Math.min(max ?? 10, val));
    input.value = val;
}

/* ── Cart: add-to-cart AJAX ─────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('addCartForm');
    if (!form) return;

    form.addEventListener('submit', e => {
        e.preventDefault();
        const btn = form.querySelector('button[type="submit"]');
        if (btn) { btn.disabled = true; btn.textContent = 'Adding…'; }

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showToast('Added to cart', 'success');
                document.querySelectorAll('.cart-badge').forEach(b => {
                    b.textContent = data.cart_count;
                    b.style.display = data.cart_count > 0 ? '' : 'none';
                });
            } else {
                showToast(data.message || 'Could not add to cart.', 'error');
            }
        })
        .catch(() => showToast('Something went wrong.', 'error'))
        .finally(() => {
            if (btn) { btn.disabled = false; btn.textContent = 'Add to cart'; }
        });
    });
});

/* ── Admin: dynamic spec key-value rows ─────────────────────────── */
function addSpecRow() {
    const container = document.getElementById('specsContainer');
    if (!container) return;
    if (container.querySelectorAll('.spec-row').length >= 30) {
        showToast('Maximum 30 specifications allowed.', 'error');
        return;
    }
    const row = document.createElement('div');
    row.className = 'spec-row';
    row.style.cssText = 'display:flex;gap:.5rem;margin-bottom:.4rem;';
    row.innerHTML =
        '<input class="form-control" type="text" name="specs[keys][]"   placeholder="Key"   maxlength="50"  style="flex:1;">' +
        '<input class="form-control" type="text" name="specs[values][]" placeholder="Value" maxlength="200" style="flex:2;">' +
        '<button type="button" onclick="this.parentElement.remove()" class="btn btn-sm btn-danger">✕</button>';
    container.appendChild(row);
}

/* ── Product detail: image thumbnail swap ───────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.thumb-img').forEach(thumb => {
        thumb.addEventListener('click', () => {
            const main = document.getElementById('mainImg');
            if (main) main.src = thumb.src;
        });
    });
});

/* ── OTP resend countdown ───────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('resendBtn');
    if (!btn) return;
    let seconds = 60;
    btn.disabled = true;
    btn.textContent = `Resend code (${seconds}s)`;
    const interval = setInterval(() => {
        seconds--;
        if (seconds <= 0) {
            clearInterval(interval);
            btn.disabled = false;
            btn.textContent = 'Resend code';
        } else {
            btn.textContent = `Resend code (${seconds}s)`;
        }
    }, 1000);
});
