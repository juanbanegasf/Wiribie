<?php
// includes/functions.php - CORREGIDO
// NO hacer session_start aquí, se hace en cada página
require_once 'config/database.php';
// Incluir database solo cuando se necesite
function getDatabase() {
    static $database = null;
    if ($database === null) {
        require_once __DIR__ . '/../config/database.php';
        $database = new Database();
    }
    return $database;
}

// Verificar si el usuario está logueado
function isLoggedIn() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['user_id']);
}

// Redirigir si no está logueado
function requireLogin() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

// Obtener usuario actual
function getCurrentUser($conn) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isLoggedIn()) return null;
    
    $stmt = $conn->prepare("SELECT id, username, email, full_name, bio, profile_pic, followers_count, following_count FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

// Obtener usuario por ID
function getUserById($conn, $user_id) {
    $stmt = $conn->prepare("SELECT id, username, email, full_name, bio, profile_pic, created_at, followers_count, following_count FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetch();
}

// Obtener usuario por username
function getUserByUsername($conn, $username) {
    $stmt = $conn->prepare("SELECT id, username, email, full_name, bio, profile_pic, created_at, followers_count, following_count FROM users WHERE username = ?");
    $stmt->execute([$username]);
    return $stmt->fetch();
}

// Formatear tiempo relativo
function timeAgo($timestamp) {
    $time = strtotime($timestamp);
    $diff = time() - $time;
    
    if ($diff < 60) return "Ahora";
    if ($diff < 3600) return floor($diff / 60) . "m";
    if ($diff < 86400) return floor($diff / 3600) . "h";
    if ($diff < 604800) return floor($diff / 86400) . "d";
    if ($diff < 2592000) return floor($diff / 604800) . "sem";
    if ($diff < 31536000) return floor($diff / 2592000) . "mes";
    return floor($diff / 31536000) . "a";
}

// Sanitizar texto
function sanitize($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

// Formatear números
function formatNumber($num) {
    $num = intval($num);
    if ($num >= 1000000) {
        $formatted = $num / 1000000;
        return ($formatted == floor($formatted)) ? floor($formatted) . 'M' : number_format($formatted, 1) . 'M';
    }
    if ($num >= 1000) {
        $formatted = $num / 1000;
        return ($formatted == floor($formatted)) ? floor($formatted) . 'K' : number_format($formatted, 1) . 'K';
    }
    return number_format($num);
}

// Verificar si el usuario dio like
function hasLiked($conn, $post_id, $user_id) {
    $stmt = $conn->prepare("SELECT id FROM likes WHERE post_id = ? AND user_id = ?");
    $stmt->execute([$post_id, $user_id]);
    return $stmt->fetch() !== false;
}

// Verificar si el usuario hizo repost
function hasReposted($conn, $post_id, $user_id) {
    $stmt = $conn->prepare("SELECT id FROM reposts WHERE post_id = ? AND user_id = ?");
    $stmt->execute([$post_id, $user_id]);
    return $stmt->fetch() !== false;
}

// Verificar si sigue a un usuario
function isFollowing($conn, $follower_id, $following_id) {
    $stmt = $conn->prepare("SELECT id FROM follows WHERE follower_id = ? AND following_id = ?");
    $stmt->execute([$follower_id, $following_id]);
    return $stmt->fetch() !== false;
}

// Obtener comentarios de un post
function getComments($conn, $post_id) {
    $stmt = $conn->prepare("
        SELECT c.*, u.username, u.profile_pic, u.full_name 
        FROM comments c 
        JOIN users u ON c.user_id = u.id 
        WHERE c.post_id = ? AND c.parent_id IS NULL
        ORDER BY c.created_at ASC
    ");
    $stmt->execute([$post_id]);
    return $stmt->fetchAll();
}

// Obtener respuestas a un comentario
function getCommentReplies($conn, $comment_id) {
    $stmt = $conn->prepare("
        SELECT c.*, u.username, u.profile_pic, u.full_name 
        FROM comments c 
        JOIN users u ON c.user_id = u.id 
        WHERE c.parent_id = ?
        ORDER BY c.created_at ASC
    ");
    $stmt->execute([$comment_id]);
    return $stmt->fetchAll();
}

// Verificar si dio like a un comentario
function hasLikedComment($conn, $comment_id, $user_id) {
    $stmt = $conn->prepare("SELECT id FROM comment_likes WHERE comment_id = ? AND user_id = ?");
    $stmt->execute([$comment_id, $user_id]);
    return $stmt->fetch() !== false;
}

// Obtener posts del feed
function getFeedPosts($conn, $limit = 50, $offset = 0) {
    $seed = floor(time() / 300);
    
    $stmt = $conn->prepare("
        SELECT p.*, u.username, u.profile_pic, u.full_name,
               NULL as repost_user_id,
               NULL as repost_username,
               NULL as repost_time,
               NULL as repost_text
        FROM posts p
        JOIN users u ON p.user_id = u.id
        
        UNION ALL
        
        SELECT p.*, u.username, u.profile_pic, u.full_name,
               r.user_id as repost_user_id,
               ru.username as repost_username,
               r.created_at as repost_time,
               r.repost_text
        FROM reposts r
        JOIN posts p ON r.post_id = p.id
        JOIN users u ON p.user_id = u.id
        JOIN users ru ON r.user_id = ru.id
        
        ORDER BY RAND(?) DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->execute([$seed, $limit, $offset]);
    return $stmt->fetchAll();
}

// Obtener posts de un usuario específico
function getUserPosts($conn, $user_id) {
    $stmt = $conn->prepare("
        SELECT p.*, u.username, u.profile_pic, u.full_name,
               NULL as repost_user_id,
               NULL as repost_username,
               NULL as repost_time,
               NULL as repost_text
        FROM posts p
        JOIN users u ON p.user_id = u.id
        WHERE p.user_id = ?
        
        UNION ALL
        
        SELECT p.*, u.username, u.profile_pic, u.full_name,
               r.user_id as repost_user_id,
               ru.username as repost_username,
               r.created_at as repost_time,
               r.repost_text
        FROM reposts r
        JOIN posts p ON r.post_id = p.id
        JOIN users u ON p.user_id = u.id
        JOIN users ru ON r.user_id = ru.id
        WHERE r.user_id = ?
        
        ORDER BY COALESCE(repost_time, created_at) DESC
    ");
    $stmt->execute([$user_id, $user_id]);
    return $stmt->fetchAll();
}

// Contar posts de un usuario
function getUserPostsCount($conn, $user_id) {
    $stmt = $conn->prepare("
        SELECT COUNT(*) as count FROM (
            SELECT id FROM posts WHERE user_id = ?
            UNION ALL
            SELECT id FROM reposts WHERE user_id = ?
        ) as total
    ");
    $stmt->execute([$user_id, $user_id]);
    return $stmt->fetch()['count'];
}

// Obtener notificaciones no leídas
function getUnreadNotificationsCount($conn, $user_id) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = FALSE");
    $stmt->execute([$user_id]);
    return $stmt->fetch()['count'];
}

// Obtener notificaciones
function getNotifications($conn, $user_id, $limit = 20) {
    $stmt = $conn->prepare("
        SELECT n.*, u.username, u.profile_pic, u.full_name,
               p.content as post_content, p.media_url as post_media
        FROM notifications n
        JOIN users u ON n.from_user_id = u.id
        LEFT JOIN posts p ON n.post_id = p.id
        WHERE n.user_id = ?
        ORDER BY n.created_at DESC
        LIMIT ?
    ");
    $stmt->execute([$user_id, $limit]);
    return $stmt->fetchAll();
}

// Crear notificación
function createNotification($conn, $user_id, $from_user_id, $type, $post_id = null, $comment_id = null) {
    if ($user_id == $from_user_id) return;
    
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, from_user_id, type, post_id, comment_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $from_user_id, $type, $post_id, $comment_id]);
}

// Buscar en la plataforma
function search($conn, $query, $filter = 'all') {
    $results = [
        'users' => [],
        'posts' => [],
        'images' => [],
        'videos' => []
    ];
    
    $search_term = "%{$query}%";
    
    if ($filter == 'all' || $filter == 'users') {
        $stmt = $conn->prepare("
            SELECT id, username, full_name, profile_pic, followers_count 
            FROM users 
            WHERE username LIKE ? OR full_name LIKE ?
            LIMIT 20
        ");
        $stmt->execute([$search_term, $search_term]);
        $results['users'] = $stmt->fetchAll();
    }
    
    if ($filter == 'all' || $filter == 'posts') {
        $stmt = $conn->prepare("
            SELECT p.*, u.username, u.profile_pic, u.full_name
            FROM posts p
            JOIN users u ON p.user_id = u.id
            WHERE p.content LIKE ?
            ORDER BY p.created_at DESC
            LIMIT 20
        ");
        $stmt->execute([$search_term]);
        $results['posts'] = $stmt->fetchAll();
    }
    
    if ($filter == 'all' || $filter == 'images') {
        $stmt = $conn->prepare("
            SELECT p.*, u.username, u.profile_pic, u.full_name
            FROM posts p
            JOIN users u ON p.user_id = u.id
            WHERE p.media_type IN ('image', 'gif')
            ORDER BY p.created_at DESC
            LIMIT 20
        ");
        $stmt->execute();
        $results['images'] = $stmt->fetchAll();
    }
    
    if ($filter == 'all' || $filter == 'videos') {
        $stmt = $conn->prepare("
            SELECT p.*, u.username, u.profile_pic, u.full_name
            FROM posts p
            JOIN users u ON p.user_id = u.id
            WHERE p.media_type = 'video'
            ORDER BY p.created_at DESC
            LIMIT 20
        ");
        $stmt->execute();
        $results['videos'] = $stmt->fetchAll();
    }
    
    return $results;
}

// Obtener post por ID
function getPostById($conn, $post_id) {
    $stmt = $conn->prepare("
        SELECT p.*, u.username, u.profile_pic, u.full_name
        FROM posts p
        JOIN users u ON p.user_id = u.id
        WHERE p.id = ?
    ");
    $stmt->execute([$post_id]);
    return $stmt->fetch();
}

// Linkify - Convertir hashtags, menciones y URLs en enlaces
function linkify($text) {
    if (empty($text)) return '';
    
    $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    
    // URLs con protocolo
    $text = preg_replace_callback(
        '#(https?://[^\s<]+)#i',
        function($matches) {
            $url = $matches[1];
            return '<a href="' . $url . '" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:text-blue-700 underline">' . $url . '</a>';
        },
        $text
    );
    
    // URLs sin protocolo
    $text = preg_replace_callback(
        '#(?<!["\'>=/])\b([a-zA-Z0-9][-a-zA-Z0-9]*\.(?:com|net|org|edu|gov|mil|app|io|co|me|tv|dev|tech|online|site|blog|shop|store|info|biz))\b#i',
        function($matches) {
            $url = $matches[1];
            return '<a href="http://' . $url . '" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:text-blue-700 underline">' . $url . '</a>';
        },
        $text
    );
    
    // Hashtags
    $text = preg_replace_callback(
        '/#([a-zA-Z0-9_áéíóúñÁÉÍÓÚÑüÜ]+)/',
        function($matches) {
            $hashtag = $matches[1];
            return '<a href="search.php?q=' . urlencode('#' . $hashtag) . '" class="text-sky-500 hover:text-sky-600 font-semibold">#' . $hashtag . '</a>';
        },
        $text
    );
    
    // Menciones
    $text = preg_replace_callback(
        '/@([a-zA-Z0-9_]+)/',
        function($matches) {
            $username = $matches[1];
            return '<a href="profile.php?user=' . $username . '" class="text-blue-800 hover:text-blue-900 font-semibold">@' . $username . '</a>';
        },
        $text
    );
    
    $text = nl2br($text);
    
    return $text;
}

// Truncar texto con "ver más"
function truncateText($text, $limit = 280) {
    if (empty($text)) return '';
    
    $plainLength = strlen(strip_tags($text));
    
    if ($plainLength <= $limit) {
        return '<div class="post-text-full">' . linkify($text) . '</div>';
    }
    
    $shortText = mb_substr($text, 0, $limit);
    
    return '<div class="post-text-container">
        <div class="post-text-short">' . linkify($shortText) . '... 
        <button onclick="toggleText(this)" class="text-blue-500 hover:text-blue-600 font-semibold ml-1">ver más</button>
        </div>
        <div class="post-text-full hidden">' . linkify($text) . ' 
        <button onclick="toggleText(this)" class="text-blue-500 hover:text-blue-600 font-semibold ml-1">ver menos</button>
        </div>
    </div>';
}
?>