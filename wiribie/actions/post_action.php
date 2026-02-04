<?php
// actions/post_action.php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$content = trim($_POST['content'] ?? '');
$media_type = 'none';
$media_url = null;

// Validar que haya contenido o media
if (empty($content) && empty($_FILES['media']['name'])) {
    header("Location: ../index.php");
    exit();
}

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Procesar archivo multimedia
    if (!empty($_FILES['media']['name'])) {
        $file = $_FILES['media'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'video/quicktime'];
        
        if (!in_array($file['type'], $allowed_types)) {
            header("Location: ../index.php?error=tipo_archivo");
            exit();
        }
        
        // Límite de tamaño: 50MB
        if ($file['size'] > 50 * 1024 * 1024) {
            header("Location: ../index.php?error=tamano_archivo");
            exit();
        }
        
        // Determinar tipo de media
        if (strpos($file['type'], 'video') !== false) {
            $media_type = 'video';
        } elseif (strpos($file['type'], 'gif') !== false) {
            $media_type = 'gif';
        } else {
            $media_type = 'image';
        }
        
        // Generar nombre único
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $media_url = uniqid() . '_' . time() . '.' . $extension;
        $upload_path = '../uploads/posts/' . $media_url;
        
        if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
            header("Location: ../index.php?error=upload");
            exit();
        }
    }
    
    // Insertar post
    $stmt = $conn->prepare("INSERT INTO posts (user_id, content, media_type, media_url) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $content, $media_type, $media_url]);
    
    header("Location: ../index.php");
    exit();
    
} catch (Exception $e) {
    header("Location: ../index.php?error=server");
    exit();
}
?>