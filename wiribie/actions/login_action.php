<?php
// actions/login_action.php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../login.php");
    exit();
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    $_SESSION['login_error'] = "Todos los campos son requeridos";
    header("Location: ../login.php");
    exit();
}

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: ../index.php");
        exit();
    } else {
        $_SESSION['login_error'] = "Usuario o contraseña incorrectos";
        header("Location: ../login.php");
        exit();
    }
    
} catch (Exception $e) {
    $_SESSION['login_error'] = "Error en el servidor";
    header("Location: ../login.php");
    exit();
}
?>