<?php
error_reporting(0);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Không có quyền']);
    exit;
}

$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

if (!$data || !isset($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
    exit;
}

$id = intval($data['id']);
$name = trim($data['name'] ?? '');
$type = trim($data['type'] ?? '');
$location = trim($data['location'] ?? '');
$price = floatval($data['price'] ?? 0);
$description = trim($data['description'] ?? '');

if (empty($name)) {
    echo json_encode(['success' => false, 'message' => 'Tên sân không được để trống']);
    exit;
}

if ($price <= 0) {
    echo json_encode(['success' => false, 'message' => 'Giá sân phải lớn hơn 0']);
    exit;
}

try {
    $db = new PDO("mysql:host=localhost;dbname=web_dat_lich_san_bong;charset=utf8", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi database']);
    exit;
}

$sql = "UPDATE pitches SET name = :name, type = :type, location = :location, price_per_hour = :price, description = :description WHERE id = :id";
$stmt = $db->prepare($sql);
$result = $stmt->execute([
    ':id' => $id,
    ':name' => $name,
    ':type' => $type,
    ':location' => $location,
    ':price' => $price,
    ':description' => $description
]);

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Cập nhật thành công']);
} else {
    echo json_encode(['success' => false, 'message' => 'Cập nhật thất bại']);
}
?>