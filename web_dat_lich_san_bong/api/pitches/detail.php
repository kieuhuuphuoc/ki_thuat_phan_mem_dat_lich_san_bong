<?php
require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../src/Models/Pitch.php';

$id = $_GET['id'] ?? 0;

$pitchModel = new Pitch();
$pitch = $pitchModel->find($id);

if ($pitch) {
    echo json_encode(['success' => true, 'pitch' => $pitch]);
} else {
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy sân']);
}