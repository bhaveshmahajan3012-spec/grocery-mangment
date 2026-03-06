<?php
$page_title = 'Products Management';
require_once 'header.php';

$msg = $err = '';

// Add Product
if(isset($_POST['add_product'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $cat = (int)$_POST['category_id'];
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];
    $unit = mysqli_real_escape_string($conn, $_POST['unit']);
    $status = $_POST['status'];

    $image_name = '';
    if(isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $allowed = ['jpg','jpeg','png','webp'];
        $ext = strtolower(pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION));
        if(in_array($ext, $allowed)) {
            $image_name = time() . '_' . basename($_FILES['product_image']['name']);
            move_uploaded_file($_FILES['product_image']['tmp_name'], '../assets/images/products/' . $image_name);
        }
    }

    $sql = "INSERT INTO products (category_id,name,description,price,stock,unit,status,image) VALUES ($cat,'$name','$desc',$price,$stock,'$unit','$status','$image_name')";
    if(mysqli_query($conn, $sql)) $msg = "Product added successfully!";
    else $err = "Error: " . mysqli_error($conn);
}

// Update Product
if(isset($_POST['update_product'])) {
    $id = (int)$_POST['id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $cat = (int)$_POST['category_id'];
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];
    $unit = mysqli_real_escape_string($conn, $_POST['unit']);
    $status = $_POST['status'];
    $sql = "UPDATE products SET category_id=$cat, name='$name', description='$desc', price=$price, stock=$stock, unit='$unit', status='$status' WHERE id=$id";
    if(mysqli_query($conn, $sql)) $msg = "Product updated successfully!";
    else $err = "Error: " . mysqli_error($conn);
}

// Delete Product
if(isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM products WHERE id=$id");
    $msg = "Product deleted!";
}

$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");
$cat_arr = [];
while($c = mysqli_fetch_assoc($categories)) { $cat_arr[$c['id']] = $c['name']; }

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$filter_cat = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;
$where = "WHERE 1=1";
if($search) $where .= " AND p.name LIKE '%$search%'";
if($filter_cat) $where .= " AND p.category_id=$filter_cat";
$products = mysqli_query($conn, "SELECT p.*, c.name as cat_name FROM products p LEFT JOIN categories c ON p.category_id=c.id $where ORDER BY p.id DESC");

// Edit mode
$edit_product = null;
if(isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $edit_result = mysqli_query($conn, "SELECT * FROM products WHERE id=$edit_id");
    $edit_product = mysqli_fetch_assoc($edit_result);
}
?>

<?php if($msg): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $msg ?></div><?php endif; ?>
<?php if($err): ?><div class="alert alert-danger"><i class="fas fa-times-circle"></i> <?= $err ?></div><?php endif; ?>

<!-- Add/Edit Form -->
<div class="card" style="margin-bottom:25px">
    <div class="card-header">
        <h3><i class="fas fa-<?= $edit_product?'edit':'plus' ?>" style="color:#27ae60"></i> <?= $edit_product?'Edit':'Add New' ?> Product</h3>
    </div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <?php if($edit_product): ?><input type="hidden" name="id" value="<?= $edit_product['id'] ?>"><?php endif; ?>
            <div class="form-row">
                <div class="form-group">
                    <label>Product Name *</label>
                    <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($edit_product['name'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Category *</label>
                    <select name="category_id" class="form-control" required>
                        <option value="">Select Category</option>
                        <?php foreach($cat_arr as $cid=>$cname): ?>
                        <option value="<?= $cid ?>" <?= ($edit_product['category_id']??'')==$cid?'selected':'' ?>><?= htmlspecialchars($cname) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Price (₹) *</label>
                    <input type="number" step="0.01" name="price" class="form-control" required value="<?= $edit_product['price'] ?? '' ?>">
                </div>
                <div class="form-group">
                    <label>Stock Quantity *</label>
                    <input type="number" name="stock" class="form-control" required value="<?= $edit_product['stock'] ?? '' ?>">
                </div>
                <div class="form-group">
                    <label>Unit</label>
                    <select name="unit" class="form-control">
                        <?php foreach(['kg','gram','litre','ml','piece','packet','dozen','bottle'] as $u): ?>
                        <option value="<?= $u ?>" <?= ($edit_product['unit']??'')==$u?'selected':'' ?>><?= ucfirst($u) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="active" <?= ($edit_product['status']??'active')=='active'?'selected':'' ?>>Active</option>
                        <option value="inactive" <?= ($edit_product['status']??'')=='inactive'?'selected':'' ?>>Inactive</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Product Image</label>
                <input type="file" name="product_image" class="form-control" accept="image/*">
                <?php if(!empty($edit_product['image'])): ?>
                    <img src="../assets/images/products/<?= htmlspecialchars($edit_product['image']) ?>"
                         style="width:80px;height:80px;object-fit:cover;border-radius:10px;margin-top:8px;box-shadow:0 2px 8px rgba(0,0,0,0.15)">
                <?php endif; ?>
            </div>
            <button type="submit" name="<?= $edit_product?'update_product':'add_product' ?>" class="btn btn-success">
                <i class="fas fa-save"></i> <?= $edit_product?'Update':'Add' ?> Product
            </button>
            <?php if($edit_product): ?><a href="products.php" class="btn btn-primary" style="margin-left:10px">Cancel</a><?php endif; ?>
        </form>
    </div>
</div>

<!-- Search & Filter -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-boxes" style="color:#27ae60"></i> All Products (<?= mysqli_num_rows($products) ?>)</h3>
        <form method="GET" style="display:flex;gap:10px;align-items:center">
            <input type="text" name="search" placeholder="Search products..." class="form-control" style="width:200px;padding:8px 12px" value="<?= htmlspecialchars($search) ?>">
            <select name="cat" class="form-control" style="width:160px;padding:8px">
                <option value="">All Categories</option>
                <?php foreach($cat_arr as $cid=>$cname): ?>
                <option value="<?= $cid ?>" <?= $filter_cat==$cid?'selected':'' ?>><?= htmlspecialchars($cname) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            <a href="products.php" class="btn btn-sm" style="background:#95a5a6;color:white">Reset</a>
        </form>
    </div>
    <div class="card-body">
        <table>
            <thead>
                <tr><th>Photo</th><th>ID</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Unit</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
            <?php while($p = mysqli_fetch_assoc($products)): ?>
            <?php
            $img_path = '../assets/images/products/' . $p['image'];
            $has_img = !empty($p['image']) && file_exists($img_path);
            ?>
            <tr>
                <td>
                    <?php if($has_img): ?>
                        <img src="<?= htmlspecialchars($img_path) ?>" style="width:55px;height:55px;object-fit:cover;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.12)">
                    <?php else: ?>
                        <span style="font-size:30px"><?= getAdminEmoji($p['name']) ?></span>
                    <?php endif; ?>
                </td>
                <td>#<?= $p['id'] ?></td>
                <td><?= htmlspecialchars($p['name']) ?></td>
                <td><?= htmlspecialchars($p['cat_name'] ?? 'N/A') ?></td>
                <td><?= CURRENCY ?><?= number_format($p['price'], 2) ?></td>
                <td><span style="color:<?= $p['stock']<10?'#e74c3c':($p['stock']<30?'#f39c12':'#27ae60') ?>;font-weight:700"><?= $p['stock'] ?></span></td>
                <td><?= $p['unit'] ?></td>
                <td><span class="status-badge status-<?= $p['status'] ?>"><?= ucfirst($p['status']) ?></span></td>
                <td>
                    <a href="?edit=<?= $p['id'] ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                    <a href="?delete=<?= $p['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this product?')"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'footer.php'; ?>

<?php
function getAdminEmoji($name) {
    $n = strtolower($name);
    if(strpos($n,'tomato')!==false) return '🍅';
    if(strpos($n,'onion')!==false) return '🧅';
    if(strpos($n,'potato')!==false) return '🥔';
    if(strpos($n,'banana')!==false) return '🍌';
    if(strpos($n,'apple')!==false) return '🍎';
    if(strpos($n,'milk')!==false) return '🥛';
    if(strpos($n,'egg')!==false) return '🥚';
    if(strpos($n,'bread')!==false) return '🍞';
    if(strpos($n,'rice')!==false) return '🍚';
    if(strpos($n,'oil')!==false) return '🫙';
    if(strpos($n,'water')!==false) return '💧';
    if(strpos($n,'juice')!==false) return '🧃';
    if(strpos($n,'paneer')!==false) return '🧀';
    if(strpos($n,'dal')!==false) return '🫘';
    if(strpos($n,'chips')!==false) return '🍟';
    if(strpos($n,'biscuit')!==false) return '🍪';
    if(strpos($n,'mirchi')!==false||strpos($n,'masala')!==false) return '🌶️';
    return '🛒';
}
?>
