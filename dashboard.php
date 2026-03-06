<?php
$page_title = 'Dashboard';
require_once 'header.php';

// Stats
$total_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM products"))['c'];
$total_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM orders"))['c'];
$total_customers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM customers"))['c'];
$total_revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_amount) as t FROM orders WHERE status='delivered'"))['t'] ?? 0;
$pending_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM orders WHERE status='pending'"))['c'];

// Recent orders
$recent_orders = mysqli_query($conn, "SELECT o.*, c.name as customer_name FROM orders o 
    JOIN customers c ON o.customer_id=c.id ORDER BY o.created_at DESC LIMIT 8");

// Low stock products
$low_stock = mysqli_query($conn, "SELECT * FROM products WHERE stock < 10 ORDER BY stock ASC LIMIT 5");
?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:#eafaf1;">💰</div>
        <div>
            <h3><?= CURRENCY ?><?= number_format($total_revenue, 0) ?></h3>
            <p>Total Revenue</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#eaf4fd;">📦</div>
        <div>
            <h3><?= $total_orders ?></h3>
            <p>Total Orders</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fef9e7;">🛒</div>
        <div>
            <h3><?= $total_products ?></h3>
            <p>Products</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fdedec;">👥</div>
        <div>
            <h3><?= $total_customers ?></h3>
            <p>Customers</p>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:25px;">
    <!-- Recent Orders -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-shopping-cart" style="color:#27ae60"></i> Recent Orders</h3>
            <a href="orders.php" class="btn btn-success btn-sm">View All</a>
        </div>
        <div class="card-body">
            <table>
                <thead>
                    <tr><th>Order ID</th><th>Customer</th><th>Amount</th><th>Status</th><th>Date</th></tr>
                </thead>
                <tbody>
                <?php while($o = mysqli_fetch_assoc($recent_orders)): ?>
                <tr>
                    <td>#<?= $o['id'] ?></td>
                    <td><?= htmlspecialchars($o['customer_name']) ?></td>
                    <td><?= CURRENCY ?><?= number_format($o['total_amount'], 2) ?></td>
                    <td><span class="status-badge status-<?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span></td>
                    <td><?= date('d M Y', strtotime($o['created_at'])) ?></td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Low Stock Alert -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-exclamation-triangle" style="color:#f39c12"></i> Low Stock</h3>
            <a href="products.php" class="btn btn-warning btn-sm">Manage</a>
        </div>
        <div class="card-body">
            <table>
                <thead><tr><th>Product</th><th>Stock</th></tr></thead>
                <tbody>
                <?php while($p = mysqli_fetch_assoc($low_stock)): ?>
                <tr>
                    <td><?= htmlspecialchars($p['name']) ?></td>
                    <td><span style="color:<?= $p['stock']==0?'#e74c3c':'#f39c12' ?>;font-weight:700"><?= $p['stock'] ?></span></td>
                </tr>
                <?php endwhile; ?>
                <?php if(mysqli_num_rows($low_stock)==0): ?>
                <tr><td colspan="2" style="text-align:center;color:#27ae60">All stocks are good! ✅</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
