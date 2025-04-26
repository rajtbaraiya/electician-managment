<?php
require_once 'config.php';

if (!isset($_GET['id'])) {
    header("Location: stock.php");
    exit();
}

$id = (int)$_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $category_id = (int)$_POST['category'];
    $brand_id = (int)$_POST['brand'];
    $model_number = sanitize($_POST['model_number']);
    $quantity = (int)$_POST['quantity'];
    $price = (float)$_POST['price'];
    $description = sanitize($_POST['description']);

    $sql = "UPDATE products SET name = ?, category_id = ?, brand_id = ?, model_number = ?, quantity = ?, price = ?, description = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("siisidsi", $name, $category_id, $brand_id, $model_number, $quantity, $price, $description, $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Product updated successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error updating product: " . $conn->error;
        $_SESSION['message_type'] = "danger";
    }

    header("Location: stock.php");
    exit();
}

// Get current product data
$sql = "SELECT p.*, c.name as category_name, b.name as brand_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        LEFT JOIN brands b ON p.brand_id = b.id 
        WHERE p.id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    $_SESSION['message'] = "Product not found!";
    $_SESSION['message_type'] = "danger";
    header("Location: stock.php");
    exit();
}

// Get all categories
$sql = "SELECT * FROM categories ORDER BY name";
$categories = $conn->query($sql);
if (!$categories) {
    die("Query failed: " . $conn->error);
}

// Get all brands
$sql = "SELECT * FROM brands ORDER BY name";
$brands = $conn->query($sql);
if (!$brands) {
    die("Query failed: " . $conn->error);
}

require_once 'header.php';
?>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Update Product</h5>
    </div>
    <div class="card-body">
        <form action="" method="POST" class="needs-validation" novalidate>
            <div class="mb-3">
                <label for="name" class="form-label">Product Name</label>
                <input type="text" class="form-control" id="name" name="name" 
                       value="<?php echo htmlspecialchars($product['name']); ?>" required>
                <div class="invalid-feedback">Please enter a product name.</div>
            </div>

            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <select class="form-select" id="category" name="category" required>
                    <option value="">Select Category</option>
                    <?php while ($category = $categories->fetch_assoc()) { ?>
                        <option value="<?php echo $category['id']; ?>" <?php if ($category['id'] === $product['category_id']) echo 'selected'; ?>><?php echo $category['name']; ?></option>
                    <?php } ?>
                </select>
                <div class="invalid-feedback">Please select a category.</div>
            </div>

            <div class="mb-3">
                <label for="brand" class="form-label">Brand</label>
                <select class="form-select" id="brand" name="brand" required>
                    <option value="">Select Brand</option>
                    <?php while ($brand = $brands->fetch_assoc()) { ?>
                        <option value="<?php echo $brand['id']; ?>" <?php if ($brand['id'] === $product['brand_id']) echo 'selected'; ?>><?php echo $brand['name']; ?></option>
                    <?php } ?>
                </select>
                <div class="invalid-feedback">Please select a brand.</div>
            </div>

            <div class="mb-3">
                <label for="model_number" class="form-label">Model Number</label>
                <input type="text" class="form-control" id="model_number" name="model_number" 
                       value="<?php echo htmlspecialchars($product['model_number']); ?>" required>
                <div class="invalid-feedback">Please enter a model number.</div>
            </div>

            <div class="mb-3">
                <label for="quantity" class="form-label">Quantity</label>
                <input type="number" class="form-control" id="quantity" name="quantity" 
                       value="<?php echo $product['quantity']; ?>" min="0" required>
                <div class="invalid-feedback">Please enter a valid quantity.</div>
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">Price (₹)</label>
                <input type="number" class="form-control" id="price" name="price" 
                       value="<?php echo $product['price']; ?>" min="0" step="0.01" required>
                <div class="invalid-feedback">Please enter a valid price.</div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" required><?php echo htmlspecialchars($product['description']); ?></textarea>
                <div class="invalid-feedback">Please enter a description.</div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Update Product</button>
                <a href="stock.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
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
</script>

<?php require_once 'footer.php'; ?>
