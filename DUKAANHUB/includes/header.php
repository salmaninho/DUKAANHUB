<?php
require_once __DIR__ . '/functions.php';
$currentUser = getCurrentUser();
$cartCount = getCartCount();
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' : '' ?>DukaanHub - Premium E-Commerce Hub</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f5f3ff',
                            100: '#ede9fe',
                            400: '#a78bfa',
                            500: '#8b5cf6',
                            600: '#7c3aed',
                            700: '#6d28d9',
                            900: '#4c1d95',
                        },
                        darkbg: '#090d16',
                        darkcard: '#111726',
                        darkborder: '#1e293b'
                    }
                }
            }
        }
    </script>
    
    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Custom Adaptive CSS -->
    <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body class="bg-gray-50 dark:bg-darkbg text-slate-800 dark:text-slate-100 min-h-screen flex flex-col selection:bg-violet-500 selection:text-white">

    <!-- Top Announcement & Contact Bar -->
    <div class="top-banner-bar text-xs py-2 px-4 border-b transition-colors">
        <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-2">
            
            <!-- Left Contact Info -->
            <div class="flex items-center gap-4 text-slate-600 dark:text-slate-300">
                <span class="inline-flex items-center gap-1.5 font-semibold text-violet-600 dark:text-violet-400">
                    <i class="fa-solid fa-store"></i> DukaanHub Official
                </span>
                <span class="hidden sm:inline-flex items-center gap-1 text-slate-500 dark:text-slate-400">
                    <i class="fa-solid fa-envelope text-violet-500"></i> Salmansahaneabdi@gmail.com
                </span>
                <span class="hidden md:inline-flex items-center gap-1 text-slate-500 dark:text-slate-400">
                    <i class="fa-solid fa-phone text-emerald-500"></i> +252 61 387 8223
                </span>
            </div>

            <!-- Right Controls: Dark/White Toggle Switch -->
            <div class="flex items-center gap-2.5">
                <!-- High-Visibility Dark / White Mode Switch Button -->
                <button type="button" onclick="toggleTheme()" class="theme-switch-btn" title="Toggle White/Dark Mode">
                    <i class="theme-toggle-icon fa-solid fa-moon text-violet-400"></i>
                    <span class="theme-toggle-text">Dark Mode</span>
                </button>
            </div>

        </div>
    </div>

    <!-- Main Navigation Header -->
    <header class="sticky top-0 z-40 glass-panel border-b transition-colors backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20 gap-4">
                
                <!-- Brand Logo: DukaanHub SVG -->
                <a href="index.php" class="group flex items-center group-hover:opacity-90 transition-opacity" title="DukaanHub - Home">
                    <!-- White Mode Logo -->
                    <img src="assets/images/logo.svg" alt="DukaanHub" class="h-12 sm:h-14 w-auto block dark:hidden group-hover:scale-105 transition-transform duration-200" style="max-width:200px;">
                    <!-- Dark Mode Logo -->
                    <img src="assets/images/logo-dark.svg" alt="DukaanHub" class="h-12 sm:h-14 w-auto hidden dark:block group-hover:scale-105 transition-transform duration-200" style="max-width:200px;">
                </a>

                <!-- Search Input Bar -->
                <form action="shop.php" method="GET" class="hidden md:flex flex-1 max-w-md mx-4">
                    <div class="relative w-full">
                        <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="Search 31+ luxury products, sneakers, watches, electronics..." 
                               class="w-full pl-11 pr-4 py-2 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-500 transition shadow-inner">
                        <button type="submit" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-violet-500 transition">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </div>
                </form>

                <!-- Navigation Links -->
                <nav class="hidden lg:flex items-center gap-6 text-sm font-semibold">
                    <a href="index.php" class="text-slate-700 dark:text-slate-200 hover:text-violet-600 dark:hover:text-violet-400 transition flex items-center gap-1.5">
                        <i class="fa-solid fa-house text-xs"></i> Home
                    </a>
                    <a href="shop.php" class="text-slate-700 dark:text-slate-200 hover:text-violet-600 dark:hover:text-violet-400 transition flex items-center gap-1.5">
                        <i class="fa-solid fa-bag-shopping text-xs"></i> Shop All
                    </a>
                    <a href="shop.php?deal=1" class="text-amber-500 hover:text-amber-400 transition flex items-center gap-1.5 font-bold">
                        <i class="fa-solid fa-bolt text-xs"></i> Flash Deals
                    </a>
                    <a href="admin/index.php" class="px-3.5 py-1.5 rounded-xl bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white font-bold text-xs shadow-md shadow-violet-600/30 transition flex items-center gap-1.5">
                        <i class="fa-solid fa-gauge-high text-amber-300"></i> Admin Dashboard
                    </a>
                </nav>

                <!-- Right Action Buttons -->
                <div class="flex items-center gap-3">
                    
                    <!-- Wishlist Button -->
                    <a href="shop.php?wishlist=1" class="relative p-2.5 rounded-xl border theme-border hover:bg-slate-100 dark:hover:bg-slate-800 transition text-slate-700 dark:text-slate-300" title="Wishlist">
                        <i class="fa-regular fa-heart text-lg"></i>
                        <span class="wishlist-badge absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full bg-rose-500 text-[11px] font-bold text-white flex items-center justify-center shadow-md">0</span>
                    </a>

                    <!-- Cart Drawer Button -->
                    <button type="button" onclick="toggleCartDrawer()" class="relative p-2.5 rounded-xl bg-violet-600/10 hover:bg-violet-600/20 text-violet-600 dark:text-violet-400 border border-violet-500/30 transition flex items-center gap-2" title="Cart Bag">
                        <i class="fa-solid fa-bag-shopping text-lg"></i>
                        <span class="cart-badge w-5 h-5 rounded-full bg-violet-600 text-[11px] font-bold text-white flex items-center justify-center shadow-md shadow-violet-600/50"><?= $cartCount ?></span>
                    </button>

                    <!-- User Account Dropdown / Login Button -->
                    <?php if ($currentUser): ?>
                    <div class="relative group">
                        <button class="flex items-center gap-2 p-1.5 rounded-xl border theme-border hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                            <img src="<?= htmlspecialchars($currentUser['avatar'] ?? 'assets/images/salman.jpg') ?>" 
                                 alt="Avatar" class="w-8 h-8 rounded-lg object-cover">
                            <span class="hidden md:inline text-xs font-bold max-w-[100px] truncate text-slate-800 dark:text-slate-200"><?= htmlspecialchars($currentUser['name']) ?></span>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 pr-1"></i>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div class="absolute right-0 mt-2 w-56 rounded-2xl glass-panel shadow-2xl border theme-border py-2 hidden group-hover:block transition-all z-50">
                            <div class="px-4 py-2 border-b theme-border">
                                <p class="text-xs text-slate-400">Signed in as</p>
                                <p class="text-sm font-bold text-slate-900 dark:text-white truncate"><?= htmlspecialchars($currentUser['name']) ?></p>
                                <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-semibold uppercase bg-violet-500/20 text-violet-600 dark:text-violet-300">
                                    <?= htmlspecialchars($currentUser['role']) ?>
                                </span>
                            </div>
                            <?php if (isAdmin()): ?>
                            <a href="admin/index.php" class="flex items-center gap-2 px-4 py-2.5 text-xs text-violet-600 dark:text-violet-400 hover:bg-violet-600/10 transition font-medium">
                                <i class="fa-solid fa-gauge-high"></i> Admin Dashboard
                            </a>
                            <?php endif; ?>
                            <a href="my-orders.php" class="flex items-center gap-2 px-4 py-2.5 text-xs text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                                <i class="fa-solid fa-clock-rotate-left"></i> My Orders & Receipts
                            </a>
                            <a href="logout.php" class="flex items-center gap-2 px-4 py-2.5 text-xs text-rose-500 hover:bg-rose-500/10 transition border-t theme-border mt-1 font-semibold">
                                <i class="fa-solid fa-arrow-right-from-bracket"></i> Sign Out
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Mobile Menu Hamburger Button -->
                    <button type="button" onclick="document.getElementById('mobile-nav').classList.toggle('hidden')" class="lg:hidden p-2 rounded-xl border theme-border text-slate-700 dark:text-slate-300">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div id="mobile-nav" class="hidden lg:hidden border-t theme-border px-4 py-4 space-y-3 glass-panel">
            <form action="shop.php" method="GET" class="mb-4">
                <input type="text" name="search" placeholder="Search products..." class="w-full px-4 py-2 rounded-xl border text-sm">
            </form>
            <a href="index.php" class="block py-2 text-sm font-semibold hover:text-violet-600">Home</a>
            <a href="shop.php" class="block py-2 text-sm font-semibold hover:text-violet-600">Shop Catalog</a>
            <a href="shop.php?deal=1" class="block py-2 text-sm font-semibold text-amber-500 hover:text-amber-400">Flash Deals</a>
            <a href="cart.php" class="block py-2 text-sm font-semibold hover:text-violet-600">Shopping Bag (<?= $cartCount ?>)</a>
            <a href="admin/index.php" class="block py-2 text-sm font-bold text-violet-600 dark:text-violet-400">
                <i class="fa-solid fa-gauge-high mr-1.5"></i> Admin Dashboard
            </a>
        </div>
    </header>

    <!-- Session Flash Message Toasts -->
    <?php if ($flash): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            showToast(<?= json_encode($flash['message']) ?>, <?= json_encode($flash['type']) ?>);
        });
    </script>
    <?php endif; ?>

    <!-- Main Content Area Starts -->
    <main class="flex-grow">
