<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/functions.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$productId = $_POST['product_id'] ?? $_GET['product_id'] ?? 0;
$quantity = $_POST['quantity'] ?? 1;

if ($action === 'add') {
    $res = addToCart($productId, $quantity);
    echo json_encode($res);
    exit;
}

if ($action === 'update') {
    $res = updateCartItem($productId, $quantity);
    echo json_encode([
        'success' => true,
        'cartCount' => getCartCount(),
        'totals' => $res
    ]);
    exit;
}

if ($action === 'remove') {
    $res = removeCartItem($productId);
    echo json_encode([
        'success' => true,
        'cartCount' => getCartCount(),
        'totals' => $res
    ]);
    exit;
}

if ($action === 'get') {
    echo json_encode([
        'success' => true,
        'cartCount' => getCartCount(),
        'items' => getCartItems(),
        'totals' => calculateCartTotals()
    ]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
