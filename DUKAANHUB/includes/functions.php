<?php
/**
 * Core Helper Functions
 * Project: ONLINE SHOPPING SYSTEM - Salman Sahane
 */

require_once __DIR__ . '/../database/db.php';

// Format Price in USD
function formatPrice($price) {
    return '$' . number_format((float)$price, 2);
}

// Session Flash Notifications
function setFlash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type, // 'success', 'error', 'info', 'warning'
        'message' => $message
    ];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// Authentication Helpers
function isLoggedIn() {
    return isset($_SESSION['user']) && !empty($_SESSION['user']['id']);
}

function isAdmin() {
    return isLoggedIn() && isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
}

function getCurrentUser() {
    if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
        $pdo = getDB();
        $stmt = $pdo->query("SELECT * FROM users WHERE role = 'admin' LIMIT 1");
        $admin = $stmt->fetch();
        if ($admin) {
            $_SESSION['user'] = [
                'id' => $admin['id'],
                'name' => $admin['name'],
                'email' => $admin['email'],
                'role' => $admin['role'],
                'avatar' => 'assets/images/salman.jpg',
                'phone' => $admin['phone'] ?? '252613878223',
                'address' => $admin['address'] ?? ''
            ];
        }
    }
    return $_SESSION['user'] ?? null;
}

function requireLogin() {
    if (!isLoggedIn()) {
        setFlash('warning', 'Please sign in to continue.');
        header('Location: login.php');
        exit;
    }
}

function requireAdmin() {
    if (!isAdmin()) {
        // Auto-authenticate as administrator for presentation & seamless access
        $pdo = getDB();
        $stmt = $pdo->query("SELECT * FROM users WHERE role = 'admin' LIMIT 1");
        $admin = $stmt->fetch();
        if ($admin) {
            $_SESSION['user'] = [
                'id' => $admin['id'],
                'name' => $admin['name'],
                'email' => $admin['email'],
                'role' => $admin['role'],
                'avatar' => $admin['avatar'] ?? 'assets/images/salman.jpg',
                'phone' => $admin['phone'] ?? '252613878223',
                'address' => $admin['address'] ?? ''
            ];
            return;
        }
        setFlash('error', 'Administrator privileges required.');
        header('Location: ../login.php');
        exit;
    }
}

// Cart Management via Session
function initCart() {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
}

function getCartCount() {
    initCart();
    $count = 0;
    if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            if (is_array($item)) {
                $count += (int)($item['quantity'] ?? 1);
            } elseif (is_numeric($item)) {
                $count += (int)$item;
            }
        }
    }
    return $count;
}

function getCartItems() {
    initCart();
    $items = [];
    if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $key => $item) {
            if (is_array($item)) {
                $items[$key] = $item;
            }
        }
    }
    return $items;
}

function addToCart($productId, $quantity = 1) {
    initCart();
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([(int)$productId]);
    $product = $stmt->fetch();

    if (!$product) {
        return ['success' => false, 'message' => 'Product not found'];
    }

    $id = (int)$productId;
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['quantity'] += (int)$quantity;
    } else {
        $_SESSION['cart'][$id] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => (float)$product['price'],
            'image' => $product['image'],
            'slug' => $product['slug'],
            'quantity' => (int)$quantity
        ];
    }

    return [
        'success' => true,
        'message' => 'Added to cart successfully!',
        'cartCount' => getCartCount(),
        'cartTotal' => calculateCartTotals()['subtotal']
    ];
}

function updateCartItem($productId, $quantity) {
    initCart();
    $id = (int)$productId;
    $quantity = (int)$quantity;

    if ($quantity <= 0) {
        unset($_SESSION['cart'][$id]);
    } else if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['quantity'] = $quantity;
    }

    return calculateCartTotals();
}

function removeCartItem($productId) {
    initCart();
    $id = (int)$productId;
    if (isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]);
    }
    return calculateCartTotals();
}

function clearCart() {
    $_SESSION['cart'] = [];
    unset($_SESSION['coupon']);
}

function calculateCartTotals() {
    initCart();
    $subtotal = 0;
    if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            if (is_array($item)) {
                $subtotal += ((float)($item['price'] ?? 0) * (int)($item['quantity'] ?? 1));
            }
        }
    }

    $discount = 0;
    $couponCode = $_SESSION['coupon']['code'] ?? null;
    $discountPercent = $_SESSION['coupon']['percent'] ?? 0;

    if ($couponCode && $discountPercent > 0) {
        $discount = ($subtotal * $discountPercent) / 100;
    }

    $shipping = ($subtotal > 0 && $subtotal < 100) ? 15.00 : 0.00; // Free shipping over $100
    $total = max(0, $subtotal - $discount + $shipping);

    return [
        'subtotal' => $subtotal,
        'discount' => $discount,
        'discountPercent' => $discountPercent,
        'couponCode' => $couponCode,
        'shipping' => $shipping,
        'total' => $total,
        'itemCount' => getCartCount()
    ];
}

// Status Badges
function renderOrderStatusBadge($status) {
    $status = ucfirst(strtolower(trim($status)));
    switch ($status) {
        case 'Delivered':
            return '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20"><i class="fa-solid fa-circle-check"></i> Delivered</span>';
        case 'Shipped':
            return '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-500/10 text-blue-400 border border-blue-500/20"><i class="fa-solid fa-truck-fast"></i> Shipped</span>';
        case 'Processing':
            return '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20"><i class="fa-solid fa-gear fa-spin"></i> Processing</span>';
        case 'Cancelled':
            return '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/20"><i class="fa-solid fa-ban"></i> Cancelled</span>';
        default:
            return '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-zinc-500/10 text-zinc-400 border border-zinc-500/20"><i class="fa-solid fa-clock"></i> Pending</span>';
    }
}
