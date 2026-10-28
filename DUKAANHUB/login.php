<?php
require_once __DIR__ . '/includes/functions.php';

$pdo = getDB();
$error = null;
$success = null;

// Handle Login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_submit'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please enter both your email address and password.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE LOWER(email) = LOWER(?)");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
                'avatar' => $user['avatar'] ?? 'assets/images/salman.jpg',
                'phone' => $user['phone'] ?? '',
                'address' => $user['address'] ?? ''
            ];

            setFlash('success', "Welcome back, {$user['name']}!");

            if ($user['role'] === 'admin') {
                header('Location: admin/index.php');
            } else {
                header('Location: index.php');
            }
            exit;
        } else {
            $error = 'Invalid email or password. You can use the 1-click demo buttons below.';
        }
    }
}

// Handle Register
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_submit'])) {
    $name = trim($_POST['reg_name'] ?? '');
    $email = trim($_POST['reg_email'] ?? '');
    $password = $_POST['reg_password'] ?? '';

    if (empty($name) || empty($email) || empty($password)) {
        $error = 'All registration fields are required.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE LOWER(email) = LOWER(?)");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'An account with that email already exists. Please sign in.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $ins = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'customer')");
            $ins->execute([$name, $email, $hashed]);

            $_SESSION['user'] = [
                'id' => $pdo->lastInsertId(),
                'name' => $name,
                'email' => $email,
                'role' => 'customer',
                'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=400&q=80',
                'phone' => '',
                'address' => ''
            ];

            setFlash('success', "Welcome to DukaanHub, $name!");
            header('Location: index.php');
            exit;
        }
    }
}

$pageTitle = 'Sign In & Demo Credentials';
require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-md mx-auto px-4 py-12">
    
    <!-- Header Branding: DukaanHub -->
    <div class="text-center mb-6">
        <a href="index.php" class="inline-flex items-center gap-3 mb-2">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-violet-600 via-indigo-600 to-cyan-400 flex items-center justify-center text-white text-xl shadow-lg shadow-violet-600/30">
                <i class="fa-solid fa-store"></i>
            </div>
            <span class="font-display font-extrabold text-2xl tracking-tight text-slate-900 dark:text-white">Dukaan<span class="text-violet-500">Hub</span></span>
        </a>
        <p class="text-xs text-slate-500 dark:text-slate-400">Account Authentication & Demo Logins</p>
    </div>

    <!-- Quick 1-Click Credentials Banner -->
    <div class="p-4 rounded-3xl glass-card border theme-border mb-6 space-y-2.5">
        <div class="flex items-center justify-between text-xs font-bold text-violet-600 dark:text-violet-300">
            <span><i class="fa-solid fa-bolt text-amber-500 mr-1"></i> Fast Demo 1-Click Auto Fill:</span>
        </div>
        <p class="text-[11px] text-slate-500 dark:text-slate-400">Click any button below to instantly populate credentials:</p>
        
        <div class="grid grid-cols-2 gap-2 pt-1">
            <button type="button" onclick="fillPageCredentials('Salmansahaneabdi@gmail.com', 'salman123')" class="p-2.5 rounded-2xl bg-violet-600/10 hover:bg-violet-600/20 border border-violet-500/30 text-left transition">
                <span class="block text-[10px] font-bold text-violet-600 dark:text-violet-300 uppercase">👑 Admin</span>
                <span class="block text-xs font-bold theme-text-title truncate">Salman Sahane</span>
                <span class="block text-[10px] text-slate-400 font-mono truncate">Salmansahaneabdi@gmail.com</span>
            </button>
            <button type="button" onclick="fillPageCredentials('samiiro@shop.com', 'user123')" class="p-2.5 rounded-2xl bg-cyan-600/10 hover:bg-cyan-600/20 border border-cyan-500/30 text-left transition">
                <span class="block text-[10px] font-bold text-cyan-600 dark:text-cyan-300 uppercase">👤 Customer</span>
                <span class="block text-xs font-bold theme-text-title truncate">Samiiro sahane</span>
                <span class="block text-[10px] text-slate-400 font-mono truncate">samiiro@shop.com</span>
            </button>
        </div>
    </div>

    <?php if ($error): ?>
        <div class="p-3.5 mb-5 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-500 text-xs flex items-center gap-2 font-semibold">
            <i class="fa-solid fa-circle-exclamation text-base"></i>
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <!-- Tabs: Sign In / Register -->
    <div class="rounded-3xl glass-card border theme-border overflow-hidden shadow-xl">
        <div class="flex border-b theme-border text-xs font-bold">
            <button type="button" id="tab-login-btn" onclick="switchAuthTab('login')" class="flex-1 py-3.5 text-center text-violet-600 dark:text-violet-400 border-b-2 border-violet-600">
                Sign In
            </button>
            <button type="button" id="tab-reg-btn" onclick="switchAuthTab('register')" class="flex-1 py-3.5 text-center text-slate-500 hover:text-slate-800 dark:hover:text-white">
                Create Account
            </button>
        </div>

        <!-- Login Form -->
        <form id="form-login" method="POST" action="login.php" class="p-6 space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Email Address</label>
                <div class="relative">
                    <input id="login-email" type="email" name="email" required placeholder="Salmansahaneabdi@gmail.com" value="Salmansahaneabdi@gmail.com" class="w-full pl-10 pr-4 py-2.5 rounded-xl border text-xs focus:outline-none focus:border-violet-500">
                    <i class="fa-regular fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Password</label>
                    <a href="forgot-password.php" class="text-[11px] font-bold text-violet-600 hover:underline">
                        Forgot Password?
                    </a>
                </div>
                <div class="relative">
                    <input id="login-password" type="password" name="password" required placeholder="••••••••" value="salman123" class="w-full pl-10 pr-10 py-2.5 rounded-xl border text-xs focus:outline-none focus:border-violet-500">
                    <i class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <button type="button" onclick="togglePasswordVisibility('login-password', this)" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                        <i class="fa-regular fa-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" name="login_submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white font-bold text-xs shadow-lg shadow-violet-600/30 transition">
                Sign In To DukaanHub
            </button>
        </form>

        <!-- Register Form -->
        <form id="form-register" method="POST" action="login.php" class="p-6 space-y-4 hidden">
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Full Name</label>
                <input type="text" name="reg_name" required placeholder="e.g. Samiiro sahane" class="w-full px-4 py-2.5 rounded-xl border text-xs focus:outline-none focus:border-violet-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Email Address</label>
                <input type="email" name="reg_email" required placeholder="you@domain.com" class="w-full px-4 py-2.5 rounded-xl border text-xs focus:outline-none focus:border-violet-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Create Password</label>
                <input type="password" name="reg_password" required placeholder="At least 6 characters" class="w-full px-4 py-2.5 rounded-xl border text-xs focus:outline-none focus:border-violet-500">
            </div>

            <button type="submit" name="register_submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white font-bold text-xs shadow-lg transition">
                Complete Registration
            </button>
        </form>
    </div>

    <!-- Quick Help -->
    <div class="mt-6 text-center text-xs text-slate-500">
        Need assistance with your password? <a href="forgot-password.php" class="font-bold text-violet-500 hover:underline">Reset with Verification Code</a>
    </div>
</div>

<script>
function fillPageCredentials(email, password) {
    switchAuthTab('login');
    const emailField = document.getElementById('login-email');
    const pwdField = document.getElementById('login-password');
    if (emailField) emailField.value = email;
    if (pwdField) pwdField.value = password;
    showToast(`Filled credentials for ${email}`, 'info');
}

function switchAuthTab(tab) {
    const loginForm = document.getElementById('form-login');
    const regForm = document.getElementById('form-register');
    const loginBtn = document.getElementById('tab-login-btn');
    const regBtn = document.getElementById('tab-reg-btn');

    if (tab === 'login') {
        loginForm.classList.remove('hidden');
        regForm.classList.add('hidden');
        loginBtn.className = "flex-1 py-3.5 text-center text-violet-600 dark:text-violet-400 border-b-2 border-violet-600";
        regBtn.className = "flex-1 py-3.5 text-center text-slate-500 hover:text-slate-800 dark:hover:text-white";
    } else {
        loginForm.classList.add('hidden');
        regForm.classList.remove('hidden');
        regBtn.className = "flex-1 py-3.5 text-center text-violet-600 dark:text-violet-400 border-b-2 border-violet-600";
        loginBtn.className = "flex-1 py-3.5 text-center text-slate-500 hover:text-slate-800 dark:hover:text-white";
    }
}

function togglePasswordVisibility(fieldId, btn) {
    const field = document.getElementById(fieldId);
    if (field.type === 'password') {
        field.type = 'text';
        btn.innerHTML = '<i class="fa-regular fa-eye-slash"></i>';
    } else {
        field.type = 'password';
        btn.innerHTML = '<i class="fa-regular fa-eye"></i>';
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
