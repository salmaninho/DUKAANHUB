<?php
$adminPageTitle = 'Manage Products';
require_once __DIR__ . '/header.php';

// Handle Add / Edit / Delete
$msg = null;
$msgType = 'success';

// Delete Product
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    $delStmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $delStmt->execute([$delId]);
    $msg = 'Product deleted successfully!';
}

// Add New Product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_product'])) {
    $name = trim($_POST['name'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 1);
    $price = (float)($_POST['price'] ?? 0);
    $originalPrice = !empty($_POST['original_price']) ? (float)$_POST['original_price'] : null;
    $stock = (int)($_POST['stock'] ?? 10);
    $image = trim($_POST['image'] ?? '');
    $badge = trim($_POST['badge'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name))) . '-' . rand(100, 999);

    if (empty($name) || $price <= 0 || empty($image)) {
        $msg = 'Please provide product name, valid price, and image URL.';
        $msgType = 'error';
    } else {
        $ins = $pdo->prepare("INSERT INTO products (category_id, name, slug, description, price, original_price, stock, badge, image, gallery, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
        $gallery = json_encode([$image]);
        $ins->execute([$categoryId, $name, $slug, $description, $price, $originalPrice, $stock, $badge, $image, $gallery]);
        $msg = "Product \"$name\" added to catalog successfully!";
    }
}

// Fetch all products
$products = $pdo->query("SELECT p.*, c.name as category_name 
                         FROM products p 
                         LEFT JOIN categories c ON p.category_id = c.id 
                         ORDER BY p.id DESC")->fetchAll();

$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
?>

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-white">Product Catalog</h1>
            <p class="text-xs text-zinc-400 mt-1">Manage luxury store inventory, pricing, and high-resolution images</p>
        </div>
        <button type="button" onclick="document.getElementById('new-product-modal').classList.remove('hidden')" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white font-bold text-xs shadow-lg shadow-violet-600/30 transition flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Add New Product
        </button>
    </div>

    <?php if ($msg): ?>
        <div class="p-4 rounded-2xl <?= $msgType === 'error' ? 'bg-rose-500/10 border-rose-500/30 text-rose-400' : 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400' ?> border text-xs flex items-center gap-2">
            <i class="fa-solid <?= $msgType === 'error' ? 'fa-circle-exclamation' : 'fa-circle-check' ?> text-base"></i>
            <?= htmlspecialchars($msg) ?>
        </div>
    <?php endif; ?>

    <!-- Product Table -->
    <div class="rounded-3xl glass-card border border-zinc-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-zinc-400 uppercase tracking-wider border-b border-zinc-800 bg-zinc-950/40">
                        <th class="py-3 px-4">Item</th>
                        <th class="py-3 px-4">Category</th>
                        <th class="py-3 px-4">Price</th>
                        <th class="py-3 px-4">Stock</th>
                        <th class="py-3 px-4">Badge</th>
                        <th class="py-3 px-4">Rating</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    <?php foreach ($products as $p): ?>
                        <tr class="hover:bg-zinc-900/60 transition">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <img src="<?= htmlspecialchars($p['image']) ?>" alt="" class="w-12 h-12 rounded-xl object-cover border border-zinc-800">
                                    <div class="min-w-0">
                                        <h4 class="font-bold text-white text-xs truncate max-w-xs"><?= htmlspecialchars($p['name']) ?></h4>
                                        <span class="text-[10px] text-zinc-500">ID: #<?= $p['id'] ?></span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-zinc-300"><?= htmlspecialchars($p['category_name'] ?? 'General') ?></td>
                            <td class="py-3.5 px-4">
                                <span class="font-extrabold text-white"><?= formatPrice($p['price']) ?></span>
                                <?php if (!empty($p['original_price'])): ?>
                                    <span class="text-[10px] text-zinc-500 line-through block"><?= formatPrice($p['original_price']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3.5 px-4">
                                <?php if ($p['stock'] > 5): ?>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20"><?= $p['stock'] ?> in stock</span>
                                <?php else: ?>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">Low (<?= $p['stock'] ?> left)</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3.5 px-4">
                                <?php if (!empty($p['badge'])): ?>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-violet-600/30 text-violet-300 border border-violet-500/40"><?= htmlspecialchars($p['badge']) ?></span>
                                <?php else: ?>
                                    <span class="text-zinc-600">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="text-amber-400 font-bold"><i class="fa-solid fa-star text-[10px]"></i> <?= $p['rating'] ?></span>
                                <span class="text-zinc-500 text-[10px]">(<?= $p['reviews_count'] ?>)</span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="../product-details.php?id=<?= $p['id'] ?>" target="_blank" class="p-2 rounded-lg bg-zinc-800 hover:bg-zinc-700 text-zinc-300 transition" title="View Public Page">
                                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                    </a>
                                    <a href="products.php?delete=<?= $p['id'] ?>" onclick="return confirm('Are you sure you want to delete this product?');" class="p-2 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/20 transition" title="Delete">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Add New Product -->
<div id="new-product-modal" class="fixed inset-0 z-50 <?= isset($_GET['action']) && $_GET['action'] === 'new' ? '' : 'hidden' ?> items-center justify-center bg-black/80 backdrop-blur-md p-4">
    <div class="max-w-2xl w-full rounded-3xl glass-panel border border-zinc-700 p-6 sm:p-8 space-y-6 my-8 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-zinc-800 pb-4">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-plus text-violet-400"></i> Add New Product To Store
            </h3>
            <button type="button" onclick="document.getElementById('new-product-modal').classList.add('hidden')" class="text-zinc-400 hover:text-white">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form method="POST" action="products.php" class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-zinc-300 mb-1">Product Title *</label>
                <input type="text" name="name" required placeholder="e.g. Sony WH-1000XM5 Headphones" class="w-full px-4 py-2.5 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-violet-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-zinc-300 mb-1">Category *</label>
                    <select name="category_id" class="w-full px-4 py-2.5 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-violet-500">
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-300 mb-1">Badge (Optional)</label>
                    <input type="text" name="badge" placeholder="e.g. BESTSELLER, 20% OFF, NEW" class="w-full px-4 py-2.5 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-violet-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-zinc-300 mb-1">Price ($) *</label>
                    <input type="number" step="0.01" name="price" required placeholder="199.99" class="w-full px-4 py-2.5 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-violet-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-300 mb-1">Original Price ($)</label>
                    <input type="number" step="0.01" name="original_price" placeholder="249.99" class="w-full px-4 py-2.5 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-violet-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-300 mb-1">Stock Units</label>
                    <input type="number" name="stock" value="15" class="w-full px-4 py-2.5 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-violet-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-zinc-300 mb-1">Product High-Res Image URL *</label>
                <input type="url" name="image" required placeholder="https://images.unsplash.com/photo-..." class="w-full px-4 py-2.5 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-violet-500">
                <p class="text-[10px] text-zinc-500 mt-1">Paste any Unsplash image URL or shopping picture.</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-zinc-300 mb-1">Detailed Description</label>
                <textarea name="description" rows="3" placeholder="Enter specifications, materials, and highlights..." class="w-full px-4 py-2.5 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-violet-500"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3">
                <button type="button" onclick="document.getElementById('new-product-modal').classList.add('hidden')" class="px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 text-xs font-semibold hover:bg-zinc-700 transition">
                    Cancel
                </button>
                <button type="submit" name="save_product" class="px-6 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-500 text-white font-bold text-xs shadow-md transition">
                    Save Product
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
