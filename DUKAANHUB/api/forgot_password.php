<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/functions.php';

$action = $_POST['action'] ?? '';
$email = trim($_POST['email'] ?? '');

if (empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Please provide your email address.']);
    exit;
}

$pdo = getDB();

// 1. Send Verification Code
if ($action === 'send_code') {
    $stmt = $pdo->prepare("SELECT id, name FROM users WHERE LOWER(email) = LOWER(?)");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        // If email not found, for security and ease of demo, we can either report or create code
        echo json_encode(['success' => false, 'message' => 'No account found with this email. Please check the spelling or use Salmansahaneabdi@gmail.com.']);
        exit;
    }

    // Generate 6-digit verification code
    $code = (string)rand(100000, 999999);
    $_SESSION['reset_email'] = $email;
    $_SESSION['reset_code'] = $code;
    $_SESSION['reset_expires'] = time() + 900; // 15 mins

    echo json_encode([
        'success' => true,
        'message' => "Verification code sent to $email!",
        'code' => $code // Provided directly for immediate testing & simulation
    ]);
    exit;
}

// 2. Verify Code & Reset Password
if ($action === 'reset_password') {
    $code = trim($_POST['code'] ?? '');
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($code) || empty($newPassword)) {
        echo json_encode(['success' => false, 'message' => 'Verification code and new password are required.']);
        exit;
    }

    if ($newPassword !== $confirmPassword) {
        echo json_encode(['success' => false, 'message' => 'Passwords do not match. Please re-enter.']);
        exit;
    }

    if (strlen($newPassword) < 5) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 5 characters long.']);
        exit;
    }

    // Check verification code from session
    $validCode = $_SESSION['reset_code'] ?? null;
    $validEmail = $_SESSION['reset_email'] ?? null;

    if (!$validCode || $validCode !== $code || strtolower($validEmail) !== strtolower($email)) {
        echo json_encode(['success' => false, 'message' => 'Invalid or expired verification code. Please request a new code.']);
        exit;
    }

    // Update password in database
    $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
    $upStmt = $pdo->prepare("UPDATE users SET password = ? WHERE LOWER(email) = LOWER(?)");
    $upStmt->execute([$hashed, $email]);

    // Clear reset session
    unset($_SESSION['reset_code'], $_SESSION['reset_email'], $_SESSION['reset_expires']);

    echo json_encode([
        'success' => true,
        'message' => 'Password updated successfully! You can now log in.'
    ]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request action.']);
