<?php
// actions/report_action.php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

$user_id = $_SESSION['user_id'];
$post_id = intval($_POST['post_id'] ?? 0);
$reason = trim($_POST['reason'] ?? '');

if ($post_id <= 0 || empty($reason)) {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
    exit();
}

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Verificar si ya reportó este post
    $stmt = $conn->prepare("SELECT id FROM reports WHERE post_id = ? AND user_id = ?");
    $stmt->execute([$post_id, $user_id]);
    
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Ya has reportado esta publicación']);
        exit();
    }
    
    // Crear reporte
    $stmt = $conn->prepare("INSERT INTO reports (post_id, user_id, reason) VALUES (?, ?, ?)");
    $stmt->execute([$post_id, $user_id, $reason]);
    
    echo json_encode(['success' => true]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error del servidor']);
}
?>