<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $category_id = (int)$_POST['category_id'];
    $brand_id = (int)$_POST['brand_id'];
    $model_number = sanitize($_POST['model_number']);
    $quantity = (int)$_POST['quantity'];
    $price = (float)$_POST['price'];
    $description = sanitize($_POST['description']);

    $sql = "INSERT INTO products (name, category_id, brand_id, model_number, quantity, price, description) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siisids", $name, $category_id, $brand_id, $model_number, $quantity, $price, $description);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Product added successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error adding product: " . $conn->error;
        $_SESSION['message_type'] = "danger";
    }

    header("Location: add_product.php");
    exit();
}

// Get all categories
$categories = $conn->query("SELECT * FROM categories ORDER BY name")->fetch_all(MYSQLI_ASSOC);

require_once 'header.php';
?>

<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="card-title mb-0">Add New Product</h5>
    </div>
    <div class="card-body">
        <form action="" method="POST" class="needs-validation" novalidate>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Product Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                    <div class="invalid-feedback">Please enter a product name.</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="model_number" class="form-label">Model Number</label>
                    <input type="text" class="form-control" id="model_number" name="model_number">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="category_id" class="form-label">Category</label>
                    <select class="form-select" id="category_id" name="category_id" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>">
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">Please select a category.</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="brand_id" class="form-label">Brand</label>
                    <select class="form-select" id="brand_id" name="brand_id" required>
                        <option value="">Select Category First</option>
                    </select>
                    <div class="invalid-feedback">Please select a brand.</div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="quantity" class="form-label">Quantity</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" min="0" required>
                    <div class="invalid-feedback">Please enter a valid quantity.</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="price" class="form-label">Price (₹)</label>
                    <input type="number" class="form-control" id="price" name="price" min="0" step="0.01" required>
                    <div class="invalid-feedback">Please enter a valid price.</div>
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Add Product
                </button>
                <a href="stock.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Stock
                </a>
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

// Dynamic brand loading based on category
document.getElementById('category_id').addEventListener('change', function() {
    const categoryId = this.value;
    const brandSelect = document.getElementById('brand_id');
    
    // Clear current options
    brandSelect.innerHTML = '<option value="">Loading brands...</option>';
    
    if (categoryId) {
        // Fetch brands for selected category
        fetch(`get_brands.php?category_id=${categoryId}`)
            .then(response => response.json())
            .then(brands => {
                brandSelect.innerHTML = '<option value="">Select Brand</option>';
                brands.forEach(brand => {
                    const option = document.createElement('option');
                    option.value = brand.id;
                    option.textContent = brand.name;
                    brandSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error:', error);
                brandSelect.innerHTML = '<option value="">Error loading brands</option>';
            });
    } else {
        brandSelect.innerHTML = '<option value="">Select Category First</option>';
    }
});
</script>

<?php require_once 'footer.php'; ?>
