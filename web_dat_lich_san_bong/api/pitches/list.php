<?php
require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../src/Models/Pitch.php';

$pitchModel = new Pitch();
$pitches = $pitchModel->getAll();

echo json_encode(['success' => true, 'pitches' => $pitches]);
?>