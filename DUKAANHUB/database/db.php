<?php
/**
 * Database Connection & Auto-Migration Handler
 * Supports both SQLite (Zero-config out of the box) & MySQL
 * Project: ONLINE SHOPPING SYSTEM - Salman Sahane
 */

if (!headers_sent() && session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database Configuration
// Set to 'sqlite' for instant zero-config setup, or 'mysql' for phpMyAdmin / XAMPP
define('DB_TYPE', 'sqlite'); 

// MySQL settings (Used only if DB_TYPE === 'mysql')
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'online_shopping_system');
define('DB_USER', 'root');
define('DB_PASS', '');

function getDB() {
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    try {
        if (DB_TYPE === 'sqlite') {
            $dbPath = __DIR__ . '/shopping.db';
            $isNew = !file_exists($dbPath);
            $pdo = new PDO("sqlite:" . $dbPath);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            // Enable WAL mode & foreign keys for SQLite performance
            $pdo->exec("PRAGMA journal_mode = WAL;");
            $pdo->exec("PRAGMA foreign_keys = ON;");

            if ($isNew || filesize($dbPath) < 100) {
                initializeSQLite($pdo);
            }
        } else {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        }
    } catch (PDOException $e) {
        die("<div style='font-family:sans-serif;padding:30px;background:#18181b;color:#f43f5e;border-radius:12px;margin:40px auto;max-width:600px;border:1px solid #27272a;'>
            <h2 style='margin-top:0;'>⚠️ Database Connection Notice</h2>
            <p style='color:#e4e4e7;'>Could not connect to database: " . htmlspecialchars($e->getMessage()) . "</p>
            <p style='color:#a1a1aa;font-size:14px;'>Default SQLite is recommended for instant launch. If you switched to MySQL, ensure MySQL is running in XAMPP/WAMP.</p>
        </div>");
    }

    return $pdo;
}

function initializeSQLite($pdo) {
    // 1. Create Users Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        role TEXT NOT NULL DEFAULT 'customer',
        avatar TEXT,
        phone TEXT,
        address TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );");

    // 2. Create Categories Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS categories (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        slug TEXT NOT NULL UNIQUE,
        icon TEXT NOT NULL,
        description TEXT,
        image TEXT
    );");

    // 3. Create Products Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        category_id INTEGER,
        name TEXT NOT NULL,
        slug TEXT NOT NULL UNIQUE,
        description TEXT,
        price REAL NOT NULL,
        original_price REAL,
        stock INTEGER DEFAULT 10,
        rating REAL DEFAULT 4.8,
        reviews_count INTEGER DEFAULT 24,
        is_featured INTEGER DEFAULT 0,
        is_deal INTEGER DEFAULT 0,
        badge TEXT,
        image TEXT NOT NULL,
        gallery TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
    );");

    // 4. Create Orders Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS orders (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        order_number TEXT NOT NULL UNIQUE,
        user_id INTEGER,
        customer_name TEXT NOT NULL,
        customer_email TEXT NOT NULL,
        customer_phone TEXT,
        shipping_address TEXT NOT NULL,
        payment_method TEXT NOT NULL,
        payment_status TEXT DEFAULT 'Paid',
        order_status TEXT DEFAULT 'Processing',
        subtotal REAL NOT NULL,
        discount REAL DEFAULT 0,
        shipping_fee REAL DEFAULT 0,
        total REAL NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    );");

    // 5. Create Order Items Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS order_items (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        order_id INTEGER NOT NULL,
        product_id INTEGER,
        product_name TEXT NOT NULL,
        product_image TEXT,
        price REAL NOT NULL,
        quantity INTEGER NOT NULL,
        total REAL NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
    );");

    // 6. Create Coupons Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS coupons (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        code TEXT NOT NULL UNIQUE,
        discount_percent INTEGER NOT NULL,
        min_spend REAL DEFAULT 0,
        is_active INTEGER DEFAULT 1
    );");

    // 7. Create Reviews Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS reviews (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        product_id INTEGER NOT NULL,
        user_name TEXT NOT NULL,
        rating INTEGER NOT NULL,
        comment TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    );");

    // Seed Data
    require_once __DIR__ . '/seed_data.php';
    seedDatabase($pdo);
}
