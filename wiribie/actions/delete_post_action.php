<?php
// actions/delete_post_action.php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

$user_id = $_SESSION['user_id'];
$post_id = intval($_POST['post_id'] ?? 0);

if ($post_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Post inválido']);
    exit();
}

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Verificar que el post pertenece al usuario
    $stmt = $conn->prepare("SELECT media_url FROM posts WHERE id = ? AND user_id = ?");
    $stmt->execute([$post_id, $user_id]);
    $post = $stmt->fetch();
    
    if (!$post) {
        echo json_encode(['success' => false, 'message' => 'Post no encontrado o no autorizado']);
        exit();
    }
    
    // Eliminar archivo de media si existe
    if ($post['media_url']) {
        $file_path = '../uploads/posts/' . $post['media_url'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
    
    // Eliminar post (cascada eliminará likes, comments, reposts)
    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
    $stmt->execute([$post_id, $user_id]);
    
    echo json_encode(['success' => true]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error del servidor']);
}
?>