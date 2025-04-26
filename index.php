<?php
require_once 'config.php';
require_once 'header.php';

// Get quick statistics
$total_products = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];
$low_stock = $conn->query("SELECT COUNT(*) as count FROM products WHERE quantity < 5")->fetch_assoc()['count'];
$total_value = $conn->query("SELECT SUM(quantity * price) as total FROM products")->fetch_assoc()['total'];
?>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title">Total Products</h5>
                <p class="card-text display-4"><?php echo $total_products; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h5 class="card-title">Low Stock Items</h5>
                <p class="card-text display-4"><?php echo $low_stock; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title">Total Inventory Value</h5>
                <p class="card-text display-4">₹<?php echo number_format($total_value, 2); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="add_product.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Add New Product
                    </a>
                    <a href="bill.php" class="btn btn-success">
                        <i class="bi bi-receipt"></i> Generate New Bill
                    </a>
                    <a href="stock.php" class="btn btn-warning">
                        <i class="bi bi-box"></i> Check Stock
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Low Stock Alert</h5>
            </div>
            <div class="card-body">
                <?php
                $low_stock_items = $conn->query("SELECT name, quantity FROM products WHERE quantity < 5 ORDER BY quantity ASC LIMIT 5");
                if ($low_stock_items->num_rows > 0) {
                    echo '<ul class="list-group">';
                    while ($item = $low_stock_items->fetch_assoc()) {
                        echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
                        echo $item['name'];
                        echo '<span class="badge bg-danger rounded-pill">' . $item['quantity'] . '</span>';
                        echo '</li>';
                    }
                    echo '</ul>';
                } else {
                    echo '<p class="text-success">No items are running low on stock!</p>';
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
