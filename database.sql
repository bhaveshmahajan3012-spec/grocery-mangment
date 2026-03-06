-- =============================================
-- GROCERY MANAGEMENT SYSTEM - DATABASE
-- =============================================

CREATE DATABASE IF NOT EXISTS grocery_db;
USE grocery_db;

CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(15),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock INT DEFAULT 0,
    image VARCHAR(255),
    unit VARCHAR(50) DEFAULT 'piece',
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending','confirmed','processing','delivered','cancelled') DEFAULT 'pending',
    payment_method VARCHAR(50) DEFAULT 'COD',
    delivery_address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
);

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Admin (password: admin123)
INSERT INTO admin (name, email, password) VALUES
('Admin User', 'admin@grocery.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Categories
INSERT INTO categories (name, description) VALUES
('Fruits & Vegetables', 'Fresh fruits and vegetables'),
('Dairy & Eggs', 'Milk, cheese, butter and eggs'),
('Bakery', 'Bread, cakes and baked goods'),
('Beverages', 'Juices, water and soft drinks'),
('Snacks', 'Chips, biscuits and snacks'),
('Grains & Pulses', 'Rice, wheat, dal and pulses'),
('Oils & Ghee', 'Cooking oils and ghee'),
('Spices', 'Masalas and spices');

-- Products WITH real images
INSERT INTO products (category_id, name, description, price, stock, unit, image) VALUES
(1, 'Tomatoes', 'Fresh red tomatoes', 30.00, 100, 'kg', 'Tomato.jpg'),
(1, 'Onions', 'Fresh onions', 25.00, 150, 'kg', 'Onion.jpg'),
(1, 'Potatoes', 'Fresh potatoes', 20.00, 200, 'kg', ''),
(1, 'Bananas', 'Fresh bananas (dozen)', 40.00, 80, 'dozen', 'Banana.jpg'),
(1, 'Apples', 'Fresh apples', 120.00, 60, 'kg', ''),
(2, 'Amul Milk', 'Full cream milk 1L', 65.00, 100, 'litre', 'Amul_milk.jpg'),
(2, 'Paneer', 'Fresh paneer 200g', 80.00, 50, 'packet', ''),
(2, 'Curd', 'Fresh dahi 500g', 45.00, 70, 'packet', ''),
(2, 'Eggs', 'Farm fresh eggs (dozen)', 75.00, 90, 'dozen', 'Eggs.jpg'),
(3, 'Bread', 'White sandwich bread', 40.00, 60, 'packet', 'Bread.jpg'),
(4, 'Mango Juice', 'Maaza mango juice 1L', 90.00, 80, 'litre', 'Mango_juice.jpg'),
(4, 'Mineral Water', 'Bisleri water 1L', 20.00, 200, 'bottle', ''),
(5, 'Lay\'s Chips', 'Lay\'s classic salted 50g', 20.00, 100, 'packet', 'Lays_chips.jpg'),
(5, 'Marie Biscuits', 'Britannia Marie 250g', 30.00, 80, 'packet', ''),
(6, 'Basmati Rice', 'Premium basmati rice 5kg', 350.00, 40, 'packet', 'Basmati-Rice-7.jpg'),
(6, 'Toor Dal', 'Toor dal 1kg', 130.00, 60, 'kg', ''),
(7, 'Sunflower Oil', 'Fortune sunflower oil 1L', 140.00, 50, 'litre', ''),
(8, 'Mirchi Powder', 'Suhana mirchi powder 100g', 35.00, 80, 'packet', 'Mirchi_powder_suhaana.jpg');
