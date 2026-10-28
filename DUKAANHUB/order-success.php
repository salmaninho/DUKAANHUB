<?php
$pageTitle = 'Order Confirmed';
require_once __DIR__ . '/includes/header.php';

$orderNumber = trim($_GET['order_number'] ?? '');
$pdo = getDB();

$stmt = $pdo->prepare("SELECT * FROM orders WHERE order_number = ?");
$stmt->execute([$orderNumber]);
$order = $stmt->fetch();

if (!$order) {
    echo "<div class='max-w-xl mx-auto my-20 p-8 glass-panel text-center rounded-3xl'><h2 class='text-2xl font-bold text-white mb-4'>Order Not Found</h2><a href='index.php' class='px-6 py-2.5 rounded-xl bg-violet-600 text-white font-bold text-sm'>Return to Store</a></div>";
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

// Fetch Items
$itemStmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$itemStmt->execute([$order['id']]);
$items = $itemStmt->fetchAll();
?>

<div class="max-w-4xl mx-auto px-4 py-12">
    <!-- Success Celebration Header -->
    <div class="text-center mb-10">
        <div class="w-20 h-20 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 flex items-center justify-center text-3xl mx-auto mb-4 animate-bounce">
            <i class="fa-solid fa-check"></i>
        </div>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-zinc-900 dark:text-white">Order Successfully Placed!</h1>
        <p class="text-sm text-zinc-400 mt-2">
            Thank you, <strong class="text-white"><?= htmlspecialchars($order['customer_name']) ?></strong>! Your order reference is <span class="font-mono font-bold text-violet-400"><?= htmlspecialchars($order['order_number']) ?></span>
        </p>
    </div>

    <!-- Official Printable Invoice Card -->
    <div id="invoice-card" class="rounded-3xl glass-card border border-zinc-800 overflow-hidden shadow-2xl p-6 sm:p-10 space-y-8 bg-zinc-900/90">
        
        <!-- Invoice Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-zinc-800 gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-violet-600 to-cyan-500 flex items-center justify-center text-white text-xl">
                    <i class="fa-solid fa-gem"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white">SALMAN SAHANE</h2>
                    <p class="text-xs text-zinc-400">Official Purchase Invoice</p>
                </div>
            </div>
            <div class="text-left sm:text-right text-xs text-zinc-400 space-y-1">
                <p><strong class="text-zinc-200">Date:</strong> <?= date('F d, Y - h:i A', strtotime($order['created_at'])) ?></p>
                <p><strong class="text-zinc-200">Order ID:</strong> <?= htmlspecialchars($order['order_number']) ?></p>
                <p><strong class="text-zinc-200">Payment:</strong> <?= htmlspecialchars($order['payment_method']) ?> (<?= htmlspecialchars($order['payment_status']) ?>)</p>
            </div>
        </div>

        <!-- Customer & Shipping Summary -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs text-zinc-300">
            <div class="p-4 rounded-2xl bg-zinc-950/60 border border-zinc-800 space-y-1">
                <span class="font-bold text-violet-400 uppercase text-[10px] tracking-wider block mb-1">Customer Info</span>
                <p class="font-bold text-sm text-white"><?= htmlspecialchars($order['customer_name']) ?></p>
                <p class="text-zinc-400"><?= htmlspecialchars($order['customer_email']) ?></p>
                <p class="text-zinc-400"><?= htmlspecialchars($order['customer_phone']) ?></p>
            </div>
            <div class="p-4 rounded-2xl bg-zinc-950/60 border border-zinc-800 space-y-1">
                <span class="font-bold text-cyan-400 uppercase text-[10px] tracking-wider block mb-1">Shipping Details</span>
                <p class="text-white"><?= htmlspecialchars($order['shipping_address']) ?></p>
                <div class="pt-2">
                    <?= renderOrderStatusBadge($order['order_status']) ?>
                </div>
            </div>
        </div>

        <!-- Order Items Breakdown -->
        <div>
            <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-400 mb-3">Purchased Items</h3>
            <div class="rounded-2xl border border-zinc-800 overflow-hidden divide-y divide-zinc-800 text-xs">
                <?php foreach ($items as $item): ?>
                    <div class="p-4 flex items-center justify-between gap-4 bg-zinc-950/40">
                        <div class="flex items-center gap-3">
                            <img src="<?= htmlspecialchars($item['product_image']) ?>" alt="" class="w-12 h-12 rounded-xl object-cover border border-zinc-800">
                            <div>
                                <h4 class="font-bold text-white text-xs"><?= htmlspecialchars($item['product_name']) ?></h4>
                                <span class="text-zinc-400"><?= formatPrice($item['price']) ?> × <?= $item['quantity'] ?></span>
                            </div>
                        </div>
                        <span class="font-bold text-white text-sm"><?= formatPrice($item['total']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Totals Calculation -->
        <div class="border-t border-zinc-800 pt-4 flex justify-end">
            <div class="w-full sm:w-64 space-y-2 text-xs">
                <div class="flex justify-between text-zinc-400">
                    <span>Subtotal:</span>
                    <span class="text-white font-semibold"><?= formatPrice($order['subtotal']) ?></span>
                </div>
                <?php if ($order['discount'] > 0): ?>
                    <div class="flex justify-between text-emerald-400">
                        <span>VIP Discount:</span>
                        <span class="font-semibold">-<?= formatPrice($order['discount']) ?></span>
                    </div>
                <?php endif; ?>
                <div class="flex justify-between text-zinc-400">
                    <span>Shipping Fee:</span>
                    <span class="text-white font-semibold"><?= $order['shipping_fee'] == 0 ? '<span class="text-emerald-400 uppercase">FREE</span>' : formatPrice($order['shipping_fee']) ?></span>
                </div>
                <div class="border-t border-zinc-800 pt-2 flex justify-between text-sm font-extrabold text-white">
                    <span>Grand Total:</span>
                    <span class="text-emerald-400 text-base"><?= formatPrice($order['total']) ?></span>
                </div>
            </div>
        </div>

    </div>

    <!-- Actions Bar -->
    <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
        <button type="button" onclick="window.print()" class="px-6 py-3 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-white font-bold text-xs border border-zinc-700 transition flex items-center gap-2 shadow">
            <i class="fa-solid fa-print"></i> Print Official Invoice
        </button>
        <a href="my-orders.php" class="px-6 py-3 rounded-xl bg-violet-600 hover:bg-violet-500 text-white font-bold text-xs shadow-lg shadow-violet-600/30 transition flex items-center gap-2">
            <i class="fa-solid fa-truck"></i> View in My Orders
        </a>
        <a href="shop.php" class="px-6 py-3 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-zinc-300 font-bold text-xs border border-zinc-800 transition">
            Continue Shopping
        </a>
    </div>
</div>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #invoice-card, #invoice-card * {
        visibility: visible;
    }
    #invoice-card {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        background: #fff !important;
        color: #000 !important;
        border: none !important;
    }
    #invoice-card * {
        color: #000 !important;
    }
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
