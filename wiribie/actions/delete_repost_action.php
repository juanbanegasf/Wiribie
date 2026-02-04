<?php
// actions/delete_repost_action.php - NUEVO
session_start();
require_once '../config/database.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');

if (ob_get_level()) ob_end_clean();
ob_start();

ini_set('display_errors', 0);
error_reporting(0);

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
    ob_clean();
    http_response_code(401);
    die(json_encode(['success' => false, 'message' => 'No autorizado']));
}

$user_id = intval($_SESSION['user_id']);
$post_id = intval($_POST['post_id'] ?? 0);

if ($post_id <= 0) {
    ob_clean();
    http_response_code(400);
    die(json_encode(['success' => false, 'message' => 'Post inválido']));
}

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    if (!$conn) {
        throw new Exception('Error de conexión');
    }
    
    $conn->beginTransaction();
    
    // Verificar que el repost pertenece al usuario
    $stmt = $conn->prepare("SELECT id FROM reposts WHERE post_id = ? AND user_id = ?");
    $stmt->execute([$post_id, $user_id]);
    $repost = $stmt->fetch();
    
    if (!$repost) {
        $conn->rollBack();
        ob_clean();
        http_response_code(404);
        die(json_encode(['success' => false, 'message' => 'Repost no encontrado o no autorizado']));
    }
    
    // Eliminar repost
    $stmt = $conn->prepare("DELETE FROM reposts WHERE post_id = ? AND user_id = ?");
    $stmt->execute([$post_id, $user_id]);
    
    // Actualizar contador del post original
    $stmt = $conn->prepare("UPDATE posts SET reposts_count = GREATEST(0, reposts_count - 1) WHERE id = ?");
    $stmt->execute([$post_id]);
    
    $conn->commit();
    
    ob_clean();
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Repost eliminado'
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }
    
    error_log("Delete repost error: " . $e->getMessage());
    
    ob_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error del servidor'
    ], JSON_UNESCAPED_UNICODE);
}

ob_end_flush();
exit();
?>