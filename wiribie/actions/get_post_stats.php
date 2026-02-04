<?php
// actions/get_post_stats.php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false]);
    exit();
}

$post_id = intval($_GET['id'] ?? 0);

if ($post_id <= 0) {
    echo json_encode(['success' => false]);
    exit();
}

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $stmt = $conn->prepare("SELECT likes_count, comments_count, reposts_count FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);
    $stats = $stmt->fetch();
    
    if (!$stats) {
        echo json_encode(['success' => false]);
        exit();
    }
    
    echo json_encode([
        'success' => true,
        'stats' => $stats
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false]);
}
?><?php
// actions/get_post_stats.php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false]);
    exit();
}

$post_id = intval($_GET['id'] ?? 0);

if ($post_id <= 0) {
    echo json_encode(['success' => false]);
    exit();
}

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $stmt = $conn->prepare("SELECT likes_count, comments_count, reposts_count FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);
    $stats = $stmt->fetch();
    
    if (!$stats) {
        echo json_encode(['success' => false]);
        exit();
    }
    
    echo json_encode([
        'success' => true,
        'stats' => $stats
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false]);
}
?>