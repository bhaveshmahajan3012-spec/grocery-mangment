<?php
$page_title = 'Categories Management';
require_once 'header.php';

$msg = $err = '';

if(isset($_POST['add_category'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    if(mysqli_query($conn, "INSERT INTO categories (name,description) VALUES ('$name','$desc')"))
        $msg = "Category added!";
    else $err = mysqli_error($conn);
}
if(isset($_POST['update_category'])) {
    $id = (int)$_POST['id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    if(mysqli_query($conn, "UPDATE categories SET name='$name', description='$desc' WHERE id=$id"))
        $msg = "Category updated!";
}
if(isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM categories WHERE id=$id");
    $msg = "Category deleted!";
}

$edit_cat = null;
if(isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    $edit_cat = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM categories WHERE id=$eid"));
}

$categories = mysqli_query($conn, "SELECT c.*, COUNT(p.id) as product_count FROM categories c LEFT JOIN products p ON c.id=p.category_id GROUP BY c.id ORDER BY c.name");
?>

<?php if($msg): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>
<?php if($err): ?><div class="alert alert-danger"><?= $err ?></div><?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 2fr;gap:25px">
    <div class="card">
        <div class="card-header"><h3><?= $edit_cat?'Edit':'Add' ?> Category</h3></div>
        <div class="card-body">
            <form method="POST">
                <?php if($edit_cat): ?><input type="hidden" name="id" value="<?= $edit_cat['id'] ?>"><?php endif; ?>
                <div class="form-group">
                    <label>Category Name *</label>
                    <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($edit_cat['name']??'') ?>">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($edit_cat['description']??'') ?></textarea>
                </div>
                <button type="submit" name="<?= $edit_cat?'update_category':'add_category' ?>" class="btn btn-success">
                    <i class="fas fa-save"></i> <?= $edit_cat?'Update':'Add' ?>
                </button>
                <?php if($edit_cat): ?><a href="categories.php" class="btn btn-primary" style="margin-left:8px">Cancel</a><?php endif; ?>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3>All Categories (<?= mysqli_num_rows($categories) ?>)</h3></div>
        <div class="card-body">
            <table>
                <thead><tr><th>ID</th><th>Name</th><th>Description</th><th>Products</th><th>Actions</th></tr></thead>
                <tbody>
                <?php while($c = mysqli_fetch_assoc($categories)): ?>
                <tr>
                    <td>#<?= $c['id'] ?></td>
                    <td><?= htmlspecialchars($c['name']) ?></td>
                    <td><?= htmlspecialchars($c['description']) ?></td>
                    <td><span class="status-badge status-active"><?= $c['product_count'] ?> items</span></td>
                    <td>
                        <a href="?edit=<?= $c['id'] ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                        <a href="?delete=<?= $c['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete category?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
