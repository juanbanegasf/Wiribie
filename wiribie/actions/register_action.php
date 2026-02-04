<?php
// actions/register_action.php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../register.php");
    exit();
}

$full_name = trim($_POST['full_name'] ?? '');
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validaciones
if (empty($full_name) || empty($username) || empty($email) || empty($password)) {
    $_SESSION['register_error'] = "Todos los campos son requeridos";
    header("Location: ../register.php");
    exit();
}

if (strlen($password) < 6) {
    $_SESSION['register_error'] = "La contraseña debe tener al menos 6 caracteres";
    header("Location: ../register.php");
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['register_error'] = "Email inválido";
    header("Location: ../register.php");
    exit();
}

if (!preg_match('/^[a-zA-Z0-9_]{3,}$/', $username)) {
    $_SESSION['register_error'] = "Usuario inválido. Mínimo 3 caracteres, solo letras, números y guión bajo";
    header("Location: ../register.php");
    exit();
}

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Verificar si el usuario ya existe
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    
    if ($stmt->fetch()) {
        $_SESSION['register_error'] = "El usuario o email ya están en uso";
        header("Location: ../register.php");
        exit();
    }
    
    // Crear usuario
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (full_name, username, email, password) VALUES (?, ?, ?, ?)");
    $stmt->execute([$full_name, $username, $email, $hashed_password]);
    
    // Login automático
    $_SESSION['user_id'] = $conn->lastInsertId();
    $_SESSION['username'] = $username;
    
    header("Location: ../index.php");
    exit();
    
} catch (Exception $e) {
    $_SESSION['register_error'] = "Error al crear la cuenta: " . $e->getMessage();
    header("Location: ../register.php");
    exit();
}
?>