<?php
$adminPageTitle = 'Manage Categories';
require_once __DIR__ . '/header.php';

$msg = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_category'])) {
    $name = trim($_POST['name'] ?? '');
    $icon = trim($_POST['icon'] ?? 'fa-tag');
    $description = trim($_POST['description'] ?? '');
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));

    if (!empty($name)) {
        $ins = $pdo->prepare("INSERT INTO categories (name, slug, icon, description) VALUES (?, ?, ?, ?)");
        $ins->execute([$name, $slug, $icon, $description]);
        $msg = "Category \"$name\" added successfully!";
    }
}

// Fetch categories with product counts
$categories = $pdo->query("SELECT c.*, COUNT(p.id) as product_count 
                         FROM categories c 
                         LEFT JOIN products p ON c.id = p.category_id 
                         GROUP BY c.id 
                         ORDER BY c.name ASC")->fetchAll();
?>

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-white">Product Categories</h1>
            <p class="text-xs text-zinc-400 mt-1">Organize luxury inventory by departmental collections</p>
        </div>
        <button type="button" onclick="document.getElementById('new-cat-modal').classList.remove('hidden')" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white font-bold text-xs shadow-lg shadow-violet-600/30 transition flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Add New Category
        </button>
    </div>

    <?php if ($msg): ?>
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-base"></i>
            <?= htmlspecialchars($msg) ?>
        </div>
    <?php endif; ?>

    <!-- Category Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($categories as $cat): ?>
            <div class="p-6 rounded-3xl glass-card border border-zinc-800 space-y-4 flex flex-col justify-between">
                <div class="flex items-start justify-between">
                    <div class="w-14 h-14 rounded-2xl bg-violet-600/10 border border-violet-500/20 text-violet-400 flex items-center justify-center text-2xl">
                        <i class="fa-solid <?= htmlspecialchars($cat['icon']) ?>"></i>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-zinc-800 text-zinc-300 border border-zinc-700">
                        <?= $cat['product_count'] ?> Products
                    </span>
                </div>

                <div>
                    <h3 class="text-lg font-bold text-white mb-1"><?= htmlspecialchars($cat['name']) ?></h3>
                    <p class="text-xs text-zinc-400 leading-relaxed"><?= htmlspecialchars($cat['description'] ?? 'Department collection') ?></p>
                </div>

                <div class="pt-3 border-t border-zinc-800/80 flex items-center justify-between text-xs">
                    <span class="text-zinc-500 font-mono text-[11px]">slug: <?= htmlspecialchars($cat['slug']) ?></span>
                    <a href="../shop.php?category=<?= urlencode($cat['slug']) ?>" target="_blank" class="text-violet-400 hover:underline flex items-center gap-1">
                        View Store <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Add Category Modal -->
<div id="new-cat-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 backdrop-blur-md p-4">
    <div class="max-w-md w-full rounded-3xl glass-panel border border-zinc-700 p-6 space-y-6">
        <div class="flex items-center justify-between border-b border-zinc-800 pb-4">
            <h3 class="text-base font-bold text-white">Add New Category</h3>
            <button type="button" onclick="document.getElementById('new-cat-modal').classList.add('hidden')" class="text-zinc-400 hover:text-white">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form method="POST" action="categories.php" class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-zinc-300 mb-1">Category Name *</label>
                <input type="text" name="name" required placeholder="e.g. Smart Audio" class="w-full px-4 py-2.5 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-violet-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-zinc-300 mb-1">FontAwesome Icon Class</label>
                <input type="text" name="icon" value="fa-headphones" class="w-full px-4 py-2.5 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-violet-500 font-mono">
            </div>

            <div>
                <label class="block text-xs font-semibold text-zinc-300 mb-1">Brief Description</label>
                <textarea name="description" rows="3" placeholder="Category highlights..." class="w-full px-4 py-2.5 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-violet-500"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('new-cat-modal').classList.add('hidden')" class="px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 text-xs font-semibold hover:bg-zinc-700 transition">
                    Cancel
                </button>
                <button type="submit" name="save_category" class="px-6 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-500 text-white font-bold text-xs shadow-md transition">
                    Create Category
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
