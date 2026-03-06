# 🛒 FreshMart - Grocery Management System
### Final Year College Project | HTML + CSS + JavaScript + PHP + MySQL

---

## 📁 Project Structure
```
grocery_management/
├── index.php               ← Landing/Home Page
├── config.php              ← Database Configuration
├── database.sql            ← MySQL Database Setup
├── admin/
│   ├── login.php           ← Admin Login
│   ├── dashboard.php       ← Admin Dashboard
│   ├── products.php        ← Product Management
│   ├── categories.php      ← Category Management
│   ├── orders.php          ← Order Management
│   ├── customers.php       ← Customer Management
│   ├── header.php          ← Admin Navbar/Sidebar
│   ├── footer.php          ← Admin Footer
│   └── logout.php          ← Logout
└── customer/
    ├── login.php           ← Customer Login + Register
    ├── dashboard.php       ← Customer Home
    ├── shop.php            ← Browse & Search Products
    ├── cart.php            ← Shopping Cart + Checkout
    ├── orders.php          ← Order History + Tracking
    ├── profile.php         ← Update Profile & Password
    ├── header.php          ← Customer Navbar
    ├── footer.php          ← Customer Footer
    └── logout.php          ← Logout
```

---

## 🚀 Setup Instructions

### Step 1: Requirements
- XAMPP / WAMP / LAMP server
- PHP 7.4+
- MySQL 5.7+
- Browser (Chrome, Firefox, Edge)

### Step 2: Database Setup
1. Start Apache and MySQL in XAMPP
2. Open phpMyAdmin → http://localhost/phpmyadmin
3. Create a new database named **grocery_db**
4. Import **database.sql** file
5. Click "Go" / Import

### Step 3: File Setup
1. Copy the `grocery_management` folder to `htdocs` (XAMPP)
2. Open `config.php` and update if needed:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');   // Your MySQL username
   define('DB_PASS', '');       // Your MySQL password
   define('DB_NAME', 'grocery_db');
   ```

### Step 4: Run the Project
- Open browser → http://localhost/grocery_management/

---

## 🔑 Login Credentials

| Role     | Email                  | Password  |
|----------|------------------------|-----------|
| Admin    | admin@grocery.com      | admin123  |
| Customer | Register new account   | Your own  |

---

## ✨ Features

### 👨‍💼 Admin Panel
- ✅ Secure Admin Login
- ✅ Dashboard with Stats (Revenue, Orders, Products, Customers)
- ✅ Low Stock Alerts
- ✅ Product Management (Add/Edit/Delete/Search)
- ✅ Category Management
- ✅ Order Management with Status Updates
- ✅ Customer Management

### 🛍️ Customer Portal
- ✅ Customer Registration & Login
- ✅ Product Browsing with Search & Category Filter
- ✅ Shopping Cart (Add/Update/Remove)
- ✅ Checkout with Address & Payment Method
- ✅ Order Placement & Real-time Status Tracking
- ✅ Order History
- ✅ Profile Management & Password Change

---

## 🛠️ Technologies Used
- **Frontend:** HTML5, CSS3, JavaScript
- **Backend:** PHP 7.4+
- **Database:** MySQL
- **Icons:** Font Awesome 6
- **Server:** Apache (XAMPP)

---

## 📊 Database Tables
1. **admin** - Admin login credentials
2. **customers** - Customer accounts
3. **categories** - Product categories
4. **products** - Product catalog
5. **orders** - Customer orders
6. **order_items** - Items within each order
7. **cart** - Shopping cart (per customer)

---

*Developed as a Final Year College Project*
