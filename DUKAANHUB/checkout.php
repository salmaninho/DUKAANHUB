<?php
require_once __DIR__ . '/includes/functions.php';

$cartItems = getCartItems();
$totals = calculateCartTotals();

if (empty($cartItems)) {
    header('Location: shop.php');
    exit;
}

$currentUser = getCurrentUser();
$error = null;

// Process Order Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $customerName = trim($_POST['customer_name'] ?? '');
    $customerEmail = trim($_POST['customer_email'] ?? '');
    $customerPhone = trim($_POST['customer_phone'] ?? '');
    $street = trim($_POST['street'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $postal = trim($_POST['postal'] ?? '');
    $paymentMethod = trim($_POST['payment_method'] ?? 'Credit Card');

    if (empty($customerName) || empty($customerEmail) || empty($street)) {
        $error = 'Please fill out all required shipping and contact details.';
    } else {
        $pdo = getDB();
        $orderNumber = 'ORD-' . strtoupper(substr(uniqid(), 7)) . rand(10, 99);
        $fullAddress = "$street, $city, $postal";
        $userId = $currentUser['id'] ?? null;
        $paymentStatus = ($paymentMethod === 'Cash on Delivery') ? 'Pending' : 'Paid';

        try {
            $pdo->beginTransaction();

            // 1. Insert Order
            $stmt = $pdo->prepare("INSERT INTO orders (order_number, user_id, customer_name, customer_email, customer_phone, shipping_address, payment_method, payment_status, order_status, subtotal, discount, shipping_fee, total) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Processing', ?, ?, ?, ?)");
            $stmt->execute([
                $orderNumber,
                $userId,
                $customerName,
                $customerEmail,
                $customerPhone,
                $fullAddress,
                $paymentMethod,
                $paymentStatus,
                $totals['subtotal'],
                $totals['discount'],
                $totals['shipping'],
                $totals['total']
            ]);

            $orderId = $pdo->lastInsertId();

            // 2. Insert Order Items & Update Product Stock
            $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, product_image, price, quantity, total) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stockStmt = $pdo->prepare("UPDATE products SET stock = MAX(0, stock - ?) WHERE id = ?");

            foreach ($cartItems as $item) {
                $itemTotal = (float)$item['price'] * (int)$item['quantity'];
                $itemStmt->execute([
                    $orderId,
                    $item['id'],
                    $item['name'],
                    $item['image'],
                    $item['price'],
                    $item['quantity'],
                    $itemTotal
                ]);

                $stockStmt->execute([(int)$item['quantity'], (int)$item['id']]);
            }

            $pdo->commit();

            // Clear Cart Session
            clearCart();

            // Redirect to Success Receipt Page
            header("Location: order-success.php?order_number=$orderNumber");
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Order placement failed: ' . $e->getMessage();
        }
    }
}

$pageTitle = 'Secure Checkout';
require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-zinc-900 dark:text-white">Secure Checkout</h1>
        <p class="text-xs text-zinc-400 mt-1">Provide your delivery information to complete your order</p>
    </div>

    <?php if ($error): ?>
        <div class="p-4 mb-6 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-xs flex items-center gap-2">
            <i class="fa-solid fa-triangle-exclamation text-base"></i>
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="checkout.php">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Left: Checkout Forms -->
            <div class="lg:col-span-8 space-y-6">
                
                <!-- Contact Information -->
                <div class="p-6 rounded-3xl glass-card border border-zinc-800 space-y-4">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2 border-b border-zinc-800 pb-3">
                        <i class="fa-solid fa-user-check text-violet-400"></i> Contact & Customer Details
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-zinc-300 mb-1">Full Name *</label>
                            <input type="text" name="customer_name" required value="<?= htmlspecialchars($currentUser['name'] ?? 'Salman Sahane Abdi') ?>" class="w-full px-4 py-2.5 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-violet-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-zinc-300 mb-1">Email Address *</label>
                            <input type="email" name="customer_email" required value="<?= htmlspecialchars($currentUser['email'] ?? 'salman@shop.com') ?>" class="w-full px-4 py-2.5 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-violet-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-300 mb-1">Phone Number *</label>
                        <input type="tel" name="customer_phone" required value="<?= htmlspecialchars($currentUser['phone'] ?? '+252 61 387 8223') ?>" class="w-full px-4 py-2.5 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-violet-500">
                    </div>
                </div>

                <!-- Shipping Address -->
                <div class="p-6 rounded-3xl glass-card border border-zinc-800 space-y-4">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2 border-b border-zinc-800 pb-3">
                        <i class="fa-solid fa-truck-ramp-box text-cyan-400"></i> Shipping Address
                    </h3>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-300 mb-1">Street Address *</label>
                        <input type="text" name="street" required value="<?= htmlspecialchars($currentUser['address'] ?? '777 Luxury Blvd, Suite 100') ?>" class="w-full px-4 py-2.5 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-violet-500">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-zinc-300 mb-1">City *</label>
                            <input type="text" name="city" required value="Silicon Valley" class="w-full px-4 py-2.5 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-violet-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-zinc-300 mb-1">Postal / ZIP Code *</label>
                            <input type="text" name="postal" required value="94016" class="w-full px-4 py-2.5 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-violet-500">
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="p-6 rounded-3xl glass-card border border-zinc-800 space-y-4">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2 border-b border-zinc-800 pb-3">
                        <i class="fa-solid fa-credit-card text-emerald-400"></i> Payment Method
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <label class="cursor-pointer p-4 rounded-2xl bg-zinc-900 border border-violet-500/40 flex flex-col items-center text-center gap-2 hover:border-violet-400 transition">
                            <input type="radio" name="payment_method" value="Credit Card" checked class="text-violet-600 focus:ring-violet-500">
                            <i class="fa-solid fa-credit-card text-xl text-violet-400"></i>
                            <span class="text-xs font-bold text-white">Credit Card</span>
                        </label>
                        <label class="cursor-pointer p-4 rounded-2xl bg-zinc-900 border border-zinc-700 flex flex-col items-center text-center gap-2 hover:border-violet-400 transition">
                            <input type="radio" name="payment_method" value="PayPal" class="text-violet-600 focus:ring-violet-500">
                            <i class="fa-brands fa-paypal text-xl text-cyan-400"></i>
                            <span class="text-xs font-bold text-white">PayPal</span>
                        </label>
                        <label class="cursor-pointer p-4 rounded-2xl bg-zinc-900 border border-zinc-700 flex flex-col items-center text-center gap-2 hover:border-violet-400 transition">
                            <input type="radio" name="payment_method" value="Cash on Delivery" class="text-violet-600 focus:ring-violet-500">
                            <i class="fa-solid fa-hand-holding-dollar text-xl text-amber-400"></i>
                            <span class="text-xs font-bold text-white">Cash On Delivery</span>
                        </label>
                    </div>

                    <!-- Dummy Card Input Details for Visual Realism -->
                    <div id="card-fields" class="space-y-3 pt-2">
                        <div>
                            <label class="block text-xs font-semibold text-zinc-300 mb-1">Card Number (Simulated)</label>
                            <input type="text" placeholder="4532 •••• •••• 8892" value="4532 9912 8841 8892" class="w-full px-4 py-2.5 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-violet-500 font-mono">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-zinc-300 mb-1">Expiry Date</label>
                                <input type="text" placeholder="MM/YY" value="08/29" class="w-full px-4 py-2.5 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-violet-500 font-mono">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-zinc-300 mb-1">Security Code (CVV)</label>
                                <input type="password" placeholder="•••" value="882" class="w-full px-4 py-2.5 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-violet-500 font-mono">
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right: Order Summary Confirmation -->
            <div class="lg:col-span-4">
                <div class="p-6 rounded-3xl glass-card border border-zinc-800 space-y-5 sticky top-28">
                    <h3 class="text-base font-bold text-white border-b border-zinc-800 pb-4">In Your Bag</h3>

                    <div class="max-h-60 overflow-y-auto space-y-3 pr-2 divide-y divide-zinc-800">
                        <?php foreach ($cartItems as $item): ?>
                            <div class="flex items-center gap-3 pt-2">
                                <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="w-12 h-12 rounded-xl object-cover border border-zinc-800">
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-xs font-semibold text-white truncate"><?= htmlspecialchars($item['name']) ?></h4>
                                    <p class="text-[11px] text-zinc-400">Qty: <?= $item['quantity'] ?></p>
                                </div>
                                <span class="text-xs font-bold text-white">
                                    <?= formatPrice((float)$item['price'] * (int)$item['quantity']) ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="border-t border-zinc-800 pt-3 space-y-2 text-xs">
                        <div class="flex justify-between text-zinc-400">
                            <span>Subtotal:</span>
                            <span class="text-white font-semibold"><?= formatPrice($totals['subtotal']) ?></span>
                        </div>
                        <?php if ($totals['discount'] > 0): ?>
                            <div class="flex justify-between text-emerald-400">
                                <span>Coupon (<?= $totals['couponCode'] ?>):</span>
                                <span class="font-semibold">-<?= formatPrice($totals['discount']) ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="flex justify-between text-zinc-400">
                            <span>Shipping:</span>
                            <span class="text-white font-semibold"><?= $totals['shipping'] == 0 ? '<span class="text-emerald-400 uppercase font-bold">FREE</span>' : formatPrice($totals['shipping']) ?></span>
                        </div>
                        <div class="border-t border-zinc-800 pt-3 flex justify-between text-base font-extrabold text-white">
                            <span>Total Due:</span>
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-violet-400 to-cyan-400">
                                <?= formatPrice($totals['total']) ?>
                            </span>
                        </div>
                    </div>

                    <button type="submit" name="place_order" class="w-full py-4 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-xs uppercase tracking-wider text-center shadow-xl shadow-emerald-600/30 transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-lock"></i> Authorize & Place Order (<?= formatPrice($totals['total']) ?>)
                    </button>

                    <p class="text-[10px] text-zinc-500 text-center">
                        By placing your order, you agree to Salman Sahane's Luxury Shopping Terms & Conditions.
                    </p>
                </div>
            </div>

        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
