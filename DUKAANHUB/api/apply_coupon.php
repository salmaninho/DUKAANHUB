<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/functions.php';

$code = trim($_POST['coupon_code'] ?? '');

if (empty($code)) {
    echo json_encode(['success' => false, 'message' => 'Please enter coupon code']);
    exit;
}

$pdo = getDB();
$stmt = $pdo->prepare("SELECT * FROM coupons WHERE UPPER(code) = UPPER(?) AND is_active = 1");
$stmt->execute([$code]);
$coupon = $stmt->fetch();

if (!$coupon) {
    echo json_encode(['success' => false, 'message' => 'Invalid or expired coupon code']);
    exit;
}

$totals = calculateCartTotals();
if ($totals['subtotal'] < (float)$coupon['min_spend']) {
    echo json_encode([
        'success' => false, 
        'message' => 'Minimum order amount for this coupon is ' . formatPrice($coupon['min_spend'])
    ]);
    exit;
}

$_SESSION['coupon'] = [
    'code' => $coupon['code'],
    'percent' => (int)$coupon['discount_percent']
];

$newTotals = calculateCartTotals();

echo json_encode([
    'success' => true,
    'discountPercent' => $coupon['discount_percent'],
    'totals' => $newTotals
]);
