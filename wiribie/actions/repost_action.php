<?php
// actions/repost_action.php - VERSION FINAL LIMPIA
session_start();
require_once '../config/database.php';

// Headers antes de cualquier output
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');

// Limpiar cualquier output buffer
if (ob_get_level()) ob_end_clean();
ob_start();

// Sin mostrar errores en JSON
ini_set('display_errors', 0);
error_reporting(0);

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
    ob_clean();
    http_response_code(401);
    die(json_encode(['success' => false, 'message' => 'No autorizado']));
}

$user_id = intval($_SESSION['user_id']);
$post_id = intval($_POST['post_id'] ?? 0);
$repost_text = trim($_POST['repost_text'] ?? '');

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
    
    // Obtener post original
    $stmt = $conn->prepare("SELECT user_id FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch();
    
    if (!$post) {
        $conn->rollBack();
        ob_clean();
        http_response_code(404);
        die(json_encode(['success' => false, 'message' => 'Post no encontrado']));
    }
    
    $original_user_id = intval($post['user_id']);
    
    // Verificar que no sea su propio post
    if ($original_user_id === $user_id) {
        $conn->rollBack();
        ob_clean();
        http_response_code(403);
        die(json_encode(['success' => false, 'message' => 'No puedes repostear tu propia publicación']));
    }
    
    // Verificar si ya hizo repost
    $stmt = $conn->prepare("SELECT id FROM reposts WHERE user_id = ? AND post_id = ?");
    $stmt->execute([$user_id, $post_id]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        // QUITAR REPOST
        $stmt = $conn->prepare("DELETE FROM reposts WHERE user_id = ? AND post_id = ?");
        $stmt->execute([$user_id, $post_id]);
        
        $stmt = $conn->prepare("UPDATE posts SET reposts_count = GREATEST(0, reposts_count - 1) WHERE id = ?");
        $stmt->execute([$post_id]);
        
        $reposted = false;
    } else {
        // HACER REPOST
        $stmt = $conn->prepare("INSERT INTO reposts (user_id, post_id, original_user_id, repost_text) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $post_id, $original_user_id, $repost_text]);
        
        $stmt = $conn->prepare("UPDATE posts SET reposts_count = reposts_count + 1 WHERE id = ?");
        $stmt->execute([$post_id]);
        
        $reposted = true;
        
        // Notificación
        $stmt = $conn->prepare("INSERT INTO notifications (user_id, from_user_id, type, post_id) VALUES (?, ?, 'repost', ?)");
        $stmt->execute([$original_user_id, $user_id, $post_id]);
    }
    
    $conn->commit();
    
    // Obtener conteo actualizado
    $stmt = $conn->prepare("SELECT reposts_count FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);
    $result = $stmt->fetch();
    $count = intval($result['reposts_count']);
    
    // Limpiar buffer y enviar respuesta
    ob_clean();
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'reposted' => $reposted,
        'count' => $count
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }
    
    error_log("Repost error: " . $e->getMessage());
    
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