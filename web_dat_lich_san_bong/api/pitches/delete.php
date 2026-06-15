<?php
require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../src/Models/Pitch.php';

session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Không có quyền']);
    exit;
}

$id = $_GET['id'] ?? 0;

$pitchModel = new Pitch();
$stmt = $pitchModel->db->prepare("DELETE FROM pitches WHERE id = ?");

if ($stmt->execute([$id])) {
    echo json_encode(['success' => true, 'message' => 'Xóa sân thành công']);
} else {
    echo json_encode(['success' => false, 'message' => 'Xóa sân thất bại']);
}
?>