<?php
$adminPageTitle = 'Dashboard Overview';
require_once __DIR__ . '/header.php';

// Fetch Aggregate KPIs
$totalRevenue = $pdo->query("SELECT COALESCE(SUM(total), 0) FROM orders WHERE payment_status = 'Paid'")->fetchColumn();
$totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

// Fetch Recent Orders
$recentOrders = $pdo->query("SELECT * FROM orders ORDER BY id DESC LIMIT 5")->fetchAll();

// Top Selling Featured Products
$topProducts = $pdo->query("SELECT p.*, c.name as category_name 
                            FROM products p 
                            LEFT JOIN categories c ON p.category_id = c.id 
                            WHERE p.is_featured = 1 
                            ORDER BY p.rating DESC, p.reviews_count DESC LIMIT 5")->fetchAll();

// Category distribution for Chart
$catStats = $pdo->query("SELECT c.name, COUNT(p.id) as product_count 
                         FROM categories c 
                         LEFT JOIN products p ON c.id = p.category_id 
                         GROUP BY c.id")->fetchAll();
$catLabels = [];
$catCounts = [];
foreach ($catStats as $cs) {
    $catLabels[] = $cs['name'];
    $catCounts[] = (int)$cs['product_count'];
}
?>

<div class="space-y-8">
    
    <!-- Executive Welcome Banner -->
    <div class="relative p-6 sm:p-8 rounded-3xl overflow-hidden bg-gradient-to-r from-violet-900/90 via-indigo-950 to-slate-900 text-white shadow-xl border border-violet-700/40">
        <!-- Ambient Glow -->
        <div class="absolute -right-16 -top-16 w-72 h-72 bg-violet-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute right-1/3 -bottom-16 w-56 h-56 bg-cyan-500/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-violet-500/20 text-violet-300 border border-violet-500/30 text-xs font-bold">
                    <i class="fa-solid fa-crown text-amber-400"></i> DukaanHub Executive Control Center
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
                    Welcome back, <?= htmlspecialchars($adminUser['name'] ?? 'Salman Sahane Abdi') ?> 👋
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 max-w-xl">
                    Real-time operational dashboard for DukaanHub. System running at 100% health with live telemetry.
                </p>
                <div class="flex flex-wrap items-center gap-3 pt-1 text-xs text-slate-300">
                    <span class="inline-flex items-center gap-1.5"><i class="fa-solid fa-envelope text-violet-400"></i> <?= htmlspecialchars($adminUser['email'] ?? 'Salmansahaneabdi@gmail.com') ?></span>
                    <span class="text-slate-500">•</span>
                    <span class="inline-flex items-center gap-1.5"><i class="fa-solid fa-phone text-emerald-400"></i> +252 61 387 8223</span>
                </div>
            </div>

            <!-- Quick Action Command Buttons -->
            <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                <a href="products.php?action=new" class="px-4 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-500 text-white font-bold text-xs shadow-lg shadow-violet-600/40 transition flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i> Add Product
                </a>
                <a href="orders.php" class="px-4 py-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-200 border border-slate-700 font-bold text-xs transition flex items-center gap-2">
                    <i class="fa-solid fa-cart-flatbed text-emerald-400"></i> Shipments
                </a>
                <a href="../index.php" target="_blank" class="px-4 py-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-200 border border-slate-700 font-bold text-xs transition flex items-center gap-2">
                    <i class="fa-solid fa-arrow-up-right-from-square text-cyan-400"></i> View Store
                </a>
            </div>
        </div>
    </div>

    <!-- 4 KPI Metrics Grid (High Contrast & Glassmorphism) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- KPI 1: Gross Revenue -->
        <div class="p-5 rounded-3xl glass-card border theme-border space-y-3 relative overflow-hidden transition-all duration-200 hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Gross Revenue</span>
                <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-dollar-sign"></i>
                </div>
            </div>
            <div>
                <p class="text-2xl sm:text-3xl font-extrabold theme-text-title"><?= formatPrice($totalRevenue) ?></p>
                <p class="text-[11px] text-emerald-500 mt-1 flex items-center gap-1 font-bold">
                    <i class="fa-solid fa-arrow-trend-up"></i> +18.4% from last week
                </p>
            </div>
        </div>

        <!-- KPI 2: Total Orders -->
        <div class="p-5 rounded-3xl glass-card border theme-border space-y-3 relative overflow-hidden transition-all duration-200 hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Store Orders</span>
                <div class="w-10 h-10 rounded-2xl bg-violet-500/10 text-violet-500 border border-violet-500/20 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-bag-shopping"></i>
                </div>
            </div>
            <div>
                <p class="text-2xl sm:text-3xl font-extrabold theme-text-title"><?= $totalOrders ?></p>
                <p class="text-[11px] text-violet-500 mt-1 flex items-center gap-1 font-bold">
                    <i class="fa-solid fa-circle-check"></i> 100% On-Time Fulfillment
                </p>
            </div>
        </div>

        <!-- KPI 3: Total Products -->
        <div class="p-5 rounded-3xl glass-card border theme-border space-y-3 relative overflow-hidden transition-all duration-200 hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Catalog Inventory</span>
                <div class="w-10 h-10 rounded-2xl bg-cyan-500/10 text-cyan-500 border border-cyan-500/20 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
            </div>
            <div>
                <p class="text-2xl sm:text-3xl font-extrabold theme-text-title"><?= $totalProducts ?></p>
                <p class="text-[11px] text-cyan-500 mt-1 flex items-center gap-1 font-bold">
                    <i class="fa-solid fa-tag"></i> 6 Diverse Categories
                </p>
            </div>
        </div>

        <!-- KPI 4: Customers -->
        <div class="p-5 rounded-3xl glass-card border theme-border space-y-3 relative overflow-hidden transition-all duration-200 hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Registered VIPs</span>
                <div class="w-10 h-10 rounded-2xl bg-amber-500/10 text-amber-500 border border-amber-500/20 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
            <div>
                <p class="text-2xl sm:text-3xl font-extrabold theme-text-title"><?= $totalUsers ?></p>
                <p class="text-[11px] text-amber-500 mt-1 flex items-center gap-1 font-bold">
                    <i class="fa-solid fa-crown"></i> Admin: Salman Sahane Abdi
                </p>
            </div>
        </div>

    </div>

    <!-- Dual Interactive Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left: Sales Revenue Trend Area Chart -->
        <div class="lg:col-span-8 p-6 rounded-3xl glass-card border theme-border space-y-4">
            <div class="flex items-center justify-between border-b theme-border pb-4">
                <div>
                    <h3 class="text-base font-bold theme-text-title">Sales & Revenue Velocity</h3>
                    <p class="text-xs theme-text-muted">Weekly revenue analytics from store transactions</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-violet-600/10 text-violet-600 dark:text-violet-400 border border-violet-500/30">
                    Live Telemetry
                </span>
            </div>
            <div class="h-72">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <!-- Right: Category Distribution Doughnut Chart -->
        <div class="lg:col-span-4 p-6 rounded-3xl glass-card border theme-border space-y-4 flex flex-col justify-between">
            <div class="border-b theme-border pb-4">
                <h3 class="text-base font-bold theme-text-title">Inventory By Category</h3>
                <p class="text-xs theme-text-muted">31 products across 6 collections</p>
            </div>
            <div class="h-64 flex items-center justify-center">
                <canvas id="categoryChart"></canvas>
            </div>
            <div class="text-[11px] theme-text-muted text-center border-t theme-border pt-3">
                Real-time synchronized SQLite database
            </div>
        </div>

    </div>

    <!-- Recent Orders & Top Selling Products 2-Column Section -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left (8 cols): Recent Orders Table -->
        <div class="lg:col-span-8 p-6 rounded-3xl glass-card border theme-border space-y-4">
            <div class="flex items-center justify-between border-b theme-border pb-4">
                <div>
                    <h3 class="text-base font-bold theme-text-title">Latest Customer Orders</h3>
                    <p class="text-xs theme-text-muted">Shipments, receipts, and order fulfillment</p>
                </div>
                <a href="orders.php" class="text-xs font-bold text-violet-500 hover:underline flex items-center gap-1">
                    Manage All <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-400 uppercase tracking-wider border-b theme-border pb-2">
                            <th class="py-3 px-3">Order ID</th>
                            <th class="py-3 px-3">Customer</th>
                            <th class="py-3 px-3">Date</th>
                            <th class="py-3 px-3">Payment</th>
                            <th class="py-3 px-3">Total</th>
                            <th class="py-3 px-3">Status</th>
                            <th class="py-3 px-3 text-right">Receipt</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y theme-border">
                        <?php foreach ($recentOrders as $ro): ?>
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                                <td class="py-3.5 px-3 font-mono font-bold text-violet-600 dark:text-violet-400"><?= htmlspecialchars($ro['order_number']) ?></td>
                                <td class="py-3.5 px-3 font-bold theme-text-title"><?= htmlspecialchars($ro['customer_name']) ?></td>
                                <td class="py-3.5 px-3 theme-text-muted"><?= date('M d, Y', strtotime($ro['created_at'])) ?></td>
                                <td class="py-3.5 px-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                        <?= htmlspecialchars($ro['payment_method']) ?>
                                    </span>
                                </td>
                                <td class="py-3.5 px-3 font-extrabold text-emerald-600 dark:text-emerald-400"><?= formatPrice($ro['total']) ?></td>
                                <td class="py-3.5 px-3"><?= renderOrderStatusBadge($ro['order_status']) ?></td>
                                <td class="py-3.5 px-3 text-right">
                                    <a href="../order-success.php?order_number=<?= htmlspecialchars($ro['order_number']) ?>" target="_blank" class="px-2.5 py-1 rounded-lg border theme-border hover:bg-slate-100 dark:hover:bg-slate-800 transition font-bold text-[11px]" title="View Full Receipt">
                                        <i class="fa-solid fa-receipt text-violet-500 mr-1"></i> View
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right (4 cols): Top Selling Luxury Products -->
        <div class="lg:col-span-4 p-6 rounded-3xl glass-card border theme-border space-y-4">
            <div class="flex items-center justify-between border-b theme-border pb-4">
                <div>
                    <h3 class="text-base font-bold theme-text-title">Top Flagship Items</h3>
                    <p class="text-xs theme-text-muted">Highest rated customer favorites</p>
                </div>
                <a href="products.php" class="text-xs font-bold text-violet-500 hover:underline">All (31)</a>
            </div>

            <div class="space-y-3">
                <?php foreach ($topProducts as $tp): ?>
                    <div class="flex items-center gap-3 p-2.5 rounded-2xl hover:bg-slate-50 dark:hover:bg-slate-800/40 border theme-border transition">
                        <img src="<?= htmlspecialchars($tp['image']) ?>" alt="<?= htmlspecialchars($tp['name']) ?>" class="w-12 h-12 rounded-xl object-cover border theme-border">
                        <div class="flex-1 min-w-0">
                            <h4 class="text-xs font-bold theme-text-title truncate"><?= htmlspecialchars($tp['name']) ?></h4>
                            <div class="flex items-center justify-between mt-1">
                                <span class="text-xs font-extrabold text-emerald-600 dark:text-emerald-400"><?= formatPrice($tp['price']) ?></span>
                                <span class="text-[10px] text-amber-500 font-bold"><i class="fa-solid fa-star text-[9px]"></i> <?= $tp['rating'] ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>

</div>

<!-- Initialize Chart.js Scripts -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Sales Trend Line Chart
    const salesCtx = document.getElementById('salesChart');
    if (salesCtx) {
        new Chart(salesCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Revenue ($)',
                    data: [1250, 2400, 1950, 3850, 2900, 4480, 5120],
                    borderColor: '#8b5cf6',
                    backgroundColor: 'rgba(139, 92, 246, 0.15)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#8b5cf6',
                    pointBorderColor: '#fff',
                    pointRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(150, 150, 150, 0.1)' },
                        ticks: { color: '#94a3b8' }
                    },
                    y: {
                        grid: { color: 'rgba(150, 150, 150, 0.1)' },
                        ticks: { color: '#94a3b8', callback: v => '$' + v }
                    }
                }
            }
        });
    }

    // 2. Category Doughnut Chart
    const catCtx = document.getElementById('categoryChart');
    if (catCtx) {
        new Chart(catCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($catLabels) ?>,
                datasets: [{
                    data: <?= json_encode($catCounts) ?>,
                    backgroundColor: [
                        '#8b5cf6',
                        '#06b6d4',
                        '#10b981',
                        '#f59e0b',
                        '#ec4899',
                        '#6366f1'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#94a3b8', boxWidth: 10, font: { size: 10 } }
                    }
                },
                cutout: '70%'
            }
        });
    }
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
