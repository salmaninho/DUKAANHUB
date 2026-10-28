<?php
ob_start();
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';
require __DIR__ . '/index.php';
$html = ob_get_clean();

// Replace PHP links to point nicely on static GitHub Pages
$html = str_replace('href="shop.php"', 'href="#shop"', $html);
$html = str_replace('href="cart.php"', 'href="#cart"', $html);
$html = str_replace('href="login.php"', 'href="#login"', $html);
$html = str_replace('href="admin/"', 'href="#admin"', $html);

file_put_contents(__DIR__ . '/index.html', $html);
echo "Successfully generated index.html (" . strlen($html) . " bytes)\n";
