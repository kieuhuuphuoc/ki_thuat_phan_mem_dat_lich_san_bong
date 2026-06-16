<?php
error_reporting(0);
ini_set('display_errors', 0);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    $db = new PDO("mysql:host=localhost;dbname=web_dat_lich_san_bong;charset=utf8", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'pitches' => [], 'message' => 'Lỗi database']);
    exit;
}

$stmt = $db->query("SELECT * FROM pitches ORDER BY id DESC");
$pitches = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'pitches' => $pitches]);
?>