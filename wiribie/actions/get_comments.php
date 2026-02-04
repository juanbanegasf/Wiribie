<?php
// actions/get_comments.php - NUEVO
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

$post_id = intval($_GET['post_id'] ?? 0);

if ($post_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Post inválido']);
    exit();
}

try {
    $database = new Database();
    $conn = $database->getConnection();
    $current_user = getCurrentUser($conn);
    
    $comments = getComments($conn, $post_id);
    
    ob_start();
    
    foreach ($comments as $comment): 
        $comment_liked = hasLikedComment($conn, $comment['id'], $current_user['id']);
        $replies = getCommentReplies($conn, $comment['id']);
    ?>
        <div class="flex items-start space-x-2">
            <a href="profile.php?user=<?php echo sanitize($comment['username']); ?>">
                <img src="uploads/profiles/<?php echo sanitize($comment['profile_pic']); ?>" 
                     alt="<?php echo sanitize($comment['username']); ?>"
                     class="w-9 h-9 rounded-full object-cover">
            </a>
            <div class="flex-1">
                <div class="bg-gray-100 rounded-2xl px-4 py-2">
                    <a href="profile.php?user=<?php echo sanitize($comment['username']); ?>" 
                       class="font-semibold text-sm hover:underline">
                        <?php echo sanitize($comment['full_name'] ?? $comment['username']); ?>
                    </a>
                    <p class="text-sm text-gray-800 post-content"><?php echo linkify($comment['content']); ?></p>
                </div>
                <div class="flex items-center space-x-4 mt-1 px-4 text-xs text-gray-500">
                    <span><?php echo timeAgo($comment['created_at']); ?></span>
                    <button onclick="toggleCommentLike(<?php echo $comment['id']; ?>)"
                            id="comment-like-<?php echo $comment['id']; ?>"
                            class="font-semibold hover:underline <?php echo $comment_liked ? 'text-red-500' : ''; ?>">
                        Me gusta (<?php echo formatNumber($comment['likes_count']); ?>)
                    </button>
                    <button onclick="replyToComment(<?php echo $post_id; ?>, <?php echo $comment['id']; ?>, '<?php echo sanitize($comment['username']); ?>')"
                            class="font-semibold hover:underline">
                        Responder
                    </button>
                </div>
                
                <!-- Respuestas -->
                <?php if (!empty($replies)): ?>
                    <div class="mt-3 space-y-3 ml-4 border-l-2 border-gray-200 pl-3">
                        <?php foreach ($replies as $reply): ?>
                            <div class="flex items-start space-x-2">
                                <img src="uploads/profiles/<?php echo sanitize($reply['profile_pic']); ?>" 
                                     class="w-7 h-7 rounded-full object-cover">
                                <div class="flex-1">
                                    <div class="bg-gray-100 rounded-2xl px-3 py-2">
                                        <a href="profile.php?user=<?php echo sanitize($reply['username']); ?>" 
                                           class="font-semibold text-xs hover:underline">
                                            <?php echo sanitize($reply['full_name'] ?? $reply['username']); ?>
                                        </a>
                                        <p class="text-xs text-gray-800 post-content"><?php echo linkify($reply['content']); ?></p>
                                    </div>
                                    <span class="text-xs text-gray-500 ml-3"><?php echo timeAgo($reply['created_at']); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach;
    
    $html = ob_get_clean();
    
    echo json_encode([
        'success' => true,
        'html' => $html
    ]);
    
} catch (Exception $e) {
    error_log("Get comments error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error del servidor']);
}
?>