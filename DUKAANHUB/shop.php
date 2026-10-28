<?php
$pageTitle = 'Shop All Products - Luxury Catalog';
require_once __DIR__ . '/includes/header.php';

$pdo = getDB();

$search = trim($_GET['search'] ?? '');
$categorySlug = trim($_GET['category'] ?? '');
$isDeal = isset($_GET['deal']) ? 1 : 0;
$sort = $_GET['sort'] ?? 'newest';

// Base Query
$query = "SELECT p.*, c.name as category_name, c.slug as category_slug 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($categorySlug)) {
    $query .= " AND c.slug = ?";
    $params[] = $categorySlug;
}

if ($isDeal === 1) {
    $query .= " AND p.is_deal = 1";
}

// Sorting
switch ($sort) {
    case 'price_asc':
        $query .= " ORDER BY p.price ASC";
        break;
    case 'price_desc':
        $query .= " ORDER BY p.price DESC";
        break;
    case 'popular':
        $query .= " ORDER BY p.reviews_count DESC";
        break;
    case 'rating':
        $query .= " ORDER BY p.rating DESC";
        break;
    default:
        $query .= " ORDER BY p.id DESC";
        break;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

// All categories for sidebar
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    
    <!-- Breadcrumb & Header Title -->
    <div class="mb-8">
        <div class="flex items-center gap-2 text-xs text-zinc-500 mb-2">
            <a href="index.php" class="hover:text-violet-400">Home</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <span class="text-zinc-300">Catalog</span>
            <?php if (!empty($categorySlug)): ?>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                <span class="text-violet-400 capitalize"><?= htmlspecialchars(str_replace('-', ' ', $categorySlug)) ?></span>
            <?php endif; ?>
        </div>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-zinc-900 dark:text-white">
                    <?= $isDeal ? '🔥 Flash Deals & Discounts' : (!empty($categorySlug) ? ucwords(str_replace('-', ' ', $categorySlug)) : 'Luxury Products Catalog') ?>
                </h1>
                <p class="text-xs text-zinc-400 mt-1">
                    Showing <span class="font-bold text-violet-400"><?= count($products) ?></span> exclusive products
                </p>
            </div>

            <!-- Sorting Dropdown Form -->
            <form id="sort-form" method="GET" class="flex items-center gap-3">
                <?php if (!empty($categorySlug)): ?><input type="hidden" name="category" value="<?= htmlspecialchars($categorySlug) ?>"><?php endif; ?>
                <?php if (!empty($search)): ?><input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>"><?php endif; ?>
                <?php if ($isDeal): ?><input type="hidden" name="deal" value="1"><?php endif; ?>

                <label class="text-xs font-semibold text-zinc-400 whitespace-nowrap">Sort By:</label>
                <select name="sort" onchange="document.getElementById('sort-form').submit()" class="px-4 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-violet-500">
                    <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest Arrivals</option>
                    <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                    <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                    <option value="popular" <?= $sort === 'popular' ? 'selected' : '' ?>>Most Popular</option>
                    <option value="rating" <?= $sort === 'rating' ? 'selected' : '' ?>>Highest Rated</option>
                </select>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        <!-- Left Filter Sidebar -->
        <aside class="space-y-6">
            <!-- Category Filter Card -->
            <div class="p-6 rounded-3xl glass-card border border-zinc-800">
                <h3 class="text-sm font-bold uppercase tracking-wider text-white mb-4 flex items-center justify-between">
                    <span>Categories</span>
                    <i class="fa-solid fa-layer-group text-violet-400"></i>
                </h3>
                <ul class="space-y-2 text-xs">
                    <li>
                        <a href="shop.php" class="flex items-center justify-between py-2 px-3 rounded-xl transition <?= empty($categorySlug) ? 'bg-violet-600/20 text-violet-600 dark:text-violet-300 font-bold border border-violet-500/30' : 'text-slate-500 dark:text-zinc-400 hover:text-violet-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-zinc-800/60' ?>">
                            <span>All Collections</span>
                            <span class="text-[10px] bg-slate-200 dark:bg-zinc-800 px-2 py-0.5 rounded-full font-bold">31</span>
                        </a>
                    </li>
                    <?php foreach ($categories as $cat): ?>
                        <li>
                            <a href="shop.php?category=<?= urlencode($cat['slug']) ?>" class="flex items-center justify-between py-2 px-3 rounded-xl transition <?= ($categorySlug === $cat['slug']) ? 'bg-violet-600/20 text-violet-600 dark:text-violet-300 font-bold border border-violet-500/30' : 'text-slate-500 dark:text-zinc-400 hover:text-violet-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-zinc-800/60' ?>">
                                <span class="flex items-center gap-2">
                                    <i class="fa-solid <?= htmlspecialchars($cat['icon']) ?> text-[11px] text-slate-400"></i>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- VIP Luxury Guarantee Card -->
            <div class="p-6 rounded-3xl bg-gradient-to-br from-violet-900/80 to-slate-900 border border-violet-500/30 text-center text-white">
                <i class="fa-solid fa-gem text-3xl text-violet-400 mb-2"></i>
                <h4 class="text-sm font-bold text-white mb-1">Authentic Luxury</h4>
                <p class="text-xs text-slate-300 mb-3">100% verified genuine products with express global shipping.</p>
                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-violet-600/40 text-[11px] font-bold text-violet-200 border border-violet-400/30">
                    <i class="fa-solid fa-shield-check"></i> DukaanHub Verified
                </div>
            </div>
        </aside>

        <!-- Product Grid Main Area -->
        <div class="lg:col-span-3">
            <?php if (empty($products)): ?>
                <div class="text-center py-20 rounded-3xl glass-card border border-zinc-800">
                    <i class="fa-solid fa-magnifying-glass text-5xl text-zinc-600 mb-4"></i>
                    <h3 class="text-lg font-bold text-white mb-2">No matching items found</h3>
                    <p class="text-xs text-zinc-400 mb-6">Try searching with another keyword or explore all collections.</p>
                    <a href="shop.php" class="px-6 py-3 rounded-xl bg-violet-600 hover:bg-violet-500 text-white font-bold text-xs shadow-md transition">Reset All Filters</a>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($products as $product): ?>
                        <div class="group rounded-3xl glass-card overflow-hidden flex flex-col justify-between border border-zinc-800/80">
                            <!-- Image & Actions -->
                            <div class="relative product-card-img-wrapper aspect-square bg-zinc-900 overflow-hidden">
                                <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-full h-full object-cover">
                                
                                <?php if (!empty($product['badge'])): ?>
                                    <div class="absolute top-3 left-3 px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-violet-600 text-white shadow-lg">
                                        <?= htmlspecialchars($product['badge']) ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Wishlist Heart -->
                                <button type="button" data-product-id="<?= $product['id'] ?>" onclick="toggleWishlist(<?= $product['id'] ?>, this)" 
                                        class="btn-wishlist absolute top-3 right-3 w-9 h-9 rounded-full bg-zinc-900/80 backdrop-blur-md text-zinc-300 hover:text-rose-500 border border-zinc-700 flex items-center justify-center transition shadow" title="Wishlist">
                                    <i class="fa-regular fa-heart"></i>
                                </button>

                                <!-- Quick View Overlay -->
                                <div class="absolute inset-x-3 bottom-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex gap-2">
                                    <button type="button" onclick="openQuickView(<?= $product['id'] ?>)" class="flex-1 py-2 rounded-xl text-xs font-bold bg-zinc-900/90 text-white backdrop-blur-md border border-zinc-700 hover:bg-zinc-800 transition flex items-center justify-center gap-1.5 shadow-lg">
                                        <i class="fa-solid fa-eye text-violet-400"></i> Quick View
                                    </button>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="p-5 flex-1 flex flex-col justify-between">
                                <div>
                                    <span class="text-[10px] uppercase font-bold tracking-wider text-violet-400 mb-1 block">
                                        <?= htmlspecialchars($product['category_name'] ?? 'Luxury') ?>
                                    </span>
                                    <h3 class="text-sm font-bold text-zinc-900 dark:text-white line-clamp-1 group-hover:text-violet-400 transition">
                                        <a href="product-details.php?id=<?= $product['id'] ?>"><?= htmlspecialchars($product['name']) ?></a>
                                    </h3>
                                    <div class="flex items-center gap-2 mt-2 mb-3">
                                        <div class="flex text-amber-400 text-xs">
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star-half-stroke"></i>
                                        </div>
                                        <span class="text-[11px] text-zinc-500">(<?= $product['reviews_count'] ?>)</span>
                                    </div>
                                </div>

                                <div class="pt-3 border-t border-zinc-200 dark:border-zinc-800/80 flex items-center justify-between gap-2">
                                    <div>
                                        <span class="text-base font-extrabold text-zinc-900 dark:text-white"><?= formatPrice($product['price']) ?></span>
                                        <?php if (!empty($product['original_price'])): ?>
                                            <span class="text-xs text-zinc-500 line-through block"><?= formatPrice($product['original_price']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <button type="button" onclick="handleAddToCart(<?= $product['id'] ?>, 1, this)" class="p-2.5 rounded-xl bg-violet-600 hover:bg-violet-500 text-white shadow-md shadow-violet-600/30 transition flex items-center justify-center" title="Add to Bag">
                                        <i class="fa-solid fa-bag-shopping text-sm"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
