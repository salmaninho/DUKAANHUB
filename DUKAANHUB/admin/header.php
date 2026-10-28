<?php
require_once __DIR__ . '/../includes/functions.php';

// Verify Admin authentication
requireAdmin();
$adminUser = getCurrentUser();
$pdo = getDB();
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($adminPageTitle) ? htmlspecialchars($adminPageTitle) . ' | ' : '' ?>DukaanHub - Admin Dashboard</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            500: '#8b5cf6',
                            600: '#7c3aed',
                            700: '#6d28d9',
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
    
    <!-- Chart.js for Dashboard Analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Custom Adaptive CSS -->
    <link rel="stylesheet" href="../assets/css/custom.css">
</head>
<body class="bg-gray-50 dark:bg-darkbg text-slate-800 dark:text-slate-100 min-h-screen flex selection:bg-violet-500 selection:text-white">

    <!-- Admin Sleek Sidebar -->
    <aside id="admin-sidebar" class="w-64 border-r theme-border flex flex-col justify-between fixed inset-y-0 left-0 z-40 transition-transform duration-300 lg:translate-x-0 -translate-x-full">
        <div>
            <!-- Admin Brand Logo -->
            <div class="h-20 px-4 flex items-center border-b theme-border">
                <a href="../index.php" title="Back to Store">
                    <img src="../assets/images/logo.svg" alt="DukaanHub" class="h-11 w-auto block dark:hidden" style="max-width:170px;">
                    <img src="../assets/images/logo-dark.svg" alt="DukaanHub" class="h-11 w-auto hidden dark:block" style="max-width:170px;">
                </a>
            </div>

            <!-- Sidebar Navigation Links -->
            <div class="p-4 space-y-1 text-xs font-semibold">
                <p class="px-3 py-2 text-[10px] uppercase font-bold tracking-widest text-slate-400">Core Management</p>
                
                <a href="index.php" class="flex items-center gap-3 px-3.5 py-3 rounded-2xl text-slate-700 dark:text-slate-300 hover:text-violet-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'bg-violet-600/10 text-violet-600 dark:text-violet-300 border border-violet-500/30' : '' ?>">
                    <i class="fa-solid fa-chart-pie text-sm text-violet-500"></i>
                    <span>Dashboard Stats</span>
                </a>

                <a href="products.php" class="flex items-center gap-3 px-3.5 py-3 rounded-2xl text-slate-700 dark:text-slate-300 hover:text-violet-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition <?= basename($_SERVER['PHP_SELF']) === 'products.php' ? 'bg-violet-600/10 text-violet-600 dark:text-violet-300 border border-violet-500/30' : '' ?>">
                    <i class="fa-solid fa-boxes-stacked text-sm text-cyan-500"></i>
                    <span>Products Catalog</span>
                </a>

                <a href="orders.php" class="flex items-center gap-3 px-3.5 py-3 rounded-2xl text-slate-700 dark:text-slate-300 hover:text-violet-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition <?= basename($_SERVER['PHP_SELF']) === 'orders.php' ? 'bg-violet-600/10 text-violet-600 dark:text-violet-300 border border-violet-500/30' : '' ?>">
                    <i class="fa-solid fa-cart-flatbed text-sm text-emerald-500"></i>
                    <span>Orders & Shipments</span>
                </a>

                <a href="categories.php" class="flex items-center gap-3 px-3.5 py-3 rounded-2xl text-slate-700 dark:text-slate-300 hover:text-violet-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition <?= basename($_SERVER['PHP_SELF']) === 'categories.php' ? 'bg-violet-600/10 text-violet-600 dark:text-violet-300 border border-violet-500/30' : '' ?>">
                    <i class="fa-solid fa-tags text-sm text-amber-500"></i>
                    <span>Product Categories</span>
                </a>

                <a href="users.php" class="flex items-center gap-3 px-3.5 py-3 rounded-2xl text-slate-700 dark:text-slate-300 hover:text-violet-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition <?= basename($_SERVER['PHP_SELF']) === 'users.php' ? 'bg-violet-600/10 text-violet-600 dark:text-violet-300 border border-violet-500/30' : '' ?>">
                    <i class="fa-solid fa-users text-sm text-purple-500"></i>
                    <span>Registered Users</span>
                </a>

                <p class="px-3 pt-6 pb-2 text-[10px] uppercase font-bold tracking-widest text-slate-400">Live Storefront</p>

                <a href="../index.php" target="_blank" class="flex items-center gap-3 px-3.5 py-3 rounded-2xl text-slate-700 dark:text-slate-300 hover:text-violet-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    <i class="fa-solid fa-arrow-up-right-from-square text-sm text-indigo-500"></i>
                    <span>View Public Store</span>
                </a>
            </div>
        </div>

        <!-- Sidebar Footer Profile -->
        <div class="p-4 border-t theme-border">
            <div class="flex items-center gap-3 p-2 rounded-2xl glass-card border theme-border">
                <?php 
                    $adAvatar = $adminUser['avatar'] ?? '../assets/images/salman.jpg';
                    if (strpos($adAvatar, 'http') !== 0 && strpos($adAvatar, '../') !== 0) {
                        $adAvatar = '../' . $adAvatar;
                    }
                ?>
                <img src="<?= htmlspecialchars($adAvatar) ?>" alt="<?= htmlspecialchars($adminUser['name']) ?>" class="w-9 h-9 rounded-xl object-cover">
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold theme-text-title truncate"><?= htmlspecialchars($adminUser['name']) ?></p>
                    <span class="text-[10px] text-violet-600 dark:text-violet-400 font-semibold block uppercase">Administrator</span>
                </div>
                <a href="../logout.php" class="text-slate-400 hover:text-rose-500 p-1.5 transition" title="Logout">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Content Container with Left Margin for Sidebar -->
    <div class="lg:pl-64 flex flex-col flex-1 w-full min-w-0">
        
        <!-- Top Admin Header Bar -->
        <header class="h-20 glass-panel border-b theme-border sticky top-0 z-30 px-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <button type="button" onclick="document.getElementById('admin-sidebar').classList.toggle('-translate-x-full')" class="lg:hidden text-slate-500 hover:text-slate-800 dark:hover:text-white">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>
                <div class="hidden sm:flex items-center gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-violet-600/10 text-violet-600 dark:text-violet-300 border border-violet-500/30">
                        DukaanHub • Administrator Portal
                    </span>
                    <span class="text-xs text-slate-400 font-medium"><?= htmlspecialchars($adminUser['email'] ?? 'Salmansahaneabdi@gmail.com') ?></span>
                </div>
            </div>

            <!-- Top Right Tools -->
            <div class="flex items-center gap-3">
                <!-- Theme Toggle Button -->
                <button type="button" onclick="toggleTheme()" class="theme-switch-btn" title="Toggle White/Dark Mode">
                    <i class="theme-toggle-icon fa-solid fa-moon text-violet-400"></i>
                    <span class="theme-toggle-text">Dark Mode</span>
                </button>

                <a href="../shop.php" class="px-4 py-2 rounded-xl bg-violet-600 hover:bg-violet-500 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5">
                    <i class="fa-solid fa-shop"></i> Storefront
                </a>
            </div>
        </header>

        <!-- Admin Page Body Area -->
        <main class="p-6 lg:p-10 flex-1">
