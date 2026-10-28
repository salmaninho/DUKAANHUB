<?php
$pageTitle = 'Shopping Bag';
require_once __DIR__ . '/includes/header.php';

$cartItems = getCartItems();
$totals = calculateCartTotals();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-zinc-900 dark:text-white">Your Shopping Bag</h1>
            <p class="text-xs text-zinc-400 mt-1">Review your luxury selections before checkout</p>
        </div>
        <?php if (!empty($cartItems)): ?>
            <a href="shop.php" class="text-xs font-semibold text-violet-400 hover:text-violet-300 flex items-center gap-1">
                <i class="fa-solid fa-arrow-left"></i> Continue Shopping
            </a>
        <?php endif; ?>
    </div>

    <?php if (empty($cartItems)): ?>
        <div class="text-center py-20 rounded-3xl glass-card border border-zinc-800 max-w-2xl mx-auto">
            <div class="w-20 h-20 rounded-full bg-violet-600/10 text-violet-400 flex items-center justify-center text-3xl mx-auto mb-4 border border-violet-500/20">
                <i class="fa-solid fa-bag-shopping"></i>
            </div>
            <h2 class="text-xl font-bold text-white mb-2">Your shopping bag is currently empty</h2>
            <p class="text-xs text-zinc-400 mb-6 max-w-sm mx-auto">Explore our high-end audio, flagship laptops, timepieces, and exclusive streetwear collection.</p>
            <a href="shop.php" class="px-8 py-3.5 rounded-2xl bg-gradient-to-r from-violet-600 to-indigo-600 text-white font-bold text-xs shadow-lg shadow-violet-600/30 transition">
                Start Shopping Now
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Items Table/List -->
            <div class="lg:col-span-8 space-y-4">
                <div class="rounded-3xl glass-card border border-zinc-800 overflow-hidden">
                    <div class="p-6 border-b border-zinc-800 flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-zinc-400">Items (<?= $totals['itemCount'] ?>)</span>
                        <a href="javascript:void(0)" onclick="if(confirm('Clear all items from your bag?')) { fetch('api/cart.php', {method:'POST', body:'action=clear'}).then(()=>location.reload()); }" class="text-xs text-rose-400 hover:underline">
                            Clear Bag
                        </a>
                    </div>

                    <div class="divide-y divide-zinc-800">
                        <?php foreach ($cartItems as $item): ?>
                            <div class="p-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                                <div class="flex items-center gap-4 w-full sm:w-auto">
                                    <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="w-20 h-20 rounded-2xl object-cover bg-zinc-900 border border-zinc-800">
                                    <div class="min-w-0">
                                        <h3 class="text-sm font-bold text-white truncate max-w-xs">
                                            <a href="product-details.php?id=<?= $item['id'] ?>" class="hover:text-violet-400"><?= htmlspecialchars($item['name']) ?></a>
                                        </h3>
                                        <p class="text-xs text-violet-400 font-extrabold mt-1"><?= formatPrice($item['price']) ?></p>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between sm:justify-end gap-6 w-full sm:w-auto">
                                    <!-- Quantity Stepper -->
                                    <div class="flex items-center border border-zinc-700 rounded-xl overflow-hidden bg-zinc-900">
                                        <button type="button" onclick="updateCartItemQty(<?= $item['id'] ?>, -1)" class="px-3 py-1.5 text-zinc-400 hover:text-white transition font-bold">-</button>
                                        <input id="cart-qty-<?= $item['id'] ?>" type="number" readonly value="<?= $item['quantity'] ?>" class="w-12 text-center bg-transparent text-white border-0 focus:outline-none text-xs font-bold">
                                        <button type="button" onclick="updateCartItemQty(<?= $item['id'] ?>, 1)" class="px-3 py-1.5 text-zinc-400 hover:text-white transition font-bold">+</button>
                                    </div>

                                    <!-- Item Subtotal -->
                                    <span class="text-sm font-extrabold text-white w-20 text-right">
                                        <?= formatPrice((float)$item['price'] * (int)$item['quantity']) ?>
                                    </span>

                                    <!-- Remove Button -->
                                    <button type="button" onclick="removeCartItemAjax(<?= $item['id'] ?>)" class="text-zinc-500 hover:text-rose-400 p-2 transition" title="Remove Item">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Promo Coupon Bar -->
                <div class="p-6 rounded-3xl glass-card border border-zinc-800 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-ticket text-2xl text-amber-400"></i>
                        <div>
                            <h4 class="text-xs font-bold text-white">Have a Promo Coupon?</h4>
                            <p class="text-[11px] text-zinc-400">Try <strong class="text-amber-400">SALMAN20</strong> for 20% off your entire order!</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <input id="coupon-code-input" type="text" placeholder="SALMAN20" value="<?= htmlspecialchars($totals['couponCode'] ?? '') ?>" class="px-4 py-2.5 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white uppercase focus:outline-none focus:border-violet-500">
                        <button type="button" onclick="applyPromoCoupon()" class="px-5 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-500 text-white font-bold text-xs transition">
                            Apply
                        </button>
                    </div>
                </div>
            </div>

            <!-- Order Summary Card -->
            <div class="lg:col-span-4">
                <div class="p-6 rounded-3xl glass-card border border-zinc-800 space-y-5 sticky top-28">
                    <h3 class="text-base font-bold text-white border-b border-zinc-800 pb-4">Order Summary</h3>

                    <div class="space-y-3 text-xs">
                        <div class="flex justify-between text-zinc-400">
                            <span>Bag Subtotal:</span>
                            <span class="text-white font-semibold"><?= formatPrice($totals['subtotal']) ?></span>
                        </div>

                        <?php if ($totals['discount'] > 0): ?>
                            <div class="flex justify-between text-emerald-400">
                                <span>Discount (<?= $totals['couponCode'] ?> - <?= $totals['discountPercent'] ?>%):</span>
                                <span class="font-semibold">-<?= formatPrice($totals['discount']) ?></span>
                            </div>
                        <?php endif; ?>

                        <div class="flex justify-between text-zinc-400">
                            <span>Estimated Shipping:</span>
                            <span class="text-white font-semibold">
                                <?= $totals['shipping'] == 0 ? '<span class="text-emerald-400 uppercase font-bold">FREE</span>' : formatPrice($totals['shipping']) ?>
                            </span>
                        </div>

                        <div class="border-t border-zinc-800 pt-3 flex justify-between text-base font-extrabold text-white">
                            <span>Estimated Total:</span>
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-violet-400 to-cyan-400">
                                <?= formatPrice($totals['total']) ?>
                            </span>
                        </div>
                    </div>

                    <a href="checkout.php" class="w-full py-4 rounded-2xl bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white font-bold text-xs uppercase tracking-wider text-center block shadow-xl shadow-violet-600/30 transition">
                        Proceed To Checkout <i class="fa-solid fa-arrow-right ml-1.5"></i>
                    </a>

                    <div class="text-center pt-2">
                        <span class="text-[10px] text-zinc-500 flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-lock text-emerald-400"></i> Encrypted 256-Bit SSL Checkout
                        </span>
                    </div>
                </div>
            </div>

        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
