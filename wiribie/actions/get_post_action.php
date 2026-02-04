<?php
// actions/get_post_action.php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

$post_id = intval($_GET['id'] ?? 0);

if ($post_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Post inválido']);
    exit();
}

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $post = getPostById($conn, $post_id);
    
    if (!$post) {
        echo json_encode(['success' => false, 'message' => 'Post no encontrado']);
        exit();
    }
    
    echo json_encode([
        'success' => true,
        'post' => $post
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error del servidor']);
}
?>