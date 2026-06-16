<?php
error_reporting(0);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$id = $_GET['id'] ?? 0;

try {
    $db = new PDO("mysql:host=localhost;dbname=web_dat_lich_san_bong;charset=utf8", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi database']);
    exit;
}

$stmt = $db->prepare("SELECT * FROM pitches WHERE id = ?");
$stmt->execute([$id]);
$pitch = $stmt->fetch(PDO::FETCH_ASSOC);

if ($pitch) {
    echo json_encode(['success' => true, 'pitch' => $pitch]);
} else {
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy sân']);
}
?>