<?php
require_once 'config.php';
require_once 'header.php';

$sql = "SELECT p.*, c.name as category_name, b.name as brand_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        LEFT JOIN brands b ON p.brand_id = b.id 
        ORDER BY c.name, b.name, p.name";
$result = $conn->query($sql);
if (!$result) {
    die("Query failed: " . $conn->error);
}
$products = $result->fetch_all(MYSQLI_ASSOC);

// Get category totals
$sql = "SELECT c.name, COUNT(p.id) as count, SUM(p.quantity * p.price) as value
        FROM categories c
        LEFT JOIN products p ON c.id = p.category_id
        GROUP BY c.id, c.name
        ORDER BY value DESC";
$result = $conn->query($sql);
if (!$result) {
    die("Query failed: " . $conn->error);
}
$category_stats = $result->fetch_all(MYSQLI_ASSOC);
?>

<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Stock Overview</h5>
                <div>
                    <a href="add_product.php" class="btn btn-light btn-sm">
                        <i class="bi bi-plus-circle"></i> Add Product
                    </a>
                    <a href="manage_categories.php" class="btn btn-light btn-sm">
                        <i class="bi bi-gear"></i> Manage Categories
                    </a>
                </div>
            </div>
            <div class="card-body">
                <?php if (count($products) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Product Details</th>
                                    <th>Category</th>
                                    <th>Stock</th>
                                    <th>Price</th>
                                    <th>Value</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                    <tr <?php echo $product['quantity'] < 5 ? 'class="table-danger"' : ''; ?>>
                                        <td>
                                            <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                                            <?php if ($product['model_number']): ?>
                                                <br>
                                                <small class="text-muted">Model: <?php echo htmlspecialchars($product['model_number']); ?></small>
                                            <?php endif; ?>
                                            <?php if ($product['brand_name']): ?>
                                                <br>
                                                <small class="text-muted">Brand: <?php echo htmlspecialchars($product['brand_name']); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                        <td>
                                            <?php if ($product['quantity'] < 5): ?>
                                                <span class="badge bg-danger"><?php echo $product['quantity']; ?></span>
                                            <?php else: ?>
                                                <?php echo $product['quantity']; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>₹<?php echo number_format($product['price'], 2); ?></td>
                                        <td>₹<?php echo number_format($product['quantity'] * $product['price'], 2); ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="update.php?id=<?php echo $product['id']; ?>" 
                                                   class="btn btn-warning btn-sm" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="delete.php?id=<?php echo $product['id']; ?>" 
                                                   class="btn btn-danger btn-sm" 
                                                   onclick="return confirm('Are you sure you want to delete this product?')"
                                                   title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        No products in stock. <a href="add_product.php">Add some products</a> to get started.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Category Stats -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">Category Overview</h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php foreach ($category_stats as $stat): ?>
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0"><?php echo htmlspecialchars($stat['name']); ?></h6>
                                    <small class="text-muted"><?php echo $stat['count']; ?> products</small>
                                </div>
                                <strong>₹<?php echo number_format($stat['value'] ?? 0, 2); ?></strong>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Low Stock Alert -->
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h5 class="card-title mb-0">Low Stock Alert</h5>
            </div>
            <div class="card-body p-0">
                <?php
                $low_stock = array_filter($products, function($p) { return $p['quantity'] < 5; });
                if (count($low_stock) > 0):
                ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($low_stock as $product): ?>
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0"><?php echo htmlspecialchars($product['name']); ?></h6>
                                        <small class="text-muted">
                                            <?php echo htmlspecialchars($product['category_name']); ?>
                                            <?php if ($product['brand_name']): ?>
                                                - <?php echo htmlspecialchars($product['brand_name']); ?>
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                    <span class="badge bg-danger"><?php echo $product['quantity']; ?> left</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="card-body">
                        <p class="text-success mb-0">No products are running low on stock!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
