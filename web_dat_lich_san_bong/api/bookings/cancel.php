<?php
require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../src/Models/Booking.php';

session_start();

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
    exit;
}

$id = $_GET['id'] ?? 0;

$bookingModel = new Booking();

if ($bookingModel->cancel($id, $_SESSION['user']['id'])) {
    echo json_encode(['success' => true, 'message' => 'Hủy lịch thành công']);
} else {
    echo json_encode(['success' => false, 'message' => 'Hủy lịch thất bại']);
}