<?php
require_once __DIR__ . '/includes/header.php';

$id = (int)($_GET['id'] ?? 0);
$pdo = getDB();

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $reviewerName = trim($_POST['user_name'] ?? '');
    $rating = (int)($_POST['rating'] ?? 5);
    $comment = trim($_POST['comment'] ?? '');

    if (!empty($reviewerName) && !empty($comment) && $id > 0) {
        $rStmt = $pdo->prepare("INSERT INTO reviews (product_id, user_name, rating, comment) VALUES (?, ?, ?, ?)");
        $rStmt->execute([$id, $reviewerName, $rating, $comment]);
        setFlash('success', 'Thank you! Your verified review has been posted.');
        header("Location: product-details.php?id=$id#reviews");
        exit;
    }
}

// Fetch Product
$stmt = $pdo->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug 
                      FROM products p 
                      LEFT JOIN categories c ON p.category_id = c.id 
                      WHERE p.id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    echo "<div class='max-w-xl mx-auto my-20 p-8 glass-panel text-center rounded-3xl'><h2 class='text-2xl font-bold text-white mb-4'>Product Not Found</h2><a href='shop.php' class='px-6 py-2.5 rounded-xl bg-violet-600 text-white font-bold text-sm'>Back to Shop</a></div>";
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$gallery = !empty($product['gallery']) ? json_decode($product['gallery'], true) : [$product['image']];

// Fetch Reviews
$revStmt = $pdo->prepare("SELECT * FROM reviews WHERE product_id = ? ORDER BY id DESC");
$revStmt->execute([$id]);
$reviews = $revStmt->fetchAll();

// Fetch Related Products
$relStmt = $pdo->prepare("SELECT * FROM products WHERE category_id = ? AND id != ? LIMIT 4");
$relStmt->execute([$product['category_id'], $id]);
$related = $relStmt->fetchAll();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-xs text-zinc-500 mb-6">
        <a href="index.php" class="hover:text-violet-400">Home</a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <a href="shop.php" class="hover:text-violet-400">Catalog</a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <a href="shop.php?category=<?= htmlspecialchars($product['category_slug'] ?? '') ?>" class="hover:text-violet-400 capitalize"><?= htmlspecialchars($product['category_name'] ?? 'Category') ?></a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <span class="text-zinc-300 truncate max-w-xs"><?= htmlspecialchars($product['name']) ?></span>
    </div>

    <!-- Product Main View -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 mb-16">
        
        <!-- Left: Gallery Showcase -->
        <div class="lg:col-span-7 space-y-4">
            <div class="rounded-3xl overflow-hidden bg-zinc-900 border border-zinc-800 aspect-square flex items-center justify-center p-4 relative group">
                <img id="main-product-image" src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-full h-full object-cover rounded-2xl shadow-2xl transition-all duration-300">
                <?php if (!empty($product['badge'])): ?>
                    <span class="absolute top-6 left-6 px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-violet-600 text-white shadow-lg">
                        <?= htmlspecialchars($product['badge']) ?>
                    </span>
                <?php endif; ?>
            </div>

            <!-- Thumbnail Selector -->
            <?php if (!empty($gallery) && count($gallery) > 1): ?>
                <div class="flex items-center gap-3 overflow-x-auto pb-2">
                    <?php foreach ($gallery as $imgUrl): ?>
                        <button type="button" onclick="document.getElementById('main-product-image').src = '<?= htmlspecialchars($imgUrl) ?>'" class="w-20 h-20 rounded-2xl overflow-hidden border-2 border-zinc-800 hover:border-violet-500 transition shrink-0 bg-zinc-900">
                            <img src="<?= htmlspecialchars($imgUrl) ?>" alt="Thumbnail" class="w-full h-full object-cover">
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right: Purchasing & Details -->
        <div class="lg:col-span-5 flex flex-col justify-between space-y-6">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-3 py-0.5 rounded-full text-xs font-bold bg-violet-500/10 text-violet-400 border border-violet-500/20">
                        <?= htmlspecialchars($product['category_name'] ?? 'Premium Collection') ?>
                    </span>
                    <span class="text-xs text-emerald-400 flex items-center gap-1 font-semibold">
                        <i class="fa-solid fa-circle-check"></i> In Stock (<?= $product['stock'] ?> units)
                    </span>
                </div>

                <h1 class="text-3xl sm:text-4xl font-extrabold text-zinc-900 dark:text-white leading-tight mb-3">
                    <?= htmlspecialchars($product['name']) ?>
                </h1>

                <!-- Rating -->
                <div class="flex items-center gap-3 mb-6">
                    <div class="flex text-amber-400 text-sm">
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star-half-stroke"></i>
                    </div>
                    <span class="text-xs font-bold text-zinc-300"><?= $product['rating'] ?></span>
                    <span class="text-zinc-500 text-xs">|</span>
                    <a href="#reviews" class="text-xs text-violet-400 hover:underline"><?= count($reviews) ?> Customer Reviews</a>
                </div>

                <!-- Price Box -->
                <div class="p-5 rounded-2xl glass-card border border-zinc-800 mb-6 flex items-baseline gap-4">
                    <span class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-violet-400 to-cyan-400">
                        <?= formatPrice($product['price']) ?>
                    </span>
                    <?php if (!empty($product['original_price'])): ?>
                        <span class="text-xl text-zinc-500 line-through"><?= formatPrice($product['original_price']) ?></span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30">
                            Save <?= formatPrice($product['original_price'] - $product['price']) ?>
                        </span>
                    <?php endif; ?>
                </div>

                <p class="text-zinc-400 text-sm leading-relaxed mb-6">
                    <?= nl2br(htmlspecialchars($product['description'])) ?>
                </p>

                <!-- Value Highlights -->
                <div class="grid grid-cols-2 gap-3 text-xs text-zinc-300 mb-6">
                    <div class="flex items-center gap-2 p-2.5 rounded-xl bg-zinc-900/60 border border-zinc-800">
                        <i class="fa-solid fa-truck-fast text-violet-400"></i> Free Worldwide Delivery
                    </div>
                    <div class="flex items-center gap-2 p-2.5 rounded-xl bg-zinc-900/60 border border-zinc-800">
                        <i class="fa-solid fa-award text-cyan-400"></i> 1-Year Official Warranty
                    </div>
                    <div class="flex items-center gap-2 p-2.5 rounded-xl bg-zinc-900/60 border border-zinc-800">
                        <i class="fa-solid fa-shield-halved text-emerald-400"></i> Authenticity Verified
                    </div>
                    <div class="flex items-center gap-2 p-2.5 rounded-xl bg-zinc-900/60 border border-zinc-800">
                        <i class="fa-solid fa-rotate-left text-amber-400"></i> 30-Day Money Back
                    </div>
                </div>
            </div>

            <!-- Action Buttons: Quantity, Add to Cart & Buy Now -->
            <div class="space-y-4 pt-4 border-t border-zinc-800">
                <div class="flex items-center gap-4">
                    <span class="text-sm font-semibold text-zinc-300">Select Quantity:</span>
                    <div class="flex items-center border border-zinc-700 rounded-2xl overflow-hidden bg-zinc-900">
                        <button type="button" onclick="let q = document.getElementById('details-qty'); if(q.value > 1) q.value--;" class="px-4 py-2 text-zinc-400 hover:text-white transition font-bold">-</button>
                        <input id="details-qty" type="number" value="1" min="1" max="<?= $product['stock'] ?>" class="w-14 text-center bg-transparent text-white border-0 focus:outline-none text-sm font-bold">
                        <button type="button" onclick="let q = document.getElementById('details-qty'); q.value++;" class="px-4 py-2 text-zinc-400 hover:text-white transition font-bold">+</button>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="button" onclick="handleAddToCart(<?= $product['id'] ?>, parseInt(document.getElementById('details-qty').value), this)" class="flex-1 py-4 px-6 rounded-2xl font-bold text-sm text-white bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 shadow-xl shadow-violet-600/30 transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-bag-shopping"></i> Add to Shopping Bag
                    </button>
                    
                    <button type="button" onclick="handleAddToCart(<?= $product['id'] ?>, parseInt(document.getElementById('details-qty').value)); window.location.href='checkout.php';" class="flex-1 py-4 px-6 rounded-2xl font-bold text-sm text-zinc-900 bg-white hover:bg-zinc-200 shadow-lg transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-bolt text-amber-500"></i> Buy Now Instantly
                    </button>

                    <button type="button" data-product-id="<?= $product['id'] ?>" onclick="toggleWishlist(<?= $product['id'] ?>, this)" class="btn-wishlist p-4 rounded-2xl border border-zinc-700 bg-zinc-800 text-zinc-300 hover:text-rose-500 transition" title="Save to Wishlist">
                        <i class="fa-regular fa-heart text-lg"></i>
                    </button>
                </div>
            </div>

        </div>
    </div>

    <!-- Customer Reviews & Submission Form -->
    <div id="reviews" class="pt-10 border-t border-zinc-800 grid grid-cols-1 lg:grid-cols-12 gap-12 mb-16">
        
        <!-- Left: Verified Reviews List -->
        <div class="lg:col-span-7 space-y-6">
            <div class="flex items-center justify-between">
                <h3 class="text-2xl font-bold text-white">Verified Customer Reviews</h3>
                <span class="text-xs text-zinc-400"><?= count($reviews) ?> total</span>
            </div>

            <?php if (empty($reviews)): ?>
                <div class="p-8 rounded-3xl glass-card text-center text-zinc-500">
                    <i class="fa-regular fa-comments text-4xl mb-2 text-zinc-600"></i>
                    <p>No reviews yet. Be the first to share your experience!</p>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($reviews as $rev): ?>
                        <div class="p-6 rounded-2xl glass-card border border-zinc-800/80">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-sm font-bold text-white flex items-center gap-2">
                                    <i class="fa-solid fa-circle-user text-violet-400"></i>
                                    <?= htmlspecialchars($rev['user_name']) ?>
                                </h4>
                                <div class="flex text-amber-400 text-xs">
                                    <?php for ($i = 0; $i < $rev['rating']; $i++): ?><i class="fa-solid fa-star"></i><?php endfor; ?>
                                </div>
                            </div>
                            <p class="text-xs text-zinc-300 leading-relaxed"><?= nl2br(htmlspecialchars($rev['comment'])) ?></p>
                            <span class="text-[10px] text-zinc-500 mt-2 block"><?= date('M d, Y', strtotime($rev['created_at'])) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right: Leave a Review Form -->
        <div class="lg:col-span-5">
            <div class="p-8 rounded-3xl glass-card border border-zinc-800">
                <h3 class="text-lg font-bold text-white mb-1">Write a Review</h3>
                <p class="text-xs text-zinc-400 mb-6">Share your impressions of this luxury product</p>

                <form method="POST" action="product-details.php?id=<?= $id ?>" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-zinc-300 mb-1">Your Full Name:</label>
                        <input type="text" name="user_name" required value="<?= htmlspecialchars($currentUser['name'] ?? '') ?>" placeholder="e.g. Salman Sahane" class="w-full px-4 py-2.5 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-violet-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-300 mb-1">Your Rating:</label>
                        <select name="rating" class="w-full px-4 py-2.5 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-amber-400 focus:outline-none focus:border-violet-500 font-bold">
                            <option value="5">⭐⭐⭐⭐⭐ 5 Stars (Outstanding)</option>
                            <option value="4">⭐⭐⭐⭐ 4 Stars (Very Good)</option>
                            <option value="3">⭐⭐⭐ 3 Stars (Average)</option>
                            <option value="2">⭐⭐ 2 Stars (Below Expectation)</option>
                            <option value="1">⭐ 1 Star (Poor)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-300 mb-1">Your Review Comment:</label>
                        <textarea name="comment" required rows="4" placeholder="How is the quality, shipping, and design?" class="w-full px-4 py-2.5 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-violet-500"></textarea>
                    </div>

                    <button type="submit" name="submit_review" class="w-full py-3 rounded-xl bg-violet-600 hover:bg-violet-500 text-white font-bold text-xs shadow-md transition">
                        Submit Verified Review
                    </button>
                </form>
            </div>
        </div>

    </div>

    <!-- Related Products -->
    <?php if (!empty($related)): ?>
        <div class="pt-10 border-t border-zinc-800">
            <h3 class="text-2xl font-bold text-white mb-6">You May Also Like</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($related as $rel): ?>
                    <div class="rounded-2xl glass-card overflow-hidden flex flex-col justify-between border border-zinc-800">
                        <div class="aspect-square bg-zinc-900 overflow-hidden relative">
                            <img src="<?= htmlspecialchars($rel['image']) ?>" alt="<?= htmlspecialchars($rel['name']) ?>" class="w-full h-full object-cover">
                        </div>
                        <div class="p-4">
                            <h4 class="text-xs font-bold text-white truncate mb-1">
                                <a href="product-details.php?id=<?= $rel['id'] ?>" class="hover:text-violet-400"><?= htmlspecialchars($rel['name']) ?></a>
                            </h4>
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-sm font-extrabold text-violet-400"><?= formatPrice($rel['price']) ?></span>
                                <button type="button" onclick="handleAddToCart(<?= $rel['id'] ?>, 1, this)" class="p-2 rounded-lg bg-zinc-800 hover:bg-violet-600 text-white transition text-xs">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
