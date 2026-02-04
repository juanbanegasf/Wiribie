<?php
// actions/comment_like_action.php - MEJORADO
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

$user_id = $_SESSION['user_id'];
$comment_id = intval($_POST['comment_id'] ?? 0);

if ($comment_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Comentario inválido']);
    exit();
}

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Verificar si ya dio like
    $stmt = $conn->prepare("SELECT id FROM comment_likes WHERE user_id = ? AND comment_id = ?");
    $stmt->execute([$user_id, $comment_id]);
    $existing_like = $stmt->fetch();
    
    if ($existing_like) {
        // Quitar like
        $stmt = $conn->prepare("DELETE FROM comment_likes WHERE user_id = ? AND comment_id = ?");
        $stmt->execute([$user_id, $comment_id]);
        
        // Actualizar contador
        $stmt = $conn->prepare("UPDATE comments SET likes_count = GREATEST(0, likes_count - 1) WHERE id = ?");
        $stmt->execute([$comment_id]);
        
        $liked = false;
    } else {
        // Dar like
        $stmt = $conn->prepare("INSERT INTO comment_likes (user_id, comment_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $comment_id]);
        
        // Actualizar contador
        $stmt = $conn->prepare("UPDATE comments SET likes_count = likes_count + 1 WHERE id = ?");
        $stmt->execute([$comment_id]);
        
        $liked = true;
        
        // Notificar al autor del comentario
        $stmt = $conn->prepare("SELECT user_id, post_id FROM comments WHERE id = ?");
        $stmt->execute([$comment_id]);
        $comment = $stmt->fetch();
        
        if ($comment && $comment['user_id'] != $user_id) {
            require_once '../includes/functions.php';
            createNotification($conn, $comment['user_id'], $user_id, 'like', $comment['post_id'], $comment_id);
        }
    }
    
    // Obtener nuevo conteo
    $stmt = $conn->prepare("SELECT likes_count FROM comments WHERE id = ?");
    $stmt->execute([$comment_id]);
    $count = $stmt->fetch()['likes_count'];
    
    echo json_encode([
        'success' => true,
        'liked' => $liked,
        'count' => intval($count)
    ]);
    
} catch (Exception $e) {
    error_log("Comment like error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error del servidor']);
}
?>