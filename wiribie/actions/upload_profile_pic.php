<?php
// actions/upload_profile_pic.php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (empty($_FILES['profile_pic']['name'])) {
    header("Location: ../edit-profile.php?error=upload");
    exit();
}

$file = $_FILES['profile_pic'];
$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];

// Validar tipo de archivo
if (!in_array($file['type'], $allowed_types)) {
    header("Location: ../edit-profile.php?error=type");
    exit();
}

// Validar tamaño (5MB máximo)
if ($file['size'] > 5 * 1024 * 1024) {
    header("Location: ../edit-profile.php?error=size");
    exit();
}

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Obtener imagen anterior para eliminarla
    $stmt = $conn->prepare("SELECT profile_pic FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $old_pic = $stmt->fetch()['profile_pic'];
    
    // Generar nombre único
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_filename = 'profile_' . $user_id . '_' . time() . '.' . $extension;
    $upload_path = '../uploads/profiles/' . $new_filename;
    
    // Mover archivo
    if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
        header("Location: ../edit-profile.php?error=upload");
        exit();
    }
    
    // Actualizar base de datos
    $stmt = $conn->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
    $stmt->execute([$new_filename, $user_id]);
    
    // Eliminar imagen anterior (si no es la default)
    if ($old_pic !== 'default-avatar.png' && file_exists('../uploads/profiles/' . $old_pic)) {
        unlink('../uploads/profiles/' . $old_pic);
    }
    
    header("Location: ../edit-profile.php?success=photo");
    exit();
    
} catch (Exception $e) {
    header("Location: ../edit-profile.php?error=upload");
    exit();
}
?>