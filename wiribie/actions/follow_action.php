<?php
// actions/follow_action.php - CORREGIDO SIN DUPLICACIÓN
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

$follower_id = intval($_SESSION['user_id']);
$following_id = intval($_POST['user_id'] ?? 0);

if ($following_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Usuario inválido']);
    exit();
}

if ($follower_id == $following_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'No puedes seguirte a ti mismo']);
    exit();
}

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    if (!$conn) {
        throw new Exception('No se pudo conectar a la base de datos');
    }
    
    // Iniciar transacción
    $conn->beginTransaction();
    
    // Verificar que el usuario a seguir existe
    $stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->execute([$following_id]);
    if (!$stmt->fetch()) {
        $conn->rollBack();
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
        exit();
    }
    
    // Verificar si ya sigue
    $stmt = $conn->prepare("SELECT id FROM follows WHERE follower_id = ? AND following_id = ?");
    $stmt->execute([$follower_id, $following_id]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        // DEJAR DE SEGUIR
        $stmt = $conn->prepare("DELETE FROM follows WHERE follower_id = ? AND following_id = ?");
        $stmt->execute([$follower_id, $following_id]);
        
        // Actualizar contador de seguidores del usuario seguido (-1)
        $stmt = $conn->prepare("UPDATE users SET followers_count = GREATEST(0, followers_count - 1) WHERE id = ?");
        $stmt->execute([$following_id]);
        
        // Actualizar contador de seguidos del usuario actual (-1)
        $stmt = $conn->prepare("UPDATE users SET following_count = GREATEST(0, following_count - 1) WHERE id = ?");
        $stmt->execute([$follower_id]);
        
        $following = false;
    } else {
        // SEGUIR
        $stmt = $conn->prepare("INSERT INTO follows (follower_id, following_id) VALUES (?, ?)");
        $stmt->execute([$follower_id, $following_id]);
        
        // Actualizar contador de seguidores del usuario seguido (+1)
        $stmt = $conn->prepare("UPDATE users SET followers_count = followers_count + 1 WHERE id = ?");
        $stmt->execute([$following_id]);
        
        // Actualizar contador de seguidos del usuario actual (+1)
        $stmt = $conn->prepare("UPDATE users SET following_count = following_count + 1 WHERE id = ?");
        $stmt->execute([$follower_id]);
        
        $following = true;
        
        // Crear notificación
        $stmt = $conn->prepare("INSERT INTO notifications (user_id, from_user_id, type) VALUES (?, ?, 'follow')");
        $stmt->execute([$following_id, $follower_id]);
    }
    
    // Commit de la transacción
    $conn->commit();
    
    // Obtener contadores actualizados
    $stmt = $conn->prepare("SELECT followers_count, following_count FROM users WHERE id = ?");
    $stmt->execute([$following_id]);
    $counts = $stmt->fetch();
    
    // Respuesta exitosa
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'following' => $following,
        'followers_count' => intval($counts['followers_count']),
        'following_count' => intval($counts['following_count'])
    ]);
    
} catch (Exception $e) {
    // Rollback en caso de error
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }
    
    // Log del error
    error_log("Follow error: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Error del servidor',
        'debug' => $e->getMessage() // Quitar en producción
    ]);
}
?>