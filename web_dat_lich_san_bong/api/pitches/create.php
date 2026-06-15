<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

session_start();

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
    exit;
}

if ($_SESSION['user']['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Không có quyền admin']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Không nhận được dữ liệu']);
    exit;
}

$name = trim($data['name'] ?? '');
$type = trim($data['type'] ?? '');
$location = trim($data['location'] ?? '');
$price = intval($data['price'] ?? 0);
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
    echo json_encode(['success' => false, 'message' => 'Lỗi database: ' . $e->getMessage()]);
    exit;
}

$stmt = $db->prepare("INSERT INTO pitches (name, type, location, price_per_hour, description) VALUES (?, ?, ?, ?, ?)");
$result = $stmt->execute([$name, $type, $location, $price, $description]);

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Thêm sân thành công']);
} else {
    echo json_encode(['success' => false, 'message' => 'Thêm sân thất bại']);
}
?>