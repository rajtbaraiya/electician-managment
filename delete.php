<?php
require_once 'config.php';

if (!isset($_GET['id'])) {
    header("Location: stock.php");
    exit();
}

$id = (int)$_GET['id'];

$sql = "DELETE FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $_SESSION['message'] = "Product deleted successfully!";
    $_SESSION['message_type'] = "success";
} else {
    $_SESSION['message'] = "Error deleting product: " . $conn->error;
    $_SESSION['message_type'] = "danger";
}

header("Location: stock.php");
exit();
