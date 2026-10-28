<?php
require_once __DIR__ . '/includes/functions.php';

$pdo = getDB();
$step = 1;
$info = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['send_code_btn'])) {
        $email = trim($_POST['email'] ?? '');
        $stmt = $pdo->prepare("SELECT id FROM users WHERE LOWER(email) = LOWER(?)");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $code = (string)rand(100000, 999999);
            $_SESSION['reset_email'] = $email;
            $_SESSION['reset_code'] = $code;
            $_SESSION['reset_expires'] = time() + 900;
            $step = 2;
            $info = "Verification code generated for $email: <strong>$code</strong> (Valid for 15 mins)";
        } else {
            $error = "No user found with email $email. Please try Salmansahaneabdi@gmail.com";
        }
    } elseif (isset($_POST['reset_pwd_btn'])) {
        $email = $_SESSION['reset_email'] ?? '';
        $code = trim($_POST['code'] ?? '');
        $newPwd = $_POST['new_password'] ?? '';
        $confPwd = $_POST['confirm_password'] ?? '';

        if ($code !== ($_SESSION['reset_code'] ?? '')) {
            $step = 2;
            $error = "Invalid verification code. Please check the code provided.";
        } elseif ($newPwd !== $confPwd) {
            $step = 2;
            $error = "New password and Confirm password do not match.";
        } elseif (strlen($newPwd) < 5) {
            $step = 2;
            $error = "Password must be at least 5 characters.";
        } else {
            $hashed = password_hash($newPwd, PASSWORD_DEFAULT);
            $up = $pdo->prepare("UPDATE users SET password = ? WHERE LOWER(email) = LOWER(?)");
            $up->execute([$hashed, $email]);
            unset($_SESSION['reset_code'], $_SESSION['reset_email']);
            setFlash('success', 'Your password has been reset successfully! Please sign in.');
            header('Location: login.php');
            exit;
        }
    }
}

$pageTitle = 'Reset Password - DukaanHub';
require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-md mx-auto px-4 py-16">
    <div class="text-center mb-8">
        <a href="index.php" class="inline-flex items-center gap-2 mb-3">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-violet-600 via-indigo-600 to-cyan-400 flex items-center justify-center text-white text-xl shadow-lg shadow-violet-600/30">
                <i class="fa-solid fa-store"></i>
            </div>
            <span class="font-extrabold text-2xl tracking-tight text-slate-900 dark:text-white">Dukaan<span class="text-violet-500">Hub</span></span>
        </a>
        <h1 class="text-xl font-bold text-slate-900 dark:text-white">Account Recovery</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Reset your password with verification code</p>
    </div>

    <div class="p-6 sm:p-8 rounded-3xl glass-card border theme-border shadow-xl">
        <?php if ($error): ?>
            <div class="p-3 mb-5 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-500 text-xs flex items-center gap-2 font-medium">
                <i class="fa-solid fa-circle-exclamation text-base"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($info): ?>
            <div class="p-3 mb-5 rounded-xl bg-violet-500/10 border border-violet-500/30 text-violet-600 dark:text-violet-300 text-xs flex items-center gap-2 font-medium">
                <i class="fa-solid fa-key text-base text-amber-500"></i>
                <div><?= $info ?></div>
            </div>
        <?php endif; ?>

        <?php if ($step === 1): ?>
            <form method="POST" action="forgot-password.php" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Enter Registered Gmail / Email:</label>
                    <input type="email" name="email" required value="Salmansahaneabdi@gmail.com" class="w-full px-4 py-2.5 rounded-xl border text-xs focus:outline-none focus:border-violet-500">
                    <p class="text-[11px] text-slate-400 mt-1">Admin demo account: <code class="text-violet-500">Salmansahaneabdi@gmail.com</code></p>
                </div>

                <button type="submit" name="send_code_btn" class="w-full py-3 rounded-xl bg-violet-600 hover:bg-violet-500 text-white font-bold text-xs shadow-md transition">
                    <i class="fa-solid fa-paper-plane mr-1.5"></i> Send 6-Digit Verification Code
                </button>
            </form>
        <?php else: ?>
            <form method="POST" action="forgot-password.php" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">6-Digit Verification Code:</label>
                    <input type="text" name="code" required value="<?= htmlspecialchars($_SESSION['reset_code'] ?? '') ?>" placeholder="e.g. 849201" class="w-full px-4 py-2.5 rounded-xl border text-xs font-mono tracking-widest text-center text-sm font-bold focus:outline-none focus:border-violet-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">New Password:</label>
                    <input type="password" name="new_password" required placeholder="Enter new password" class="w-full px-4 py-2.5 rounded-xl border text-xs focus:outline-none focus:border-violet-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Confirm New Password:</label>
                    <input type="password" name="confirm_password" required placeholder="Confirm new password" class="w-full px-4 py-2.5 rounded-xl border text-xs focus:outline-none focus:border-violet-500">
                </div>

                <button type="submit" name="reset_pwd_btn" class="w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md transition">
                    <i class="fa-solid fa-check mr-1.5"></i> Update Password & Sign In
                </button>
            </form>
        <?php endif; ?>

        <div class="mt-6 pt-4 border-t theme-border text-center">
            <a href="login.php" class="text-xs font-bold text-violet-500 hover:underline">
                <i class="fa-solid fa-arrow-left mr-1"></i> Back to Sign In
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
