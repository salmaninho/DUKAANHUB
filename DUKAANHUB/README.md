# 👑 ONLINE SHOPPING SYSTEM — DUKAANHUB
### Developed by **Salman Sahane**

> A flagship, modern e-commerce web application built with clean PHP, PDO architecture, dynamic AJAX shopping bag, real-time dark/light mode engine, and an ultra-modern glassmorphism executive dashboard.

---

## 🌐 Live Website & Project Links

| Resource | Action Link |
| :--- | :--- |
| 🚀 **Live Published Website** | [👉 **View Live Website**](https://salmaninho.github.io/DUKAANHUB/) |
| 💻 **GitHub Project Repository** | [👉 **View Source Code on GitHub**](https://github.com/salmaninho/DUKAANHUB) |

*Click the links above to preview the live published storefront on GitHub Pages or inspect the full source code and project files on GitHub.*

---

## 🌟 Key Highlights & Comparison

| Feature | Old Class Template (AdminLTE / Valid Admin) | **Salman Sahane's Luxury System** |
| :--- | :--- | :--- |
| **Theme / Design** | Outdated Bootstrap 3/4 boxy tables | **Modern Cyber/Luxe Glassmorphism** with Tailwind CSS & custom animations |
| **Dark Mode** | Inverted CSS hacks or none | **Native Smooth Dark Mode** with 1-click toggle and persistence |
| **Interactivity** | Page reloads on every button press | **100% Functional Buttons & AJAX Cart**, instant toast alerts, modals, wishlists |
| **Visuals & Media** | Missing or broken local image links | **High-Resolution Shopping Photography** across 6 diverse categories |
| **Admin Dashboard** | Generic AdminLTE copied theme | **Bespoke Executive Dashboard** with Chart.js analytics & live status controls |
| **Setup Friction** | Complex MySQL import required before starting | **Zero-Config Auto-Migration** (SQLite out-of-the-box + MySQL `.sql` ready) |

---

## 🔑 Demo Access Credentials

The demo credentials and Salman Sahane's name are prominently featured at the top banner of the browser:

| Role | Email Address | Password | Privileges |
| :--- | :--- | :--- | :--- |
| **Administrator** | `salman@shop.com` | `salman123` | Full access to Admin Portal, Analytics, Products & Orders |
| **Customer** | `customer@shop.com` | `user123` | Shopping bag, checkout, tracking past orders & invoices |

*Note: On the login page, you can also click the **"1-Click Auto Fill"** buttons to log in with one tap!*

### 🎟️ VIP Coupon Code
- **`SALMAN20`** — Gives an instant **20% discount** at checkout!

---

## 🚀 How to Run in Visual Studio Code

### Method 1: Using the 1-Click Batch File (Easiest)
1. Open this folder in **Visual Studio Code**:
   `C:\Users\salmaan\.gemini\antigravity\scratch\online-shopping-system`
2. Open the terminal (`Ctrl + \``) or File Explorer.
3. Simply double-click or run:
   ```cmd
   .\run.bat
   ```
4. It will start the PHP server and automatically launch `http://localhost:8000` in your browser!

---

### Method 2: Running via VS Code Built-in Terminal
1. Open Visual Studio Code in this project folder.
2. Open the integrated terminal (`Ctrl + ~` or **Terminal -> New Terminal**).
3. Run:
   ```powershell
   php -S localhost:8000
   ```
4. Open your browser and navigate to:
   ```
   http://localhost:8000
   ```

---

### Method 3: Using VS Code Tasks (`Ctrl + Shift + B`)
- Press `Ctrl + Shift + B` (Run Build Task).
- Select **"Start Online Shopping System (PHP Server)"**.
- It will automatically launch your default web browser and run the server.

---

## 🗄️ Database Architecture

This project is engineered with a **Dual-Database Adapter**:

1. **Default: SQLite (Zero Configuration Required)**
   - Database file is stored at `database/shopping.db`.
   - Automatically creates all tables and seeds 12 luxury products, categories, users, orders, and reviews on first boot!
   - **No MySQL configuration or XAMPP start needed.**

2. **Optional: MySQL / phpMyAdmin (If requested by teacher)**
   - A complete schema file is provided: `database/schema_mysql.sql`.
   - To use MySQL:
     1. Import `database/schema_mysql.sql` into phpMyAdmin.
     2. Open `database/db.php` and set:
        ```php
        define('DB_TYPE', 'mysql');
        ```

---

## 📁 Project Directory Structure

```
online-shopping-system/
├── .vscode/                 # VS Code task and launch configurations
│   ├── launch.json
│   ├── tasks.json
│   └── settings.json
├── database/                # Database handler and schemas
│   ├── db.php               # PDO connection & auto-table creator
│   ├── seed_data.php        # Rich products, categories, orders seeder
│   ├── schema_mysql.sql     # Ready-to-import MySQL schema
│   └── shopping.db          # Active SQLite database file
├── includes/                # Shared layout & reusable components
│   ├── header.php           # Top bar (Salman Sahane, Credentials, Dark mode toggle)
│   ├── footer.php           # Drawer cart, QuickView modal, Trust badges
│   └── functions.php        # Cart calculations, Auth guards, Order badges
├── admin/                   # Custom Executive Admin Dashboard
│   ├── index.php            # Analytics KPIs, Chart.js Revenue trend & recent orders
│   ├── products.php         # Product inventory management & Add modal
│   ├── orders.php           # Order status management (Shipped / Delivered)
│   ├── categories.php       # Category departments & counts
│   ├── users.php            # Registered accounts & role badges
│   ├── header.php           # Admin sidebar & top nav
│   └── footer.php
├── api/                     # Asynchronous AJAX endpoints
│   ├── cart.php             # Add, update quantity, remove, get cart state
│   ├── apply_coupon.php     # Promo discount engine (SALMAN20)
│   └── quickview.php        # Product modal data provider
├── assets/
│   ├── css/
│   │   └── custom.css       # Glassmorphism, animations, toast notifications
│   └── js/
│       └── main.js          # Interactive click handlers, toasts, wishlist, theme
├── index.php                # Homepage with hero showcase & trending items
├── shop.php                 # Catalog with live search & category sidebar
├── product-details.php      # Product gallery, specs, reviews form, Buy Now
├── cart.php                 # Shopping bag with quantity steppers & promo codes
├── checkout.php             # Delivery address, payment selection & order placement
├── order-success.php        # Printable official invoice receipt
├── my-orders.php            # Customer order history & tracking
├── login.php                # Authentication & 1-click credential auto-fill
├── logout.php               # Session termination
├── run.bat                  # One-click Windows starter script
└── README.md                # Project documentation
```

---

## 💎 Features Built for Presentation

- **Top of the Browser Personalization**: Proudly presents **Salman Sahane** and quick login credentials in the top announcement bar and page header.
- **Dark Mode**: Sleek obsidian dark palette by default, toggleable to crisp light mode with instant memory.
- **Rich Product Catalog**: 12+ luxury items (Sony WH-1000XM5, Apple Watch Ultra 2, Nike Air Jordans, MacBook Pro M3, Leica Q3, Cyberpunk Techwear) with high-res product photos.
- **Every Button Fully Functional**:
  - Add to Shopping Bag (instant badge bounce & toast)
  - Quick View modal
  - Heart Wishlist toggle (persists in browser)
  - Cart offcanvas drawer
  - Promo code discounts (`SALMAN20`)
  - Order placement with printable invoice receipt
  - Review rating form with star ratings
  - Admin product creation & order status modification
