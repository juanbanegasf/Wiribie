<?php
// verificar_instalacion.php - EJECUTAR PARA VERIFICAR
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Verificación de Instalación - Wiribie v2.1</h1>";
echo "<style>body{font-family:sans-serif;padding:20px;} .ok{color:green;} .error{color:red;} .warning{color:orange;}</style>";

$checks = [];

// 1. Verificar PHP
$checks[] = [
    'test' => 'Versión de PHP',
    'status' => version_compare(PHP_VERSION, '7.4.0', '>='),
    'message' => 'PHP ' . PHP_VERSION
];

// 2. Verificar extensiones
$checks[] = [
    'test' => 'PDO MySQL',
    'status' => extension_loaded('pdo_mysql'),
    'message' => extension_loaded('pdo_mysql') ? 'Instalado' : 'No instalado'
];

$checks[] = [
    'test' => 'GD (imágenes)',
    'status' => extension_loaded('gd'),
    'message' => extension_loaded('gd') ? 'Instalado' : 'No instalado'
];

// 3. Verificar archivos críticos
$critical_files = [
    'config/database.php',
    'includes/functions.php',
    'includes/header.php',
    'includes/navbar.php',
    'includes/post_component.php',
    'actions/like_action.php',
    'actions/comment_action.php',
    'actions/repost_action.php',
    'actions/follow_action.php',
    'assets/js/main.js',
    'assets/js/video-autoplay.js'
];

foreach ($critical_files as $file) {
    $checks[] = [
        'test' => "Archivo: $file",
        'status' => file_exists($file),
        'message' => file_exists($file) ? 'Existe' : 'No encontrado'
    ];
}

// 4. Verificar carpetas
$folders = [
    'uploads/profiles' => true,
    'uploads/posts' => true
];

foreach ($folders as $folder => $should_be_writable) {
    $exists = is_dir($folder);
    $writable = $exists && is_writable($folder);
    
    $checks[] = [
        'test' => "Carpeta: $folder",
        'status' => $exists && (!$should_be_writable || $writable),
        'message' => !$exists ? 'No existe' : ($writable ? 'Existe y escribible' : 'Existe pero no escribible')
    ];
}

// 5. Verificar avatar default
$checks[] = [
    'test' => 'Avatar default',
    'status' => file_exists('uploads/profiles/default-avatar.png'),
    'message' => file_exists('uploads/profiles/default-avatar.png') ? 'Existe' : 'No encontrado'
];

// 6. Verificar base de datos
try {
    require_once 'config/database.php';
    $db = new Database();
    $conn = $db->getConnection();
    
    $checks[] = [
        'test' => 'Conexión a BD',
        'status' => $conn !== null,
        'message' => $conn !== null ? 'Conectado' : 'Error de conexión'
    ];
    
    if ($conn) {
        // Verificar tablas
        $required_tables = ['users', 'posts', 'likes', 'comments', 'reposts', 'follows', 'notifications', 'reports', 'comment_likes'];
        
        foreach ($required_tables as $table) {
            $stmt = $conn->query("SHOW TABLES LIKE '$table'");
            $exists = $stmt->rowCount() > 0;
            
            $checks[] = [
                'test' => "Tabla: $table",
                'status' => $exists,
                'message' => $exists ? 'Existe' : 'No encontrada'
            ];
        }
        
        // Verificar columnas nuevas
        $column_checks = [
            ['posts', 'likes_count'],
            ['posts', 'comments_count'],
            ['posts', 'reposts_count'],
            ['users', 'followers_count'],
            ['users', 'following_count'],
            ['comments', 'likes_count'],
            ['comments', 'parent_id'],
            ['reposts', 'repost_text']
        ];
        
        foreach ($column_checks as list($table, $column)) {
            $stmt = $conn->query("SHOW COLUMNS FROM $table LIKE '$column'");
            $exists = $stmt->rowCount() > 0;
            
            $checks[] = [
                'test' => "Columna: $table.$column",
                'status' => $exists,
                'message' => $exists ? 'Existe' : 'No encontrada'
            ];
        }
    }
    
} catch (Exception $e) {
    $checks[] = [
        'test' => 'Error de BD',
        'status' => false,
        'message' => $e->getMessage()
    ];
}

// Mostrar resultados
echo "<table border='1' cellpadding='10' style='border-collapse:collapse;width:100%;'>";
echo "<tr style='background:#f0f0f0;'><th>Verificación</th><th>Estado</th><th>Detalles</th></tr>";

$total = count($checks);
$passed = 0;

foreach ($checks as $check) {
    $class = $check['status'] ? 'ok' : 'error';
    $icon = $check['status'] ? '✅' : '❌';
    if ($check['status']) $passed++;
    
    echo "<tr>";
    echo "<td>{$check['test']}</td>";
    echo "<td class='$class' style='text-align:center;font-weight:bold;'>$icon</td>";
    echo "<td>{$check['message']}</td>";
    echo "</tr>";
}

echo "</table>";

// Resumen
$percentage = round(($passed / $total) * 100);
$color = $percentage >= 90 ? 'green' : ($percentage >= 70 ? 'orange' : 'red');

echo "<h2 style='color:$color;'>Resultado: $passed/$total verificaciones pasadas ($percentage%)</h2>";

if ($percentage >= 90) {
    echo "<p style='color:green;font-size:18px;'><strong>✅ ¡Instalación correcta! Wiribie está listo para usarse.</strong></p>";
    echo "<p><a href='index.php' style='background:#3b82f6;color:white;padding:10px 20px;text-decoration:none;border-radius:8px;'>Ir a Wiribie →</a></p>";
} elseif ($percentage >= 70) {
    echo "<p style='color:orange;font-size:18px;'><strong>⚠️ Instalación parcial. Revisa los errores marcados.</strong></p>";
} else {
    echo "<p style='color:red;font-size:18px;'><strong>❌ Instalación incompleta. Faltan componentes críticos.</strong></p>";
}

echo "<hr>";
echo "<h3>Acciones Recomendadas:</h3>";
echo "<ul>";

if (!file_exists('uploads/profiles/default-avatar.png')) {
    echo "<li>Descargar avatar default desde: <a href='https://ui-avatars.com/api/?name=User&size=400&background=3B82F6&color=fff&rounded=true' target='_blank'>aquí</a></li>";
}

if (!is_writable('uploads/profiles') || !is_writable('uploads/posts')) {
    echo "<li>Ejecutar: <code>chmod -R 777 uploads/</code></li>";
}

echo "<li>Ejecutar <code>database_update_v2.sql</code> en phpMyAdmin si faltan tablas o columnas</li>";
echo "<li>Limpiar caché del navegador (Ctrl + Shift + Delete)</li>";
echo "<li>Verificar credenciales en <code>config/database.php</code></li>";
echo "</ul>";

echo "<hr>";
echo "<p><small>Wiribie v2.1 - Sistema de verificación automática</small></p>";
?>