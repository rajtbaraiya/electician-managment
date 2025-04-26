<?php
require_once 'config.php';

// Initialize bill items array in session if not exists
if (!isset($_SESSION['bill_items'])) {
    $_SESSION['bill_items'] = [];
}

// Add item to bill
if (isset($_POST['add_item'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];

    // Get product details
    $sql = "SELECT p.*, c.name as category_name, b.name as brand_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            LEFT JOIN brands b ON p.brand_id = b.id 
            WHERE p.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();

    if ($product && $quantity > 0 && $quantity <= $product['quantity']) {
        // Add to bill items
        $_SESSION['bill_items'][] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'category_name' => $product['category_name'],
            'brand_name' => $product['brand_name'],
            'model_number' => $product['model_number'],
            'quantity' => $quantity,
            'price' => $product['price'],
            'total' => $quantity * $product['price']
        ];

        $_SESSION['message'] = "Item added to bill!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Invalid product or quantity!";
        $_SESSION['message_type'] = "danger";
    }

    header("Location: bill.php");
    exit();
}

// Remove item from bill
if (isset($_GET['remove'])) {
    $index = (int)$_GET['remove'];
    if (isset($_SESSION['bill_items'][$index])) {
        unset($_SESSION['bill_items'][$index]);
        $_SESSION['bill_items'] = array_values($_SESSION['bill_items']); // Reindex array
    }
    header("Location: bill.php");
    exit();
}

// Clear bill
if (isset($_GET['clear'])) {
    $_SESSION['bill_items'] = [];
    header("Location: bill.php");
    exit();
}

require_once 'header.php';

// Get all categories for filter
$categories = $conn->query("SELECT * FROM categories ORDER BY name")->fetch_all(MYSQLI_ASSOC);

// Get all products with category and brand info
$sql = "SELECT p.*, c.name as category_name, b.name as brand_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        LEFT JOIN brands b ON p.brand_id = b.id 
        WHERE p.quantity > 0 
        ORDER BY c.name, b.name, p.name";
$result = $conn->query($sql);
if (!$result) {
    die("Query failed: " . $conn->error);
}
$products = $result->fetch_all(MYSQLI_ASSOC);

// Calculate totals
$subtotal = 0;
$gst_rate = 18; // 18% GST
foreach ($_SESSION['bill_items'] as $item) {
    $subtotal += $item['total'];
}
$gst_amount = ($subtotal * $gst_rate) / 100;
$total_amount = $subtotal + $gst_amount;
?>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Generate Bill</h5>
            </div>
            <div class="card-body">
                <form action="" method="POST" class="needs-validation" novalidate>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="category_filter" class="form-label">Filter by Category</label>
                            <select class="form-select" id="category_filter">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo htmlspecialchars($category['name']); ?>">
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="product_id" class="form-label">Select Product</label>
                            <select class="form-select" id="product_id" name="product_id" required>
                                <option value="">Choose product...</option>
                                <?php foreach ($products as $product): ?>
                                    <option value="<?php echo $product['id']; ?>" 
                                            data-category="<?php echo htmlspecialchars($product['category_name']); ?>"
                                            data-price="<?php echo $product['price']; ?>"
                                            data-stock="<?php echo $product['quantity']; ?>">
                                        <?php 
                                        echo htmlspecialchars($product['name']);
                                        if ($product['brand_name']) {
                                            echo ' - ' . htmlspecialchars($product['brand_name']);
                                        }
                                        if ($product['model_number']) {
                                            echo ' (' . htmlspecialchars($product['model_number']) . ')';
                                        }
                                        echo ' [Stock: ' . $product['quantity'] . ']';
                                        ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
                            <div class="invalid-feedback">Invalid quantity!</div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Price</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="text" class="form-control" id="price_display" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" name="add_item" class="btn btn-primary">
                            <i class="bi bi-plus"></i> Add to Bill
                        </button>
                    </div>
                </form>

                <?php if (!empty($_SESSION['bill_items'])): ?>
                    <div class="table-responsive mt-4">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Product Details</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-end">Price</th>
                                    <th class="text-end">Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($_SESSION['bill_items'] as $index => $item): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                            <?php if ($item['brand_name']): ?>
                                                <br>
                                                <small class="text-muted">Brand: <?php echo htmlspecialchars($item['brand_name']); ?></small>
                                            <?php endif; ?>
                                            <?php if ($item['model_number']): ?>
                                                <br>
                                                <small class="text-muted">Model: <?php echo htmlspecialchars($item['model_number']); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center"><?php echo $item['quantity']; ?></td>
                                        <td class="text-end">₹<?php echo number_format($item['price'], 2); ?></td>
                                        <td class="text-end">₹<?php echo number_format($item['total'], 2); ?></td>
                                        <td class="text-end">
                                            <a href="?remove=<?php echo $index; ?>" class="btn btn-danger btn-sm">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr class="table-light">
                                    <td colspan="3" class="text-end"><strong>Sub Total:</strong></td>
                                    <td class="text-end">₹<?php echo number_format($subtotal, 2); ?></td>
                                    <td></td>
                                </tr>
                                <tr class="table-light">
                                    <td colspan="3" class="text-end"><strong>GST (<?php echo $gst_rate; ?>%):</strong></td>
                                    <td class="text-end">₹<?php echo number_format($gst_amount, 2); ?></td>
                                    <td></td>
                                </tr>
                                <tr class="table-primary">
                                    <td colspan="3" class="text-end"><strong>Total Amount:</strong></td>
                                    <td class="text-end"><strong>₹<?php echo number_format($total_amount, 2); ?></strong></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex gap-2 mt-3">
                        <a href="print_bill.php" class="btn btn-success" target="_blank">
                            <i class="bi bi-printer"></i> Print Bill
                        </a>
                        <a href="?clear=1" class="btn btn-warning" onclick="return confirm('Clear all items from bill?')">
                            <i class="bi bi-x-circle"></i> Clear Bill
                        </a>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info mt-3">
                        No items added to bill yet.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">Quick Add Popular Items</h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php 
                    // Get top 5 most added products
                    $popular_products = array_slice($products, 0, 5);
                    foreach ($popular_products as $product):
                    ?>
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                                    <br>
                                    <small class="text-muted">
                                        <?php echo htmlspecialchars($product['category_name']); ?>
                                        <?php if ($product['brand_name']): ?>
                                            - <?php echo htmlspecialchars($product['brand_name']); ?>
                                        <?php endif; ?>
                                    </small>
                                </div>
                                <form action="" method="POST" class="d-flex gap-2">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <input type="number" name="quantity" value="1" min="1" 
                                           max="<?php echo $product['quantity']; ?>" 
                                           class="form-control form-control-sm" style="width: 70px;">
                                    <button type="submit" name="add_item" class="btn btn-primary btn-sm">
                                        <i class="bi bi-plus"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Form validation
(function () {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})()

// Category filter
document.getElementById('category_filter').addEventListener('change', function() {
    const category = this.value;
    const productSelect = document.getElementById('product_id');
    const options = productSelect.options;
    
    for (let i = 0; i < options.length; i++) {
        const option = options[i];
        if (!category || option.value === '' || option.dataset.category === category) {
            option.style.display = '';
        } else {
            option.style.display = 'none';
        }
    }
});

// Dynamic price display and quantity validation
document.getElementById('product_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const quantityInput = document.getElementById('quantity');
    const priceDisplay = document.getElementById('price_display');
    
    if (this.value) {
        const price = selectedOption.dataset.price;
        const stock = parseInt(selectedOption.dataset.stock);
        
        priceDisplay.value = '₹' + parseFloat(price).toFixed(2);
        quantityInput.max = stock;
        quantityInput.value = 1;
    } else {
        priceDisplay.value = '';
        quantityInput.value = '';
        quantityInput.removeAttribute('max');
    }
});
</script>

<?php require_once 'footer.php'; ?>
