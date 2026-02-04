<?php
// actions/comment_action.php - CORREGIDO
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

error_reporting(0);
ini_set('display_errors', 0);

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

$user_id = intval($_SESSION['user_id']);
$post_id = intval($_POST['post_id'] ?? 0);
$content = trim($_POST['content'] ?? '');
$parent_id = isset($_POST['parent_id']) && $_POST['parent_id'] !== '' ? intval($_POST['parent_id']) : null;

if ($post_id <= 0 || empty($content)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
    exit();
}

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    if (!$conn) {
        throw new Exception('No se pudo conectar a la base de datos');
    }
    
    $conn->beginTransaction();
    
    // Verificar que el post existe
    $stmt = $conn->prepare("SELECT user_id FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch();
    
    if (!$post) {
        $conn->rollBack();
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Post no encontrado']);
        exit();
    }
    
    // Obtener datos del usuario actual
    $current_user = getCurrentUser($conn);
    
    // Insertar comentario
    if ($parent_id) {
        $stmt = $conn->prepare("INSERT INTO comments (user_id, post_id, content, parent_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $post_id, $content, $parent_id]);
        
        // Notificar al autor del comentario padre
        $stmt = $conn->prepare("SELECT user_id FROM comments WHERE id = ?");
        $stmt->execute([$parent_id]);
        $parent_comment = $stmt->fetch();
        
        if ($parent_comment && $parent_comment['user_id'] != $user_id) {
            $stmt = $conn->prepare("INSERT INTO notifications (user_id, from_user_id, type, post_id, comment_id) VALUES (?, ?, 'comment_reply', ?, ?)");
            $stmt->execute([$parent_comment['user_id'], $user_id, $post_id, $parent_id]);
        }
    } else {
        $stmt = $conn->prepare("INSERT INTO comments (user_id, post_id, content) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $post_id, $content]);
        
        // Notificar al autor del post
        if ($post['user_id'] != $user_id) {
            $stmt = $conn->prepare("INSERT INTO notifications (user_id, from_user_id, type, post_id) VALUES (?, ?, 'comment', ?)");
            $stmt->execute([$post['user_id'], $user_id, $post_id]);
        }
    }
    
    $comment_id = $conn->lastInsertId();
    
    // Actualizar contador de comentarios
    $stmt = $conn->prepare("UPDATE posts SET comments_count = comments_count + 1 WHERE id = ?");
    $stmt->execute([$post_id]);
    
    $conn->commit();
    
    // Obtener nuevo conteo
    $stmt = $conn->prepare("SELECT comments_count FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);
    $new_count = intval($stmt->fetch()['comments_count']);
    
    // Generar HTML del comentario
    $comment_html = '
    <div class="flex items-start space-x-2 animate-fade-in">
        <a href="profile.php?user=' . sanitize($current_user['username']) . '">
            <img src="uploads/profiles/' . sanitize($current_user['profile_pic']) . '" 
                 alt="' . sanitize($current_user['username']) . '"
                 class="w-9 h-9 rounded-full object-cover">
        </a>
        <div class="flex-1">
            <div class="bg-gray-100 rounded-2xl px-4 py-2">
                <a href="profile.php?user=' . sanitize($current_user['username']) . '" 
                   class="font-semibold text-sm hover:underline">
                    ' . sanitize($current_user['full_name'] ?? $current_user['username']) . '
                </a>
                <p class="text-sm text-gray-800 post-content">' . linkify($content) . '</p>
            </div>
            <div class="flex items-center space-x-4 mt-1 px-4 text-xs text-gray-500">
                <span>Ahora</span>
                <button onclick="toggleCommentLike(' . $comment_id . ')"
                        id="comment-like-' . $comment_id . '"
                        class="font-semibold hover:underline">
                    Me gusta (0)
                </button>
                <button onclick="replyToComment(' . $post_id . ', ' . $comment_id . ', \'' . sanitize($current_user['username']) . '\')"
                        class="font-semibold hover:underline">
                    Responder
                </button>
            </div>
        </div>
    </div>';
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'comment_html' => $comment_html,
        'new_count' => $new_count
    ]);
    
} catch (Exception $e) {
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }
    
    error_log("Comment error: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Error del servidor',
        'debug' => $e->getMessage()
    ]);
}
?>