<?php
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_GET['category_id'])) {
    echo json_encode([]);
    exit();
}

$category_id = (int)$_GET['category_id'];

$sql = "SELECT id, name FROM brands WHERE category_id = ? ORDER BY name";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $category_id);
$stmt->execute();
$result = $stmt->get_result();
$brands = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode($brands);
