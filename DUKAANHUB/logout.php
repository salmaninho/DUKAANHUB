<?php
require_once __DIR__ . '/includes/functions.php';

unset($_SESSION['user']);
setFlash('info', 'You have been signed out successfully.');
header('Location: login.php');
exit;
