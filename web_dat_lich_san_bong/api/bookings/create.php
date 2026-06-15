<?php
require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../src/Models/Booking.php';

session_start();

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$bookingModel = new Booking();

if ($bookingModel->checkConflict($data['pitch_id'], $data['booking_date'], $data['start_time'], $data['end_time'])) {
    echo json_encode(['success' => false, 'message' => 'Khung giờ này đã có người đặt']);
    exit;
}

if ($bookingModel->create($_SESSION['user']['id'], $data['pitch_id'], $data['booking_date'], $data['start_time'], $data['end_time'])) {
    echo json_encode(['success' => true, 'message' => 'Đặt sân thành công']);
} else {
    echo json_encode(['success' => false, 'message' => 'Đặt sân thất bại']);
}