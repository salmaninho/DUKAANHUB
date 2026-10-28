    </main>
    <!-- Main Content Area Ends -->

    <!-- Quick View Product Modal -->
    <div id="quickview-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 backdrop-blur-md p-4 transition-all">
        <div class="relative w-full max-w-3xl glass-panel rounded-3xl border theme-border shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-200">
            <button type="button" onclick="closeQuickView()" class="absolute top-4 right-4 z-10 w-9 h-9 rounded-full bg-slate-200 dark:bg-slate-800 text-slate-500 hover:text-slate-900 dark:hover:text-white flex items-center justify-center transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <div id="quickview-modal-content">
                <!-- Dynamically Populated by JavaScript -->
            </div>
        </div>
    </div>

    <!-- Sliding Offcanvas Cart Drawer -->
    <div id="cart-drawer-backdrop" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden transition-opacity" onclick="toggleCartDrawer()"></div>
    <div id="cart-drawer" class="fixed top-0 right-0 h-full w-full max-w-md border-l theme-border shadow-2xl z-50 translate-x-full transition-transform duration-300 flex flex-col">
        <div class="p-5 border-b theme-border flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-bag-shopping text-violet-500 text-lg"></i>
                <h3 class="font-bold text-base theme-text-title">Your Shopping Bag</h3>
                <span class="cart-badge px-2 py-0.5 rounded-full text-xs font-semibold bg-violet-600/20 text-violet-600 dark:text-violet-300 border border-violet-500/30"><?= $cartCount ?></span>
            </div>
            <button type="button" onclick="toggleCartDrawer()" class="p-2 text-slate-400 hover:text-slate-700 dark:hover:text-white rounded-lg transition">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Drawer Body -->
        <div class="flex-1 overflow-y-auto p-5 space-y-3" id="drawer-items-list">
            <?php 
            $drawerItems = getCartItems();
            $totals = calculateCartTotals();
            if (empty($drawerItems)): 
            ?>
                <div class="py-16 text-center text-slate-400">
                    <i class="fa-solid fa-bag-shopping text-5xl mb-3 text-slate-300 dark:text-slate-600"></i>
                    <p class="text-sm font-semibold theme-text-title">Your bag is empty</p>
                    <p class="text-xs text-slate-400 mt-1">Explore DukaanHub's curated luxury collection</p>
                    <a href="shop.php" onclick="toggleCartDrawer()" class="inline-block mt-4 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-violet-600 hover:bg-violet-500 transition">Start Shopping</a>
                </div>
            <?php else: ?>
                <?php foreach ($drawerItems as $item): ?>
                    <div class="flex gap-3 p-3 rounded-2xl glass-card border theme-border items-center">
                        <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="w-14 h-14 rounded-xl object-cover border theme-border">
                        <div class="flex-1 min-w-0">
                            <h4 class="text-xs font-bold theme-text-title truncate"><?= htmlspecialchars($item['name']) ?></h4>
                            <p class="text-xs text-emerald-600 dark:text-emerald-400 font-extrabold mt-0.5"><?= formatPrice($item['price']) ?> x <?= $item['quantity'] ?></p>
                        </div>
                        <button type="button" onclick="removeCartItemAjax(<?= $item['id'] ?>)" class="text-slate-400 hover:text-rose-500 p-2 transition" title="Remove">
                            <i class="fa-regular fa-trash-can"></i>
                        </button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Drawer Footer -->
        <?php if (!empty($drawerItems)): ?>
        <div class="p-5 border-t theme-border space-y-3">
            <div class="flex justify-between text-sm">
                <span class="theme-text-muted">Subtotal:</span>
                <span class="font-extrabold theme-text-title"><?= formatPrice($totals['subtotal']) ?></span>
            </div>
            <div class="flex gap-2">
                <a href="cart.php" class="flex-1 py-2.5 px-3 rounded-xl border theme-border text-center text-xs font-bold theme-text-title hover:bg-slate-100 dark:hover:bg-slate-800 transition">View Bag</a>
                <a href="checkout.php" class="flex-1 py-2.5 px-3 rounded-xl bg-gradient-to-r from-violet-600 to-indigo-600 text-center text-xs font-bold text-white hover:from-violet-500 hover:to-indigo-500 shadow-md shadow-violet-600/30 transition">Checkout</a>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Modern Luxury Footer -->
    <footer class="mt-16 border-t theme-border transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Col 1: About DukaanHub -->
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <img src="assets/images/logo.svg" alt="DukaanHub" class="h-10 w-auto block dark:hidden" style="max-width:160px;">
                        <img src="assets/images/logo-dark.svg" alt="DukaanHub" class="h-10 w-auto hidden dark:block" style="max-width:160px;">
                    </div>
                    <p class="text-xs theme-text-muted leading-relaxed">
                        The ultimate destination for premium laptops, spatial VR hardware, titanium timepieces, and exclusive designer fashion.
                    </p>
                    <div class="text-xs text-slate-500 space-y-1 pt-1">
                        <p><i class="fa-solid fa-envelope text-violet-500 mr-1.5"></i> Salmansahaneabdi@gmail.com</p>
                        <p><i class="fa-solid fa-phone text-emerald-500 mr-1.5"></i> +252 61 387 8223</p>
                    </div>
                </div>

                <!-- Col 2: Categories -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Shop Collections</h4>
                    <ul class="space-y-2 text-xs theme-text-muted">
                        <li><a href="shop.php?category=electronics" class="hover:text-violet-500 transition">Electronics & Hardware</a></li>
                        <li><a href="shop.php?category=smart-watches" class="hover:text-violet-500 transition">Smart Watches & Wearables</a></li>
                        <li><a href="shop.php?category=footwear" class="hover:text-violet-500 transition">Footwear & Sneakers</a></li>
                        <li><a href="shop.php?category=fashion" class="hover:text-violet-500 transition">Fashion & Apparel</a></li>
                        <li><a href="shop.php?category=accessories" class="hover:text-violet-500 transition">Luxury Accessories</a></li>
                        <li><a href="shop.php?category=gaming" class="hover:text-violet-500 transition">Gaming & Spatial VR</a></li>
                    </ul>
                </div>

                <!-- Col 3: Customer Care & Quick Links -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Customer Service</h4>
                    <ul class="space-y-2 text-xs theme-text-muted">
                        <li><a href="my-orders.php" class="hover:text-violet-500 transition">Track Orders & Receipts</a></li>
                        <li><a href="cart.php" class="hover:text-violet-500 transition">Shopping Bag & Checkout</a></li>
                        <li><a href="shop.php?deal=1" class="hover:text-violet-500 transition">Flash Deals & Coupons</a></li>
                        <li><a href="forgot-password.php" class="hover:text-violet-500 transition">Reset Password (Verification)</a></li>
                        <li><a href="admin/index.php" class="hover:text-violet-500 transition">Admin Portal</a></li>
                    </ul>
                </div>

                <!-- Col 4: VIP Newsletter -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">VIP Newsletter</h4>
                    <p class="text-xs theme-text-muted mb-3">Subscribe to get secret drops and VIP announcements!</p>
                    <form onsubmit="event.preventDefault(); showToast('Subscribed! Welcome to DukaanHub VIP drops.', 'success'); this.reset();" class="space-y-2">
                        <input type="email" required placeholder="Enter your email" class="w-full px-3.5 py-2 rounded-xl border text-xs focus:outline-none focus:border-violet-500">
                        <button type="submit" class="w-full py-2.5 rounded-xl bg-violet-600 hover:bg-violet-500 text-white font-bold text-xs shadow-md transition">
                            <i class="fa-solid fa-paper-plane mr-1"></i> Subscribe Now
                        </button>
                    </form>
                </div>
            </div>

            <!-- Bottom Copyright & Payment Badges -->
            <div class="mt-10 pt-6 border-t theme-border flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
                <p>© <?= date('Y') ?> <strong class="text-violet-600 dark:text-violet-400">DukaanHub</strong>. All Rights Reserved. Contact: Salmansahaneabdi@gmail.com</p>
                <div class="flex items-center gap-4 text-lg text-slate-400">
                    <i class="fa-brands fa-cc-visa"></i>
                    <i class="fa-brands fa-cc-mastercard"></i>
                    <i class="fa-brands fa-cc-apple-pay"></i>
                    <i class="fa-brands fa-cc-paypal"></i>
                </div>
            </div>
        </div>
    </footer>

    <!-- Cart Drawer Toggle Script -->
    <script>
        function toggleCartDrawer() {
            const drawer = document.getElementById('cart-drawer');
            const backdrop = document.getElementById('cart-drawer-backdrop');
            if (drawer && backdrop) {
                const isOpen = !drawer.classList.contains('translate-x-full');
                if (isOpen) {
                    drawer.classList.add('translate-x-full');
                    backdrop.classList.add('hidden');
                } else {
                    drawer.classList.remove('translate-x-full');
                    backdrop.classList.remove('hidden');
                }
            }
        }
    </script>

    <!-- Main JavaScript File -->
    <script src="assets/js/main.js"></script>
</body>
</html>
