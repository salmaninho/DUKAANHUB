-- ========================================================
-- ONLINE SHOPPING SYSTEM - MySQL Database Schema
-- Developer: Salman Sahane
-- Can be imported into phpMyAdmin or MySQL Workbench
-- ========================================================

CREATE DATABASE IF NOT EXISTS `online_shopping_system` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `online_shopping_system`;

-- 1. Users Table
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` VARCHAR(20) NOT NULL DEFAULT 'customer',
  `avatar` VARCHAR(255) NULL,
  `phone` VARCHAR(50) NULL,
  `address` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Categories Table
CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `icon` VARCHAR(50) NOT NULL,
  `description` TEXT NULL,
  `image` VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Products Table
CREATE TABLE IF NOT EXISTS `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT NULL,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `description` TEXT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `original_price` DECIMAL(10,2) NULL,
  `stock` INT NOT NULL DEFAULT 10,
  `rating` DECIMAL(2,1) DEFAULT 4.8,
  `reviews_count` INT DEFAULT 24,
  `is_featured` TINYINT(1) DEFAULT 0,
  `is_deal` TINYINT(1) DEFAULT 0,
  `badge` VARCHAR(50) NULL,
  `image` TEXT NOT NULL,
  `gallery` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Orders Table
CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_number` VARCHAR(50) NOT NULL UNIQUE,
  `user_id` INT NULL,
  `customer_name` VARCHAR(100) NOT NULL,
  `customer_email` VARCHAR(100) NOT NULL,
  `customer_phone` VARCHAR(50) NULL,
  `shipping_address` TEXT NOT NULL,
  `payment_method` VARCHAR(50) NOT NULL,
  `payment_status` VARCHAR(50) DEFAULT 'Paid',
  `order_status` VARCHAR(50) DEFAULT 'Processing',
  `subtotal` DECIMAL(10,2) NOT NULL,
  `discount` DECIMAL(10,2) DEFAULT 0.00,
  `shipping_fee` DECIMAL(10,2) DEFAULT 0.00,
  `total` DECIMAL(10,2) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Order Items Table
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `product_id` INT NULL,
  `product_name` VARCHAR(255) NOT NULL,
  `product_image` TEXT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `quantity` INT NOT NULL,
  `total` DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Coupons Table
CREATE TABLE IF NOT EXISTS `coupons` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(50) NOT NULL UNIQUE,
  `discount_percent` INT NOT NULL,
  `min_spend` DECIMAL(10,2) DEFAULT 0.00,
  `is_active` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Reviews Table
CREATE TABLE IF NOT EXISTS `reviews` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT NOT NULL,
  `user_name` VARCHAR(100) NOT NULL,
  `rating` INT NOT NULL,
  `comment` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Admin User (Salman Sahane) and Customer User
-- Passwords: 'salman123' and 'user123' (BCrypt hashed)
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `avatar`, `phone`, `address`) VALUES
(1, 'Salman Sahane', 'salman@shop.com', '$2y$10$wK1b0Q7cTqG7n1fI3oKkse1mZqZ3NsmHq30N9oXw4L1U4Gq2x0ZCe', 'admin', 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=400&q=80', '+1 (555) 987-6543', '777 Luxury Blvd, Silicon Valley, CA'),
(2, 'Samiiro sahane', 'samiiro@shop.com', '$2y$10$mC1B7sP50NnmPqy/4zL3vOm1f0d3BszqQe0yT.p4w8EwE4B82Y7sO', 'customer', 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=400&q=80', '+1 (555) 234-5678', '456 Sunset Ave, Los Angeles, CA'),
(3, 'Muscab sahane', 'muscab@shop.com', '$2y$10$mC1B7sP50NnmPqy/4zL3vOm1f0d3BszqQe0yT.p4w8EwE4B82Y7sO', 'customer', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=400&q=80', '+1 (555) 345-6789', '108 Ocean View Drive, Miami, FL'),
(4, 'Salmo sahane', 'salmo@shop.com', '$2y$10$mC1B7sP50NnmPqy/4zL3vOm1f0d3BszqQe0yT.p4w8EwE4B82Y7sO', 'customer', 'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=400&q=80', '+1 (555) 456-7890', '720 Grand Avenue, Chicago, IL'),
(5, 'Halimo ahmed', 'halimo@gmail.com', '$2y$10$mC1B7sP50NnmPqy/4zL3vOm1f0d3BszqQe0yT.p4w8EwE4B82Y7sO', 'customer', 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=400&q=80', '+1 (555) 567-8901', '15 Park Place, Suite 400, New York, NY');

-- Coupons
INSERT INTO `coupons` (`code`, `discount_percent`, `min_spend`, `is_active`) VALUES
('SALMAN20', 20, 50.00, 1),
('WELCOME10', 10, 30.00, 1),
('VIP50', 50, 200.00, 1);
