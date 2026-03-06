<?php
$page_title = 'Orders Management';
require_once 'header.php';

$msg = '';

// Update order status
if(isset($_POST['update_status'])) {
    $oid = (int)$_POST['order_id'];
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    mysqli_query($conn, "UPDATE orders SET status='$status' WHERE id=$oid");
    $msg = "Order #$oid status updated to " . ucfirst($status) . "!";
}

$filter = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';
$where = $filter ? "WHERE o.status='$filter'" : "";

$orders = mysqli_query($conn, "SELECT o.*, c.name as cname, c.phone, c.email 
    FROM orders o JOIN customers c ON o.customer_id=c.id $where ORDER BY o.created_at DESC");

// View order details
$view_order = null;
if(isset($_GET['view'])) {
    $vid = (int)$_GET['view'];
    $view_order = mysqli_fetch_assoc(mysqli_query($conn, "SELECT o.*, c.name as cname, c.phone, c.email, c.address FROM orders o JOIN customers c ON o.customer_id=c.id WHERE o.id=$vid"));
    $order_items = mysqli_query($conn, "SELECT oi.*, p.name as pname, p.unit FROM order_items oi JOIN products p ON oi.product_id=p.id WHERE oi.order_id=$vid");
}
?>

<?php if($msg): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $msg ?></div><?php endif; ?>

<?php if($view_order): ?>
<!-- Order Details View -->
<div class="card" style="margin-bottom:25px">
    <div class="card-header">
        <h3>📋 Order #<?= $view_order['id'] ?> Details</h3>
        <a href="orders.php" class="btn btn-primary btn-sm">← Back to Orders</a>
    </div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:25px">
            <div style="background:#f8f9fa;padding:15px;border-radius:10px">
                <h4 style="color:#2c3e50;margin-bottom:10px">Customer Info</h4>
                <p><strong>Name:</strong> <?= htmlspecialchars($view_order['cname']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($view_order['email']) ?></p>
                <p><strong>Phone:</strong> <?= htmlspecialchars($view_order['phone']) ?></p>
            </div>
            <div style="background:#f8f9fa;padding:15px;border-radius:10px">
                <h4 style="color:#2c3e50;margin-bottom:10px">Order Info</h4>
                <p><strong>Date:</strong> <?= date('d M Y, h:i A', strtotime($view_order['created_at'])) ?></p>
                <p><strong>Status:</strong> <span class="status-badge status-<?= $view_order['status'] ?>"><?= ucfirst($view_order['status']) ?></span></p>
                <p><strong>Payment:</strong> <?= $view_order['payment_method'] ?></p>
                <p><strong>Total:</strong> <strong style="color:#27ae60"><?= CURRENCY ?><?= number_format($view_order['total_amount'],2) ?></strong></p>
            </div>
        </div>
        <h4 style="margin-bottom:15px;color:#2c3e50">Order Items</h4>
        <table>
            <thead><tr><th>Product</th><th>Unit</th><th>Price</th><th>Qty</th><th>Subtotal</th></tr></thead>
            <tbody>
            <?php while($item = mysqli_fetch_assoc($order_items)): ?>
            <tr>
                <td><?= htmlspecialchars($item['pname']) ?></td>
                <td><?= $item['unit'] ?></td>
                <td><?= CURRENCY ?><?= number_format($item['price'],2) ?></td>
                <td><?= $item['quantity'] ?></td>
                <td><?= CURRENCY ?><?= number_format($item['price']*$item['quantity'],2) ?></td>
            </tr>
            <?php endwhile; ?>
            <tr><td colspan="4" style="text-align:right"><strong>Grand Total:</strong></td><td><strong style="color:#27ae60"><?= CURRENCY ?><?= number_format($view_order['total_amount'],2) ?></strong></td></tr>
            </tbody>
        </table>
        <div style="margin-top:20px">
            <h4 style="margin-bottom:10px">Update Status</h4>
            <form method="POST" style="display:flex;gap:10px;align-items:center">
                <input type="hidden" name="order_id" value="<?= $view_order['id'] ?>">
                <select name="status" class="form-control" style="width:200px;padding:8px">
                    <?php foreach(['pending','confirmed','processing','delivered','cancelled'] as $s): ?>
                    <option value="<?= $s ?>" <?= $view_order['status']==$s?'selected':'' ?>><?= ucfirst($s) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name="update_status" class="btn btn-success">Update Status</button>
            </form>
        </div>
    </div>
</div>
<?php else: ?>

<!-- Filter Buttons -->
<div style="margin-bottom:20px;display:flex;gap:10px;flex-wrap:wrap">
    <a href="orders.php" class="btn btn-sm <?= !$filter?'btn-primary':'btn-primary' ?>" style="<?= !$filter?'':'background:#95a5a6;' ?>">All Orders</a>
    <?php foreach(['pending','confirmed','processing','delivered','cancelled'] as $s): ?>
    <a href="?status=<?= $s ?>" class="btn btn-sm" style="background:<?= $filter==$s?'#2c3e50':'#95a5a6' ?>;color:white"><?= ucfirst($s) ?></a>
    <?php endforeach; ?>
</div>

<!-- Orders Table -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-shopping-cart" style="color:#27ae60"></i> <?= $filter ? ucfirst($filter) : 'All' ?> Orders (<?= mysqli_num_rows($orders) ?>)</h3>
    </div>
    <div class="card-body">
        <table>
            <thead>
                <tr><th>Order ID</th><th>Customer</th><th>Phone</th><th>Amount</th><th>Payment</th><th>Status</th><th>Date</th><th>Actions</th></tr>
            </thead>
            <tbody>
            <?php while($o = mysqli_fetch_assoc($orders)): ?>
            <tr>
                <td>#<?= $o['id'] ?></td>
                <td><?= htmlspecialchars($o['cname']) ?></td>
                <td><?= htmlspecialchars($o['phone']) ?></td>
                <td><?= CURRENCY ?><?= number_format($o['total_amount'],2) ?></td>
                <td><?= $o['payment_method'] ?></td>
                <td><span class="status-badge status-<?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span></td>
                <td><?= date('d M Y', strtotime($o['created_at'])) ?></td>
                <td>
                    <a href="?view=<?= $o['id'] ?>" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i> View</a>
                </td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php require_once 'footer.php'; ?>
