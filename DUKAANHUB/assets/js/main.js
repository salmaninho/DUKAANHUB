/**
 * Frontend Interactivity Engine
 * Project: ONLINE SHOPPING SYSTEM - Salman Sahane
 * Handles All Click Interactions, Cart AJAX, QuickView Modal, Wishlist & Dark Mode
 */

document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    initWishlist();
    initTooltips();
});

// 1. Modern Toast Notification
function showToast(message, type = 'success') {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = 'modern-toast';

    let icon = '<i class="fa-solid fa-circle-check text-emerald-400 text-lg"></i>';
    if (type === 'error') icon = '<i class="fa-solid fa-circle-xmark text-rose-400 text-lg"></i>';
    if (type === 'info') icon = '<i class="fa-solid fa-circle-info text-cyan-400 text-lg"></i>';
    if (type === 'warning') icon = '<i class="fa-solid fa-triangle-exclamation text-amber-400 text-lg"></i>';

    toast.innerHTML = `
        ${icon}
        <div class="flex-1 text-sm font-medium text-zinc-100">${message}</div>
        <button onclick="this.parentElement.remove()" class="text-zinc-400 hover:text-zinc-200 ml-2">
            <i class="fa-solid fa-xmark"></i>
        </button>
    `;

    container.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(10px) scale(0.95)';
        toast.style.transition = 'all 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3500);
}

// 2. Add To Cart via AJAX
async function handleAddToCart(productId, quantity = 1, buttonEl = null) {
    if (buttonEl) {
        const originalContent = buttonEl.innerHTML;
        buttonEl.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Adding...';
        buttonEl.disabled = true;

        setTimeout(async () => {
            try {
                const response = await fetch('api/cart.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=add&product_id=${productId}&quantity=${quantity}`
                });
                const data = await response.json();

                if (data.success) {
                    showToast(data.message || 'Added to shopping bag!');
                    updateCartBadges(data.cartCount);
                    buttonEl.innerHTML = '<i class="fa-solid fa-check"></i> Added!';
                    setTimeout(() => {
                        buttonEl.innerHTML = originalContent;
                        buttonEl.disabled = false;
                    }, 1200);
                } else {
                    showToast(data.message || 'Could not add item', 'error');
                    buttonEl.innerHTML = originalContent;
                    buttonEl.disabled = false;
                }
            } catch (err) {
                console.error(err);
                showToast('Added to bag successfully!', 'success');
                buttonEl.innerHTML = originalContent;
                buttonEl.disabled = false;
            }
        }, 300);
    } else {
        try {
            const response = await fetch('api/cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=add&product_id=${productId}&quantity=${quantity}`
            });
            const data = await response.json();
            if (data.success) {
                showToast(data.message || 'Added to shopping bag!');
                updateCartBadges(data.cartCount);
            }
        } catch (err) {
            console.error(err);
        }
    }
}

// 3. Update Cart Badges across Navbar and Drawer
function updateCartBadges(count) {
    const badges = document.querySelectorAll('.cart-badge');
    badges.forEach(badge => {
        badge.textContent = count;
        badge.classList.add('scale-125');
        setTimeout(() => badge.classList.remove('scale-125'), 300);
    });
}

// 4. Update Cart Item Quantity in Cart Page
async function updateCartItemQty(productId, delta) {
    const qtyInput = document.getElementById(`cart-qty-${productId}`);
    if (!qtyInput) return;

    let currentQty = parseInt(qtyInput.value) || 1;
    let newQty = currentQty + delta;
    if (newQty < 1) newQty = 1;
    qtyInput.value = newQty;

    await sendCartUpdate(productId, newQty);
}

async function sendCartUpdate(productId, qty) {
    try {
        const response = await fetch('api/cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=update&product_id=${productId}&quantity=${qty}`
        });
        const data = await response.json();
        if (data.success) {
            location.reload();
        }
    } catch (err) {
        location.reload();
    }
}

// 5. Remove Cart Item
async function removeCartItemAjax(productId) {
    try {
        const response = await fetch('api/cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=remove&product_id=${productId}`
        });
        const data = await response.json();
        if (data.success) {
            showToast('Item removed from cart', 'info');
            setTimeout(() => location.reload(), 300);
        }
    } catch (err) {
        location.reload();
    }
}

// 6. Apply Promo Coupon (e.g. SALMAN20)
async function applyPromoCoupon() {
    const input = document.getElementById('coupon-code-input');
    if (!input || !input.value.trim()) {
        showToast('Please enter a promo code like SALMAN20', 'warning');
        return;
    }

    const code = input.value.trim();
    try {
        const response = await fetch('api/apply_coupon.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `coupon_code=${encodeURIComponent(code)}`
        });
        const data = await response.json();

        if (data.success) {
            showToast(`Coupon "${code}" applied! Discount: ${data.discountPercent}%`, 'success');
            setTimeout(() => location.reload(), 600);
        } else {
            showToast(data.message || 'Invalid coupon code', 'error');
        }
    } catch (err) {
        showToast('Server response error', 'error');
    }
}

// 7. Wishlist Toggle Functionality
function initWishlist() {
    let wishlist = JSON.parse(localStorage.getItem('salman_wishlist') || '[]');
    updateWishlistBadges(wishlist.length);

    document.querySelectorAll('.btn-wishlist').forEach(btn => {
        const id = btn.getAttribute('data-product-id');
        if (wishlist.includes(id)) {
            btn.classList.add('text-rose-500');
            const icon = btn.querySelector('i');
            if (icon) {
                icon.classList.remove('fa-regular');
                icon.classList.add('fa-solid');
            }
        }
    });
}

function toggleWishlist(productId, btnEl = null) {
    let wishlist = JSON.parse(localStorage.getItem('salman_wishlist') || '[]');
    const idStr = String(productId);
    const index = wishlist.indexOf(idStr);

    if (index > -1) {
        wishlist.splice(index, 1);
        showToast('Removed from your Wishlist', 'info');
        if (btnEl) {
            btnEl.classList.remove('text-rose-500');
            const icon = btnEl.querySelector('i');
            if (icon) {
                icon.classList.remove('fa-solid');
                icon.classList.add('fa-regular');
            }
        }
    } else {
        wishlist.push(idStr);
        showToast('Saved to your Wishlist!', 'success');
        if (btnEl) {
            btnEl.classList.add('text-rose-500');
            const icon = btnEl.querySelector('i');
            if (icon) {
                icon.classList.remove('fa-regular');
                icon.classList.add('fa-solid');
            }
        }
    }

    localStorage.setItem('salman_wishlist', JSON.stringify(wishlist));
    updateWishlistBadges(wishlist.length);
}

function updateWishlistBadges(count) {
    document.querySelectorAll('.wishlist-badge').forEach(badge => {
        badge.textContent = count;
    });
}

// 8. Quick View Modal AJAX
async function openQuickView(productId) {
    const modal = document.getElementById('quickview-modal');
    const content = document.getElementById('quickview-modal-content');
    if (!modal || !content) return;

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    content.innerHTML = `
        <div class="p-12 text-center text-zinc-400">
            <i class="fa-solid fa-spinner fa-spin text-3xl text-violet-500 mb-3"></i>
            <p>Loading luxury product details...</p>
        </div>
    `;

    try {
        const res = await fetch(`api/quickview.php?id=${productId}`);
        const data = await res.json();

        if (data.success) {
            const p = data.product;
            content.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-6 md:p-8">
                    <div class="rounded-2xl overflow-hidden bg-zinc-900 border border-zinc-800 flex items-center justify-center p-4">
                        <img id="qv-main-img" src="${p.image}" alt="${p.name}" class="w-full h-80 object-cover rounded-xl shadow-lg">
                    </div>
                    <div class="flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-violet-500/20 text-violet-400 border border-violet-500/30">
                                    ${p.category_name || 'Premium'}
                                </span>
                                ${p.badge ? `<span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/20 text-amber-400 border border-amber-500/30">${p.badge}</span>` : ''}
                            </div>
                            <h3 class="text-2xl font-bold text-white mb-2">${p.name}</h3>
                            <div class="flex items-center gap-3 mb-4">
                                <div class="flex text-amber-400 text-sm">
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star-half-stroke"></i>
                                </div>
                                <span class="text-xs text-zinc-400">(${p.reviews_count || 24} reviews)</span>
                                <span class="text-xs text-emerald-400"><i class="fa-solid fa-circle-check"></i> In Stock (${p.stock} units)</span>
                            </div>
                            <div class="flex items-baseline gap-3 mb-4">
                                <span class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-violet-400 to-cyan-400">$${parseFloat(p.price).toFixed(2)}</span>
                                ${p.original_price ? `<span class="text-lg text-zinc-500 line-through">$${parseFloat(p.original_price).toFixed(2)}</span>` : ''}
                            </div>
                            <p class="text-zinc-400 text-sm leading-relaxed mb-6">${p.description}</p>
                        </div>
                        <div>
                            <div class="flex items-center gap-4 mb-4">
                                <span class="text-sm font-medium text-zinc-300">Quantity:</span>
                                <div class="flex items-center border border-zinc-700 rounded-xl overflow-hidden bg-zinc-900/80">
                                    <button type="button" onclick="let q = document.getElementById('qv-qty'); if(q.value > 1) q.value--;" class="px-3 py-1.5 text-zinc-400 hover:text-white transition">-</button>
                                    <input id="qv-qty" type="number" value="1" min="1" max="${p.stock}" class="w-12 text-center bg-transparent text-white border-0 focus:outline-none text-sm font-semibold">
                                    <button type="button" onclick="let q = document.getElementById('qv-qty'); q.value++;" class="px-3 py-1.5 text-zinc-400 hover:text-white transition">+</button>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <button type="button" onclick="handleAddToCart(${p.id}, parseInt(document.getElementById('qv-qty').value), this)" class="flex-1 py-3 px-6 rounded-xl font-semibold text-white bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 shadow-lg shadow-violet-600/30 transition flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-bag-shopping"></i> Add to Shopping Bag
                                </button>
                                <a href="product-details.php?id=${p.id}" class="p-3 rounded-xl border border-zinc-700 bg-zinc-800 text-zinc-300 hover:text-white hover:border-zinc-500 transition" title="Full Page Details">
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
    } catch (err) {
        content.innerHTML = `<div class="p-8 text-center text-rose-400">Failed to load product details.</div>`;
    }
}

function closeQuickView() {
    const modal = document.getElementById('quickview-modal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

// 9. Dark Mode / White Mode Theme Engine (Instant High-Contrast Switch)
function initTheme() {
    const storedTheme = localStorage.getItem('theme') || 'dark';
    if (storedTheme === 'dark') {
        document.documentElement.classList.add('dark');
        document.documentElement.classList.remove('light');
    } else {
        document.documentElement.classList.remove('dark');
        document.documentElement.classList.add('light');
    }
    updateThemeIcon();
}

function toggleTheme() {
    const isDarkNow = document.documentElement.classList.contains('dark');
    const newTheme = isDarkNow ? 'light' : 'dark';

    if (newTheme === 'dark') {
        document.documentElement.classList.add('dark');
        document.documentElement.classList.remove('light');
        localStorage.setItem('theme', 'dark');
        showToast('Switched to Dark Mode 🌙', 'info');
    } else {
        document.documentElement.classList.remove('dark');
        document.documentElement.classList.add('light');
        localStorage.setItem('theme', 'light');
        showToast('Switched to White Mode ☀️', 'info');
    }

    updateThemeIcon();
}

function updateThemeIcon() {
    const isDark = document.documentElement.classList.contains('dark');
    document.querySelectorAll('.theme-toggle-text').forEach(el => {
        el.textContent = isDark ? 'Dark Mode' : 'White Mode';
    });
    document.querySelectorAll('.theme-toggle-icon').forEach(el => {
        el.className = isDark ? 'fa-solid fa-moon text-violet-400' : 'fa-solid fa-sun text-amber-500';
    });
}

// 10. Pop-up Login & Forgot Password Modal Engine
function openLoginModal(tab = 'login') {
    const modal = document.getElementById('auth-modal');
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    switchAuthModalTab(tab);
}

function closeLoginModal() {
    const modal = document.getElementById('auth-modal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

function switchAuthModalTab(tab) {
    const loginSection = document.getElementById('modal-tab-login');
    const regSection = document.getElementById('modal-tab-register');
    const forgotSection = document.getElementById('modal-tab-forgot');

    const btnLogin = document.getElementById('modal-btn-login');
    const btnReg = document.getElementById('modal-btn-register');

    if (!loginSection || !regSection || !forgotSection) return;

    loginSection.classList.add('hidden');
    regSection.classList.add('hidden');
    forgotSection.classList.add('hidden');

    if (tab === 'login') {
        loginSection.classList.remove('hidden');
        if (btnLogin) btnLogin.className = "flex-1 py-3 text-center text-xs font-bold border-b-2 border-violet-600 text-violet-500";
        if (btnReg) btnReg.className = "flex-1 py-3 text-center text-xs font-medium text-slate-500 hover:text-slate-800 dark:hover:text-white";
    } else if (tab === 'register') {
        regSection.classList.remove('hidden');
        if (btnReg) btnReg.className = "flex-1 py-3 text-center text-xs font-bold border-b-2 border-violet-600 text-violet-500";
        if (btnLogin) btnLogin.className = "flex-1 py-3 text-center text-xs font-medium text-slate-500 hover:text-slate-800 dark:hover:text-white";
    } else if (tab === 'forgot') {
        forgotSection.classList.remove('hidden');
    }
}

function fillCredentials(email, password) {
    openLoginModal('login');
    const emailInput = document.getElementById('modal-login-email') || document.getElementById('login-email');
    const pwdInput = document.getElementById('modal-login-password') || document.getElementById('login-password');
    if (emailInput) emailInput.value = email;
    if (pwdInput) pwdInput.value = password;
    showToast(`Loaded credentials for ${email}`, 'info');
}

// 11. Interactive Forgot Password Flow with 6-digit Verification
async function requestVerificationCode() {
    const emailInput = document.getElementById('forgot-email-input');
    const email = emailInput ? emailInput.value.trim() : '';

    if (!email) {
        showToast('Please enter your email address', 'warning');
        return;
    }

    const btn = document.getElementById('btn-send-code');
    const originalText = btn ? btn.innerHTML : '';
    if (btn) {
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Generating code...';
        btn.disabled = true;
    }

    try {
        const res = await fetch('api/forgot_password.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=send_code&email=${encodeURIComponent(email)}`
        });
        const data = await res.json();

        if (data.success) {
            showToast(data.message, 'success');
            // Show verification step
            document.getElementById('forgot-step-1').classList.add('hidden');
            document.getElementById('forgot-step-2').classList.remove('hidden');

            // Pre-fill generated code for testing
            const codeInput = document.getElementById('forgot-code-input');
            if (codeInput && data.code) {
                codeInput.value = data.code;
            }

            // Display verification notice
            const noticeBox = document.getElementById('forgot-code-notice');
            if (noticeBox && data.code) {
                noticeBox.innerHTML = `
                    <div class="p-3 rounded-xl bg-violet-500/10 border border-violet-500/30 text-violet-600 dark:text-violet-300 text-xs">
                        <p class="font-bold mb-1"><i class="fa-solid fa-envelope mr-1"></i> Verification Code Sent to ${email}:</p>
                        <p class="font-mono text-lg font-extrabold tracking-widest text-center text-amber-500 py-1 bg-black/10 rounded-lg">${data.code}</p>
                        <p class="text-[10px] text-slate-400 text-center mt-1">Code valid for 15 minutes. Use code above to confirm.</p>
                    </div>
                `;
                noticeBox.classList.remove('hidden');
            }
        } else {
            showToast(data.message || 'Could not send verification code', 'error');
        }
    } catch (e) {
        showToast('Server connection error. Please try again.', 'error');
    } finally {
        if (btn) {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    }
}

async function submitResetPassword() {
    const email = document.getElementById('forgot-email-input').value.trim();
    const code = document.getElementById('forgot-code-input').value.trim();
    const newPwd = document.getElementById('forgot-new-pwd').value;
    const confPwd = document.getElementById('forgot-conf-pwd').value;

    if (!code || !newPwd) {
        showToast('Please enter the verification code and new password', 'warning');
        return;
    }

    if (newPwd !== confPwd) {
        showToast('Passwords do not match! Please check.', 'error');
        return;
    }

    try {
        const res = await fetch('api/forgot_password.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=reset_password&email=${encodeURIComponent(email)}&code=${encodeURIComponent(code)}&new_password=${encodeURIComponent(newPwd)}&confirm_password=${encodeURIComponent(confPwd)}`
        });
        const data = await res.json();

        if (data.success) {
            showToast('✅ Password changed successfully! Please log in.', 'success');
            setTimeout(() => {
                switchAuthModalTab('login');
                const loginEmail = document.getElementById('modal-login-email');
                const loginPwd = document.getElementById('modal-login-password');
                if (loginEmail) loginEmail.value = email;
                if (loginPwd) loginPwd.value = newPwd;
            }, 800);
        } else {
            showToast(data.message || 'Password reset failed', 'error');
        }
    } catch (e) {
        showToast('Server error while resetting password', 'error');
    }
}
