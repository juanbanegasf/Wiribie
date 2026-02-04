<?php
// actions/update_profile.php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$full_name = trim($_POST['full_name'] ?? '');
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$bio = trim($_POST['bio'] ?? '');

// Validaciones
if (empty($username) || empty($email)) {
    header("Location: ../edit-profile.php?error=required");
    exit();
}

if (!preg_match('/^[a-zA-Z0-9_]{3,}$/', $username)) {
    header("Location: ../edit-profile.php?error=username");
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../edit-profile.php?error=email");
    exit();
}

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Verificar si el username o email ya están en uso por otro usuario
    $stmt = $conn->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
    $stmt->execute([$username, $email, $user_id]);
    
    if ($stmt->fetch()) {
        header("Location: ../edit-profile.php?error=exists");
        exit();
    }
    
    // Actualizar perfil
    $stmt = $conn->prepare("UPDATE users SET full_name = ?, username = ?, email = ?, bio = ? WHERE id = ?");
    $stmt->execute([$full_name, $username, $email, $bio, $user_id]);
    
    // Actualizar sesión
    $_SESSION['username'] = $username;
    
    header("Location: ../edit-profile.php?success=profile");
    exit();
    
} catch (Exception $e) {
    header("Location: ../edit-profile.php?error=update");
    exit();
}
?>