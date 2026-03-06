<?php
$page_title = 'Customers Management';
require_once 'header.php';

if(isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM customers WHERE id=$id");
    echo '<div class="alert alert-success">Customer deleted!</div>';
}

$customers = mysqli_query($conn, "SELECT c.*, COUNT(o.id) as order_count, SUM(o.total_amount) as total_spent 
    FROM customers c LEFT JOIN orders o ON c.id=o.customer_id GROUP BY c.id ORDER BY c.created_at DESC");
?>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-users" style="color:#27ae60"></i> All Customers (<?= mysqli_num_rows($customers) ?>)</h3>
    </div>
    <div class="card-body">
        <table>
            <thead>
                <tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Orders</th><th>Total Spent</th><th>Joined</th><th>Action</th></tr>
            </thead>
            <tbody>
            <?php while($c = mysqli_fetch_assoc($customers)): ?>
            <tr>
                <td>#<?= $c['id'] ?></td>
                <td><?= htmlspecialchars($c['name']) ?></td>
                <td><?= htmlspecialchars($c['email']) ?></td>
                <td><?= htmlspecialchars($c['phone']) ?></td>
                <td><?= $c['order_count'] ?></td>
                <td><?= CURRENCY ?><?= number_format($c['total_spent']??0, 2) ?></td>
                <td><?= date('d M Y', strtotime($c['created_at'])) ?></td>
                <td><a href="?delete=<?= $c['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete customer?')"><i class="fas fa-trash"></i></a></td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'footer.php'; ?>
