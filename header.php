<?php
require_once '../config.php';
if(!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }
$admin_name = $_SESSION['admin_name'];

// Get counts for sidebar badges
$total_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM products"))['c'];
$total_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM orders"))['c'];
$total_customers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM customers"))['c'];
$pending_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM orders WHERE status='pending'"))['c'];
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Admin Panel' ?> - FreshMart</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; display: flex; min-height: 100vh; }

        /* SIDEBAR */
        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, #1a252f 0%, #2c3e50 100%);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
        }
        .sidebar-brand {
            padding: 25px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-align: center;
        }
        .sidebar-brand h2 { font-size: 22px; color: #2ecc71; }
        .sidebar-brand p { font-size: 12px; color: #aaa; }
        .sidebar-menu { padding: 20px 0; }
        .menu-label { font-size: 11px; color: #7f8c8d; padding: 10px 20px 5px; text-transform: uppercase; letter-spacing: 1px; }
        .sidebar-menu a {
            display: flex; align-items: center; gap: 12px;
            padding: 13px 20px; color: #bdc3c7; text-decoration: none;
            transition: all 0.3s; position: relative;
        }
        .sidebar-menu a:hover, .sidebar-menu a.active {
            background: rgba(46,204,113,0.15); color: #2ecc71;
            border-left: 4px solid #2ecc71;
        }
        .sidebar-menu a i { width: 20px; }
        .badge {
            background: #e74c3c; color: white; font-size: 11px;
            padding: 2px 8px; border-radius: 10px; margin-left: auto;
        }
        .admin-info { padding: 20px; border-top: 1px solid rgba(255,255,255,0.1); }
        .admin-info p { font-size: 13px; color: #aaa; }
        .admin-info strong { color: white; }

        /* MAIN CONTENT */
        .main-content { margin-left: 260px; flex: 1; display: flex; flex-direction: column; }
        .topbar {
            background: white; padding: 15px 30px;
            display: flex; justify-content: space-between; align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            position: sticky; top: 0; z-index: 100;
        }
        .topbar h1 { font-size: 22px; color: #2c3e50; }
        .topbar-right { display: flex; align-items: center; gap: 15px; }
        .btn-logout {
            background: #e74c3c; color: white; padding: 8px 18px;
            border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 600;
        }
        .btn-logout:hover { background: #c0392b; }
        .content { padding: 25px 30px; }

        /* CARDS */
        .stats-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 20px; margin-bottom: 30px; }
        .stat-card {
            background: white; border-radius: 15px; padding: 25px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
            display: flex; align-items: center; gap: 20px;
            transition: transform 0.3s;
        }
        .stat-card:hover { transform: translateY(-3px); }
        .stat-icon { width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 26px; }
        .stat-card h3 { font-size: 28px; color: #2c3e50; }
        .stat-card p { font-size: 13px; color: #777; }

        /* TABLES */
        .card { background: white; border-radius: 15px; box-shadow: 0 3px 15px rgba(0,0,0,0.08); overflow: hidden; margin-bottom: 25px; }
        .card-header { padding: 20px 25px; border-bottom: 2px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; }
        .card-header h3 { font-size: 18px; color: #2c3e50; }
        .card-body { padding: 20px 25px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8f9fa; padding: 12px 15px; text-align: left; font-size: 13px; color: #666; text-transform: uppercase; }
        td { padding: 13px 15px; border-bottom: 1px solid #f0f0f0; font-size: 14px; color: #444; }
        tr:hover td { background: #fafafa; }
        .status-badge { padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-confirmed { background: #d1ecf1; color: #0c5460; }
        .status-delivered { background: #d4edda; color: #155724; }
        .status-cancelled { background: #f8d7da; color: #721c24; }
        .status-processing { background: #e2d9f3; color: #491d8b; }
        .status-active { background: #d4edda; color: #155724; }
        .status-inactive { background: #f8d7da; color: #721c24; }

        /* FORMS */
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-weight: 600; color: #444; margin-bottom: 7px; font-size: 14px; }
        .form-control {
            width: 100%; padding: 11px 15px;
            border: 2px solid #e0e0e0; border-radius: 10px;
            font-size: 14px; transition: border-color 0.3s; outline: none;
        }
        .form-control:focus { border-color: #27ae60; }
        .btn { padding: 10px 22px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; border: none; text-decoration: none; display: inline-block; transition: all 0.3s; }
        .btn-success { background: #27ae60; color: white; }
        .btn-success:hover { background: #1e8449; }
        .btn-danger { background: #e74c3c; color: white; }
        .btn-danger:hover { background: #c0392b; }
        .btn-primary { background: #2980b9; color: white; }
        .btn-primary:hover { background: #1a6694; }
        .btn-warning { background: #f39c12; color: white; }
        .btn-warning:hover { background: #d68910; }
        .btn-sm { padding: 6px 14px; font-size: 13px; }
        .alert { padding: 12px 15px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-danger { background: #f8d7da; color: #721c24; }

        @media(max-width:900px) {
            .sidebar { width: 60px; }
            .sidebar span, .menu-label, .admin-info p, .sidebar-brand p, .badge { display: none; }
            .main-content { margin-left: 60px; }
            .stats-grid { grid-template-columns: 1fr 1fr; }
        }
    </style>
</head>
<body>
<div class="sidebar">
    <div class="sidebar-brand">
        <h2>🛒 FreshMart</h2>
        <p>Admin Dashboard</p>
    </div>
    <div class="sidebar-menu">
        <div class="menu-label">Main</div>
        <a href="dashboard.php" class="<?= $current_page=='dashboard.php'?'active':'' ?>">
            <i class="fas fa-home"></i><span>Dashboard</span>
        </a>
        <div class="menu-label">Inventory</div>
        <a href="categories.php" class="<?= $current_page=='categories.php'?'active':'' ?>">
            <i class="fas fa-tags"></i><span>Categories</span>
        </a>
        <a href="products.php" class="<?= $current_page=='products.php'?'active':'' ?>">
            <i class="fas fa-box"></i><span>Products</span>
            <span class="badge"><?= $total_products ?></span>
        </a>
        <div class="menu-label">Sales</div>
        <a href="orders.php" class="<?= $current_page=='orders.php'?'active':'' ?>">
            <i class="fas fa-shopping-cart"></i><span>Orders</span>
            <?php if($pending_orders > 0): ?><span class="badge"><?= $pending_orders ?></span><?php endif; ?>
        </a>
        <div class="menu-label">Users</div>
        <a href="customers.php" class="<?= $current_page=='customers.php'?'active':'' ?>">
            <i class="fas fa-users"></i><span>Customers</span>
            <span class="badge"><?= $total_customers ?></span>
        </a>
    </div>
    <div class="admin-info">
        <p>Logged in as<br><strong><?= htmlspecialchars($admin_name) ?></strong></p>
    </div>
</div>
<div class="main-content">
    <div class="topbar">
        <h1><?= $page_title ?? 'Dashboard' ?></h1>
        <div class="topbar-right">
            <span style="color:#666;font-size:14px;"><i class="fas fa-user-shield"></i> <?= htmlspecialchars($admin_name) ?></span>
            <a href="logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
    <div class="content">
