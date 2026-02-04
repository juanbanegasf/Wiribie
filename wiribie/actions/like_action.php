<?php
// actions/like_action.php - CORREGIDO SIN DUPLICACIÓN
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Prevenir salida de errores que rompan el JSON
error_reporting(0);
ini_set('display_errors', 0);

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

$user_id = intval($_SESSION['user_id']);
$post_id = intval($_POST['post_id'] ?? 0);

if ($post_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Post inválido']);
    exit();
}

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    if (!$conn) {
        throw new Exception('No se pudo conectar a la base de datos');
    }
    
    // Iniciar transacción para atomicidad
    $conn->beginTransaction();
    
    // Verificar si el post existe
    $stmt = $conn->prepare("SELECT user_id FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch();
    
    if (!$post) {
        $conn->rollBack();
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Post no encontrado']);
        exit();
    }
    
    $original_user_id = $post['user_id'];
    
    // Verificar si ya dio like
    $stmt = $conn->prepare("SELECT id FROM likes WHERE user_id = ? AND post_id = ?");
    $stmt->execute([$user_id, $post_id]);
    $existing_like = $stmt->fetch();
    
    if ($existing_like) {
        // QUITAR LIKE
        $stmt = $conn->prepare("DELETE FROM likes WHERE user_id = ? AND post_id = ?");
        $stmt->execute([$user_id, $post_id]);
        
        // Actualizar contador (-1)
        $stmt = $conn->prepare("UPDATE posts SET likes_count = GREATEST(0, likes_count - 1) WHERE id = ?");
        $stmt->execute([$post_id]);
        
        $liked = false;
    } else {
        // DAR LIKE
        $stmt = $conn->prepare("INSERT INTO likes (user_id, post_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $post_id]);
        
        // Actualizar contador (+1)
        $stmt = $conn->prepare("UPDATE posts SET likes_count = likes_count + 1 WHERE id = ?");
        $stmt->execute([$post_id]);
        
        $liked = true;
        
        // Crear notificación (solo si no es su propio post)
        if ($original_user_id != $user_id) {
            $stmt = $conn->prepare("INSERT INTO notifications (user_id, from_user_id, type, post_id) VALUES (?, ?, 'like', ?)");
            $stmt->execute([$original_user_id, $user_id, $post_id]);
        }
    }
    
    // Commit de la transacción
    $conn->commit();
    
    // Obtener conteo actualizado
    $stmt = $conn->prepare("SELECT likes_count FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);
    $result = $stmt->fetch();
    $count = intval($result['likes_count']);
    
    // Respuesta exitosa
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'liked' => $liked,
        'count' => $count
    ]);
    
} catch (Exception $e) {
    // Rollback en caso de error
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }
    
    // Log del error (opcional)
    error_log("Like error: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Error del servidor',
        'debug' => $e->getMessage() // Quitar en producción
    ]);
}
?>