<?php
require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../src/Utils/Database.php';

session_start();

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
    exit;
}

$db = Database::getInstance()->getConnection();

if ($_SESSION['user']['role'] === 'admin') {
    // Admin xem tất cả bookings - lấy đúng tên người dùng
    $stmt = $db->prepare("
        SELECT 
            b.*, 
            u.name as user_name, 
            p.name as pitch_name 
        FROM bookings b 
        LEFT JOIN users u ON b.user_id = u.id 
        LEFT JOIN pitches p ON b.pitch_id = p.id 
        ORDER BY b.booking_date DESC, b.id DESC
    ");
    $stmt->execute();
    $bookings = $stmt->fetchAll();
} else {
    // User chỉ xem bookings của mình
    $stmt = $db->prepare("
        SELECT 
            b.*, 
            p.name as pitch_name 
        FROM bookings b 
        LEFT JOIN pitches p ON b.pitch_id = p.id 
        WHERE b.user_id = ? 
        ORDER BY b.booking_date DESC, b.id DESC
    ");
    $stmt->execute([$_SESSION['user']['id']]);
    $bookings = $stmt->fetchAll();
}

echo json_encode(['success' => true, 'bookings' => $bookings]);
?>