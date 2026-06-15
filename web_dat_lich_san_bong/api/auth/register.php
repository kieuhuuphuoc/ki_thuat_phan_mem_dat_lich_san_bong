<?php
require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../src/Models/User.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['name']) || !isset($data['email']) || !isset($data['password'])) {
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin']);
    exit;
}

$userModel = new User();

if ($userModel->findByEmail($data['email'])) {
    echo json_encode(['success' => false, 'message' => 'Email đã tồn tại']);
    exit;
}

if ($userModel->create($data['name'], $data['email'], $data['password'])) {
    echo json_encode(['success' => true, 'message' => 'Đăng ký thành công']);
} else {
    echo json_encode(['success' => false, 'message' => 'Đăng ký thất bại']);
}