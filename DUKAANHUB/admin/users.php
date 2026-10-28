<?php
$adminPageTitle = 'Registered Users';
require_once __DIR__ . '/header.php';

$users = $pdo->query("SELECT * FROM users ORDER BY id ASC")->fetchAll();
?>

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-white">Registered Users</h1>
            <p class="text-xs text-zinc-400 mt-1">Review authenticated accounts, administrators, and customer profiles</p>
        </div>
        <span class="px-4 py-2 rounded-xl bg-zinc-900 border border-zinc-800 text-xs font-bold text-violet-400">
            Total Accounts: <?= count($users) ?>
        </span>
    </div>

    <!-- Users Table -->
    <div class="rounded-3xl glass-card border border-zinc-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-zinc-400 uppercase tracking-wider border-b border-zinc-800 bg-zinc-950/40">
                        <th class="py-3 px-4">User</th>
                        <th class="py-3 px-4">Email Address</th>
                        <th class="py-3 px-4">Phone</th>
                        <th class="py-3 px-4">Role</th>
                        <th class="py-3 px-4">Registered Date</th>
                        <th class="py-3 px-4 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    <?php foreach ($users as $u): 
                        $uAvatar = $u['avatar'] ?? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=100&q=80';
                        if (strpos($uAvatar, 'http') !== 0 && strpos($uAvatar, '../') !== 0) {
                            $uAvatar = '../' . $uAvatar;
                        }
                    ?>
                        <tr class="hover:bg-zinc-900/60 transition">
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-3">
                                    <img src="<?= htmlspecialchars($uAvatar) ?>" alt="<?= htmlspecialchars($u['name']) ?>" class="w-10 h-10 rounded-xl object-cover border border-zinc-700">
                                    <div>
                                        <h4 class="font-bold text-white text-xs"><?= htmlspecialchars($u['name']) ?></h4>
                                        <span class="text-[10px] text-zinc-500">ID: #<?= $u['id'] ?></span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4 font-mono text-zinc-300"><?= htmlspecialchars($u['email']) ?></td>
                            <td class="py-4 px-4 text-zinc-400"><?= htmlspecialchars($u['phone'] ?? 'N/A') ?></td>
                            <td class="py-4 px-4">
                                <?php if ($u['role'] === 'admin'): ?>
                                    <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase bg-amber-500/10 text-amber-300 border border-amber-500/30 flex items-center gap-1 w-max">
                                        <i class="fa-solid fa-crown text-amber-400"></i> Administrator
                                    </span>
                                <?php else: ?>
                                    <span class="px-3 py-1 rounded-full text-[10px] font-semibold uppercase bg-zinc-800 text-zinc-300 border border-zinc-700 flex items-center gap-1 w-max">
                                        <i class="fa-solid fa-user text-zinc-400"></i> Customer
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="py-4 px-4 text-zinc-400"><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
                            <td class="py-4 px-4 text-right">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                    <i class="fa-solid fa-circle text-[6px]"></i> Active
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
