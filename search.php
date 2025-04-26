<?php
require_once 'config.php';
require_once 'header.php';

$search_results = [];
if (isset($_GET['query']) && !empty($_GET['query'])) {
    $query = sanitize($_GET['query']);
    $sql = "SELECT p.*, c.name as category_name, b.name as brand_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            LEFT JOIN brands b ON p.brand_id = b.id 
            WHERE p.name LIKE ? 
               OR p.model_number LIKE ? 
               OR c.name LIKE ? 
               OR b.name LIKE ? 
               OR p.description LIKE ?
            ORDER BY c.name, b.name, p.name";
    $stmt = $conn->prepare($sql);
    $search_term = "%{$query}%";
    $stmt->bind_param("sssss", $search_term, $search_term, $search_term, $search_term, $search_term);
    $stmt->execute();
    $result = $stmt->get_result();
    $search_results = $result->fetch_all(MYSQLI_ASSOC);
}

// Get all categories for filter
$categories = $conn->query("SELECT * FROM categories ORDER BY name")->fetch_all(MYSQLI_ASSOC);
?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Search Products</h5>
            </div>
            <div class="card-body">
                <form action="" method="GET" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" class="form-control" name="query" 
                                       placeholder="Search by product name, model, category, or brand..." 
                                       value="<?php echo isset($_GET['query']) ? htmlspecialchars($_GET['query']) : ''; ?>">
                                <button class="btn btn-primary" type="submit">
                                    <i class="bi bi-search"></i> Search
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <select name="category" class="form-select">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>"
                                            <?php echo (isset($_GET['category']) && $_GET['category'] == $category['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </form>

                <?php if (isset($_GET['query'])): ?>
                    <?php if (count($search_results) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Product Details</th>
                                        <th>Category</th>
                                        <th>Stock</th>
                                        <th>Price</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($search_results as $product): ?>
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
                                                <?php if ($product['description']): ?>
                                                    <br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($product['description']); ?></small>
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
                            No products found matching your search criteria.
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
