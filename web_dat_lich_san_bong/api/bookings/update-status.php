<?php
require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../src/Utils/Database.php';

session_start();

// Kiểm tra đăng nhập và quyền admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Không có quyền']);
    exit;
}

$id = $_GET['id'] ?? 0;
$status = $_GET['status'] ?? '';

if (!$id || !$status) {
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin']);
    exit;
}

// Các trạng thái hợp lệ
$validStatus = ['pending', 'confirmed', 'cancelled'];
if (!in_array($status, $validStatus)) {
    echo json_encode(['success' => false, 'message' => 'Trạng thái không hợp lệ']);
    exit;
}

$db = Database::getInstance()->getConnection();

$stmt = $db->prepare("UPDATE bookings SET status = ? WHERE id = ?");
$result = $stmt->execute([$status, $id]);

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Cập nhật thành công']);
} else {
    echo json_encode(['success' => false, 'message' => 'Cập nhật thất bại']);
}
?>