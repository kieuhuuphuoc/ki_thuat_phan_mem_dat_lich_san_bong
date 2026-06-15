<?php
require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../src/Models/User.php';

session_start();

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['email']) || !isset($data['password'])) {
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin']);
    exit;
}

$userModel = new User();
$user = $userModel->findByEmail($data['email']);

if ($user && password_verify($data['password'], $user['password'])) {
    $_SESSION['user'] = [
        'id' => $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'role' => $user['role']
    ];
    
    echo json_encode([
        'success' => true,
        'user' => $_SESSION['user']
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Sai email hoặc mật khẩu']);
}