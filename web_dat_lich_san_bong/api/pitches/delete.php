<?php
error_reporting(0);
ini_set('display_errors', 0);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE, OPTIONS");
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

$id = $_GET['id'] ?? 0;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Thiếu ID sân']);
    exit;
}

try {
    $db = new PDO("mysql:host=localhost;dbname=web_dat_lich_san_bong;charset=utf8", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi database']);
    exit;
}

$stmt = $db->prepare("DELETE FROM pitches WHERE id = ?");
$result = $stmt->execute([$id]);

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Xóa sân thành công']);
} else {
    echo json_encode(['success' => false, 'message' => 'Xóa sân thất bại']);
}
?>