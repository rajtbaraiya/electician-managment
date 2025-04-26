<?php
require_once 'config.php';

// Add Category
if (isset($_POST['add_category'])) {
    $name = sanitize($_POST['category_name']);
    $sql = "INSERT INTO categories (name) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $name);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Category added successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error adding category!";
        $_SESSION['message_type'] = "danger";
    }
    header("Location: manage_categories.php");
    exit();
}

// Add Brand
if (isset($_POST['add_brand'])) {
    $name = sanitize($_POST['brand_name']);
    $category_id = (int)$_POST['category_id'];
    
    $sql = "INSERT INTO brands (name, category_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $name, $category_id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Brand added successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error adding brand!";
        $_SESSION['message_type'] = "danger";
    }
    header("Location: manage_categories.php");
    exit();
}

// Delete Category
if (isset($_GET['delete_category'])) {
    $id = (int)$_GET['delete_category'];
    $sql = "DELETE FROM categories WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Category deleted successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error deleting category!";
        $_SESSION['message_type'] = "danger";
    }
    header("Location: manage_categories.php");
    exit();
}

// Delete Brand
if (isset($_GET['delete_brand'])) {
    $id = (int)$_GET['delete_brand'];
    $sql = "DELETE FROM brands WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Brand deleted successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error deleting brand!";
        $_SESSION['message_type'] = "danger";
    }
    header("Location: manage_categories.php");
    exit();
}

// Get all categories
$categories = $conn->query("SELECT * FROM categories ORDER BY name")->fetch_all(MYSQLI_ASSOC);

// Get all brands with category names
$brands = $conn->query("
    SELECT b.*, c.name as category_name 
    FROM brands b 
    LEFT JOIN categories c ON b.category_id = c.id 
    ORDER BY c.name, b.name
")->fetch_all(MYSQLI_ASSOC);

require_once 'header.php';
?>

<div class="row">
    <!-- Categories Management -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Manage Categories</h5>
            </div>
            <div class="card-body">
                <form action="" method="POST" class="mb-4">
                    <div class="input-group">
                        <input type="text" name="category_name" class="form-control" placeholder="Enter category name" required>
                        <button type="submit" name="add_category" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Add Category
                        </button>
                    </div>
                </form>

                <div class="list-group">
                    <?php foreach ($categories as $category): ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <?php echo htmlspecialchars($category['name']); ?>
                        <a href="?delete_category=<?php echo $category['id']; ?>" 
                           class="btn btn-danger btn-sm" 
                           onclick="return confirm('Are you sure? This will delete all related brands too!')">
                            <i class="bi bi-trash"></i>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Brands Management -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">Manage Brands</h5>
            </div>
            <div class="card-body">
                <form action="" method="POST" class="mb-4">
                    <div class="mb-3">
                        <select name="category_id" class="form-select" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>">
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="input-group">
                        <input type="text" name="brand_name" class="form-control" placeholder="Enter brand name" required>
                        <button type="submit" name="add_brand" class="btn btn-success">
                            <i class="bi bi-plus-circle"></i> Add Brand
                        </button>
                    </div>
                </form>

                <div class="list-group">
                    <?php foreach ($brands as $brand): ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong><?php echo htmlspecialchars($brand['name']); ?></strong>
                            <br>
                            <small class="text-muted">Category: <?php echo htmlspecialchars($brand['category_name']); ?></small>
                        </div>
                        <a href="?delete_brand=<?php echo $brand['id']; ?>" 
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Are you sure?')">
                            <i class="bi bi-trash"></i>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
