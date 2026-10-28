<?php
$pageTitle = 'Home - Curated Luxury Store';
require_once __DIR__ . '/includes/header.php';

$pdo = getDB();

// Fetch categories
$categories = $pdo->query("SELECT * FROM categories ORDER BY id ASC")->fetchAll();

// Fetch active category filter if any
$selectedCategory = trim($_GET['category'] ?? '');

// Fetch featured products
if (!empty($selectedCategory)) {
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug 
                           FROM products p 
                           LEFT JOIN categories c ON p.category_id = c.id 
                           WHERE c.slug = ?
                           ORDER BY p.id ASC");
    $stmt->execute([$selectedCategory]);
    $featuredProducts = $stmt->fetchAll();
} else {
    $featuredProducts = $pdo->query("SELECT p.*, c.name as category_name, c.slug as category_slug 
                                      FROM products p 
                                      LEFT JOIN categories c ON p.category_id = c.id 
                                      ORDER BY p.is_featured DESC, p.id ASC LIMIT 16")->fetchAll();
}

// Fetch flash deals
$deals = $pdo->query("SELECT p.*, c.name as category_name 
                      FROM products p 
                      LEFT JOIN categories c ON p.category_id = c.id 
                      WHERE p.is_deal = 1 
                      ORDER BY p.id ASC LIMIT 4")->fetchAll();
?>

<!-- Compact Luxury Hero Banner (Adaptive Dual-Engine: Radiant Pearl in White Mode / Obsidian Luxury in Dark Mode) -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 pb-4">
    <div class="relative rounded-3xl p-6 sm:p-8 overflow-hidden hero-luxury-banner transition-colors duration-200">
        <!-- Ambient Glow -->
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-violet-500/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute right-1/3 -bottom-20 w-64 h-64 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
            <!-- Left Info -->
            <div class="max-w-2xl space-y-3 text-center md:text-left">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full hero-badge-pill text-xs font-bold transition">
                    <i class="fa-solid fa-sparkles text-amber-500"></i> Welcome to DukaanHub • The Premier Luxury Marketplace
                </div>

                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight leading-tight">
                    Curated Luxury & <span class="text-transparent bg-clip-text bg-gradient-to-r from-violet-600 via-indigo-600 to-cyan-500 dark:from-violet-400 dark:via-indigo-300 dark:to-cyan-400">Tech Excellence</span>
                </h1>

                <p class="text-xs sm:text-sm leading-relaxed font-normal">
                    Experience genuine flagship laptops, titanium timepieces, spatial VR headsets, and designer streetwear with instant 1-click cart and express worldwide shipping.
                </p>

                <!-- Action Links -->
                <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 pt-1">
                    <a href="shop.php" class="px-5 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-700 text-white font-bold text-xs shadow-lg shadow-violet-600/30 transition flex items-center gap-2">
                        <i class="fa-solid fa-bag-shopping"></i> Explore 31+ Products
                    </a>
                    <a href="shop.php?deal=1" class="px-5 py-2.5 rounded-xl bg-white dark:bg-slate-800/80 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold text-xs border border-slate-300 dark:border-slate-700 shadow-sm transition flex items-center gap-2">
                        <i class="fa-solid fa-bolt text-amber-500"></i> Flash Deals
                    </a>
                </div>
            </div>

            <!-- Right Fast Badges -->
            <div class="grid grid-cols-2 gap-3 w-full md:w-auto">
                <div class="p-3.5 rounded-2xl hero-stat-card text-center transition">
                    <p class="text-xl sm:text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-violet-600 to-indigo-600 dark:from-violet-300 dark:to-cyan-300">31+</p>
                    <p class="stat-label text-[10px] font-bold uppercase tracking-wider mt-0.5">Luxury Items</p>
                </div>
                <div class="p-3.5 rounded-2xl hero-stat-card text-center transition">
                    <p class="text-xl sm:text-2xl font-extrabold text-emerald-600 dark:text-emerald-400">100%</p>
                    <p class="stat-label text-[10px] font-bold uppercase tracking-wider mt-0.5">Authentic Stock</p>
                </div>
                <div class="p-3.5 rounded-2xl hero-stat-card text-center transition">
                    <p class="text-xl sm:text-2xl font-extrabold text-amber-500 dark:text-amber-400">4.9 ★</p>
                    <p class="stat-label text-[10px] font-bold uppercase tracking-wider mt-0.5">VIP Rating</p>
                </div>
                <div class="p-3.5 rounded-2xl hero-stat-card text-center transition">
                    <p class="text-xl sm:text-2xl font-extrabold text-cyan-600 dark:text-cyan-400">Free</p>
                    <p class="stat-label text-[10px] font-bold uppercase tracking-wider mt-0.5">Fast Shipping</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Category Quick Navigation Pills (Well-Separated & Crisp) -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Browse Collections</h3>
        <a href="shop.php" class="text-xs font-bold text-violet-500 hover:underline">View All &rarr;</a>
    </div>

    <div class="flex items-center gap-2.5 overflow-x-auto pb-2 scrollbar-none">
        <a href="index.php" class="product-pill-cat px-4 py-2 rounded-xl text-xs font-bold border transition flex items-center gap-2 whitespace-nowrap <?= empty($selectedCategory) ? 'active' : '' ?>">
            <i class="fa-solid fa-border-all"></i> All Categories (31)
        </a>
        <?php foreach ($categories as $cat): ?>
            <a href="index.php?category=<?= urlencode($cat['slug']) ?>" class="product-pill-cat px-4 py-2 rounded-xl text-xs font-bold border transition flex items-center gap-2 whitespace-nowrap <?= ($selectedCategory === $cat['slug']) ? 'active' : '' ?>">
                <i class="fa-solid <?= htmlspecialchars($cat['icon']) ?>"></i> <?= htmlspecialchars($cat['name']) ?>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- Flash Deals Highlight Bar (Compact) -->
<?php if (!empty($deals)): ?>
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
    <div class="p-4 rounded-2xl glass-card border theme-border flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-500 flex items-center justify-center text-lg">
                <i class="fa-solid fa-bolt"></i>
            </span>
            <div>
                <h4 class="text-sm font-bold theme-text-title">Flash Deals & Limited Edition Drops</h4>
                <p class="text-xs theme-text-muted">Save up to 40% on top electronics, sneakers, and luxury items today.</p>
            </div>
        </div>
        <a href="shop.php?deal=1" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs shadow transition flex items-center gap-1.5 whitespace-nowrap">
            <i class="fa-solid fa-fire"></i> Shop All Deals
        </a>
    </div>
</section>
<?php endif; ?>

<!-- Curated Product Catalog Grid (Compact, Well-Separated, Highly Legible) -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
        <div>
            <h2 class="text-xl sm:text-2xl font-extrabold theme-text-title">
                <?= !empty($selectedCategory) ? htmlspecialchars(ucwords(str_replace('-', ' ', $selectedCategory))) : 'Featured Luxury Products' ?>
            </h2>
            <p class="text-xs theme-text-muted mt-0.5">Showing <?= count($featuredProducts) ?> authentic hand-crafted products in DukaanHub</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="shop.php" class="px-3.5 py-1.5 rounded-xl border theme-border text-xs font-semibold theme-text-muted hover:theme-text-title transition">
                <i class="fa-solid fa-filter mr-1"></i> Advanced Filters
            </a>
            <a href="shop.php?sort=price_low" class="px-3.5 py-1.5 rounded-xl border theme-border text-xs font-semibold theme-text-muted hover:theme-text-title transition">
                <i class="fa-solid fa-arrow-down-short-wide mr-1"></i> Sort
            </a>
        </div>
    </div>

    <!-- Product Cards Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
        <?php foreach ($featuredProducts as $product): ?>
            <div class="group glass-card rounded-2xl overflow-hidden flex flex-col transition-all duration-200">
                
                <!-- Product Image & Badges -->
                <div class="relative product-card-img-wrapper aspect-square bg-slate-100 dark:bg-slate-900">
                    <img src="<?= htmlspecialchars($product['image']) ?>" 
                         alt="<?= htmlspecialchars($product['name']) ?>" 
                         loading="lazy"
                         class="w-full h-full object-cover">

                    <!-- Top Floating Badges -->
                    <div class="absolute top-2.5 left-2.5 flex flex-col gap-1 z-10">
                        <?php if (!empty($product['badge'])): ?>
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-violet-600 text-white shadow-sm">
                                <?= htmlspecialchars($product['badge']) ?>
                            </span>
                        <?php endif; ?>
                        <?php if ($product['is_deal']): ?>
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-rose-600 text-white shadow-sm">
                                SALE
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Quick Action Overlay Buttons -->
                    <div class="absolute top-2.5 right-2.5 flex flex-col gap-1.5 opacity-90 sm:opacity-0 group-hover:opacity-100 transition-opacity z-10">
                        <button type="button" onclick="openQuickView(<?= $product['id'] ?>)" class="w-8 h-8 rounded-xl bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:text-violet-600 shadow-md flex items-center justify-center text-xs transition" title="Quick View">
                            <i class="fa-regular fa-eye"></i>
                        </button>
                        <button type="button" onclick="toggleWishlist(<?= $product['id'] ?>, this)" class="w-8 h-8 rounded-xl bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:text-rose-500 shadow-md flex items-center justify-center text-xs transition" title="Save to Wishlist">
                            <i class="fa-regular fa-heart"></i>
                        </button>
                    </div>
                </div>

                <!-- Product Body Details -->
                <div class="p-4 flex-1 flex flex-col justify-between">
                    <div>
                        <!-- Category & Rating -->
                        <div class="flex items-center justify-between text-[11px] mb-1">
                            <span class="font-semibold text-violet-600 dark:text-violet-400 uppercase tracking-wider"><?= htmlspecialchars($product['category_name'] ?? 'Luxury') ?></span>
                            <span class="flex items-center gap-1 font-bold text-amber-500">
                                <i class="fa-solid fa-star text-[10px]"></i> <?= number_format($product['rating'], 1) ?>
                            </span>
                        </div>

                        <!-- Product Title -->
                        <h3 class="font-bold text-sm leading-snug line-clamp-2 mb-2 theme-text-title hover:text-violet-600 transition">
                            <a href="product-details.php?id=<?= $product['id'] ?>">
                                <?= htmlspecialchars($product['name']) ?>
                            </a>
                        </h3>
                    </div>

                    <!-- Price & Add To Cart Button -->
                    <div class="pt-2 border-t theme-border flex items-center justify-between mt-auto">
                        <div>
                            <span class="text-base sm:text-lg font-extrabold text-emerald-600 dark:text-emerald-400">
                                <?= formatPrice($product['price']) ?>
                            </span>
                            <?php if ($product['original_price'] && $product['original_price'] > $product['price']): ?>
                                <span class="block text-[11px] text-slate-400 line-through">
                                    <?= formatPrice($product['original_price']) ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <button type="button" onclick="handleAddToCart(<?= $product['id'] ?>, 1, this)" class="px-3 py-2 rounded-xl bg-violet-600 hover:bg-violet-500 text-white text-xs font-bold shadow-md shadow-violet-600/30 transition flex items-center gap-1.5" title="Add to Bag">
                            <i class="fa-solid fa-plus"></i> <span class="hidden sm:inline">Add</span>
                        </button>
                    </div>
                </div>

            </div>
        <?php endforeach; ?>
    </div>

    <!-- View Catalog Footer Banner -->
    <div class="mt-10 p-6 rounded-3xl glass-card border theme-border text-center">
        <h3 class="text-base font-bold theme-text-title">Looking for something specific?</h3>
        <p class="text-xs theme-text-muted mt-1 max-w-md mx-auto">Browse our entire catalog of 31 luxury products across Electronics, Gaming & VR, Smart Watches, Footwear, Fashion, and Accessories.</p>
        <a href="shop.php" class="inline-flex items-center gap-2 mt-4 px-6 py-2.5 rounded-xl bg-gradient-to-r from-violet-600 to-indigo-600 text-white text-xs font-bold shadow-lg shadow-violet-600/30 hover:opacity-95 transition">
            <i class="fa-solid fa-shop"></i> View Entire Catalog (31 Products)
        </a>
    </div>
</section>

<!-- Trust Badges Section -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl glass-card border theme-border flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-violet-500/10 text-violet-500 flex items-center justify-center text-lg">
                <i class="fa-solid fa-truck-fast"></i>
            </div>
            <div>
                <h5 class="text-xs font-bold theme-text-title">Free Express Shipping</h5>
                <p class="text-[11px] theme-text-muted">On all orders over $100</p>
            </div>
        </div>

        <div class="p-4 rounded-2xl glass-card border theme-border flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center text-lg">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div>
                <h5 class="text-xs font-bold theme-text-title">100% Genuine Stock</h5>
                <p class="text-[11px] theme-text-muted">Authentic luxury guarantee</p>
            </div>
        </div>

        <div class="p-4 rounded-2xl glass-card border theme-border flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-cyan-500/10 text-cyan-500 flex items-center justify-center text-lg">
                <i class="fa-solid fa-rotate-left"></i>
            </div>
            <div>
                <h5 class="text-xs font-bold theme-text-title">30-Day Easy Returns</h5>
                <p class="text-[11px] theme-text-muted">Hassle-free instant refunds</p>
            </div>
        </div>

        <div class="p-4 rounded-2xl glass-card border theme-border flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center text-lg">
                <i class="fa-solid fa-headset"></i>
            </div>
            <div>
                <h5 class="text-xs font-bold theme-text-title">24/7 VIP Assistance</h5>
                <p class="text-[11px] theme-text-muted">Dedicated store support</p>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
