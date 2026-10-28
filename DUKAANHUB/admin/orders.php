<?php
$adminPageTitle = 'Orders Management';
require_once __DIR__ . '/header.php';

// Handle Order Status Update
$updateMsg = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $orderId = (int)$_POST['order_id'];
    $newStatus = trim($_POST['status'] ?? 'Processing');
    
    $upStmt = $pdo->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
    $upStmt->execute([$newStatus, $orderId]);
    $updateMsg = "Order status updated to \"$newStatus\" successfully!";
}

$orders = $pdo->query("SELECT * FROM orders ORDER BY id DESC")->fetchAll();
?>

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-white">Orders & Shipments</h1>
            <p class="text-xs text-zinc-400 mt-1">Manage fulfillment, customer deliveries, and status updates</p>
        </div>
        <span class="px-4 py-2 rounded-xl bg-zinc-900 border border-zinc-800 text-xs font-bold text-violet-400">
            Total Orders: <?= count($orders) ?>
        </span>
    </div>

    <?php if ($updateMsg): ?>
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-base"></i>
            <?= htmlspecialchars($updateMsg) ?>
        </div>
    <?php endif; ?>

    <!-- Orders Table -->
    <div class="rounded-3xl glass-card border border-zinc-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-zinc-400 uppercase tracking-wider border-b border-zinc-800 bg-zinc-950/40">
                        <th class="py-3 px-4">Order ID</th>
                        <th class="py-3 px-4">Customer Details</th>
                        <th class="py-3 px-4">Date</th>
                        <th class="py-3 px-4">Amount</th>
                        <th class="py-3 px-4">Payment</th>
                        <th class="py-3 px-4">Status & Update</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    <?php foreach ($orders as $ord): ?>
                        <tr class="hover:bg-zinc-900/60 transition">
                            <td class="py-4 px-4 font-mono font-bold text-white"><?= htmlspecialchars($ord['order_number']) ?></td>
                            <td class="py-4 px-4">
                                <p class="font-bold text-white"><?= htmlspecialchars($ord['customer_name']) ?></p>
                                <p class="text-[11px] text-zinc-400"><?= htmlspecialchars($ord['customer_email']) ?></p>
                                <p class="text-[10px] text-zinc-500 truncate max-w-xs"><?= htmlspecialchars($ord['shipping_address']) ?></p>
                            </td>
                            <td class="py-4 px-4 text-zinc-400"><?= date('M d, Y - h:i A', strtotime($ord['created_at'])) ?></td>
                            <td class="py-4 px-4 font-extrabold text-emerald-400 text-sm"><?= formatPrice($ord['total']) ?></td>
                            <td class="py-4 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-zinc-800 text-zinc-300">
                                    <?= htmlspecialchars($ord['payment_method']) ?>
                                </span>
                                <span class="block text-[10px] text-zinc-500 mt-0.5"><?= htmlspecialchars($ord['payment_status']) ?></span>
                            </td>
                            <td class="py-4 px-4">
                                <form method="POST" action="orders.php" class="flex items-center gap-2">
                                    <input type="hidden" name="order_id" value="<?= $ord['id'] ?>">
                                    <select name="status" onchange="this.form.submit()" class="px-2.5 py-1.5 rounded-lg bg-zinc-900 border border-zinc-700 text-[11px] text-white focus:outline-none focus:border-violet-500 font-semibold">
                                        <option value="Pending" <?= $ord['order_status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="Processing" <?= $ord['order_status'] === 'Processing' ? 'selected' : '' ?>>Processing</option>
                                        <option value="Shipped" <?= $ord['order_status'] === 'Shipped' ? 'selected' : '' ?>>Shipped</option>
                                        <option value="Delivered" <?= $ord['order_status'] === 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                                        <option value="Cancelled" <?= $ord['order_status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                    </select>
                                    <input type="hidden" name="update_status" value="1">
                                </form>
                            </td>
                            <td class="py-4 px-4 text-right">
                                <a href="../order-success.php?order_number=<?= htmlspecialchars($ord['order_number']) ?>" target="_blank" class="px-3 py-1.5 rounded-lg bg-zinc-800 hover:bg-zinc-700 text-zinc-200 border border-zinc-700 font-semibold transition inline-flex items-center gap-1.5">
                                    <i class="fa-solid fa-receipt text-violet-400"></i> Invoice
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
