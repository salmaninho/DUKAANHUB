<?php
$pageTitle = 'My Order History & Tracking';
require_once __DIR__ . '/includes/header.php';

$pdo = getDB();
$currentUser = getCurrentUser();

// Fetch orders: if logged in, filter by user_id or show recent orders
if ($currentUser) {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? OR customer_email = ? ORDER BY id DESC");
    $stmt->execute([$currentUser['id'], $currentUser['email']]);
} else {
    // Show all recent guest demo orders
    $stmt = $pdo->query("SELECT * FROM orders ORDER BY id DESC LIMIT 10");
}
$orders = $stmt->fetchAll();
?>

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-zinc-900 dark:text-white">Order Tracking & History</h1>
            <p class="text-xs text-zinc-400 mt-1">Track real-time shipment status and view printable invoices</p>
        </div>
        <a href="shop.php" class="px-5 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-500 text-white font-bold text-xs shadow-md transition w-max">
            <i class="fa-solid fa-plus mr-1"></i> Place New Order
        </a>
    </div>

    <?php if (empty($orders)): ?>
        <div class="p-12 text-center rounded-3xl glass-card border border-zinc-800">
            <i class="fa-solid fa-box-open text-4xl text-zinc-600 mb-3"></i>
            <h3 class="text-base font-bold text-white mb-1">No orders found</h3>
            <p class="text-xs text-zinc-400 mb-4">You have not placed any orders yet.</p>
            <a href="shop.php" class="px-6 py-2.5 rounded-xl bg-violet-600 text-white font-bold text-xs">Start Shopping</a>
        </div>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($orders as $ord): 
                $itemStmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
                $itemStmt->execute([$ord['id']]);
                $items = $itemStmt->fetchAll();
            ?>
                <div class="p-6 rounded-3xl glass-card border border-zinc-800 space-y-4 hover:border-violet-500/40 transition">
                    <!-- Order Top Row -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-zinc-800 gap-2">
                        <div class="flex items-center gap-3">
                            <span class="font-mono font-bold text-sm text-white"><?= htmlspecialchars($ord['order_number']) ?></span>
                            <span class="text-xs text-zinc-500">•</span>
                            <span class="text-xs text-zinc-400"><?= date('M d, Y', strtotime($ord['created_at'])) ?></span>
                        </div>
                        <div class="flex items-center gap-3">
                            <?= renderOrderStatusBadge($ord['order_status']) ?>
                            <a href="order-success.php?order_number=<?= htmlspecialchars($ord['order_number']) ?>" class="px-3 py-1.5 rounded-lg bg-zinc-800 hover:bg-zinc-700 text-xs font-semibold text-zinc-200 border border-zinc-700 transition">
                                <i class="fa-solid fa-receipt mr-1 text-violet-400"></i> Invoice
                            </a>
                        </div>
                    </div>

                    <!-- Items Preview -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        <?php foreach ($items as $item): ?>
                            <div class="flex items-center gap-3 p-2.5 rounded-xl bg-zinc-950/40 border border-zinc-800/80">
                                <img src="<?= htmlspecialchars($item['product_image']) ?>" alt="" class="w-10 h-10 rounded-lg object-cover">
                                <div class="min-w-0">
                                    <h4 class="text-xs font-semibold text-white truncate"><?= htmlspecialchars($item['product_name']) ?></h4>
                                    <span class="text-[11px] text-zinc-400"><?= formatPrice($item['price']) ?> × <?= $item['quantity'] ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Order Footer -->
                    <div class="pt-3 border-t border-zinc-800/80 flex items-center justify-between text-xs text-zinc-400">
                        <span>Paid via: <strong class="text-zinc-200"><?= htmlspecialchars($ord['payment_method']) ?></strong></span>
                        <span>Total Paid: <strong class="text-emerald-400 text-sm font-extrabold"><?= formatPrice($ord['total']) ?></strong></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
