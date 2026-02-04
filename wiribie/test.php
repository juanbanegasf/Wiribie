<?php
// test.php - DIAGNÓSTICO
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Diagnóstico de Wiribie</h1>";

// Test 1: Verificar archivo functions.php
if (file_exists('includes/functions.php')) {
    echo "<p style='color:green'>✅ includes/functions.php existe</p>";
    
    // Intentar incluir
    try {
        require_once 'includes/functions.php';
        echo "<p style='color:green'>✅ includes/functions.php se incluyó correctamente</p>";
        
        // Test 2: Verificar función requireLogin
        if (function_exists('requireLogin')) {
            echo "<p style='color:green'>✅ Función requireLogin() está definida</p>";
        } else {
            echo "<p style='color:red'>❌ Función requireLogin() NO está definida</p>";
        }
        
        // Test 3: Verificar otras funciones
        $functions = ['isLoggedIn', 'getCurrentUser', 'sanitize', 'formatNumber', 'linkify', 'truncateText'];
        foreach ($functions as $func) {
            if (function_exists($func)) {
                echo "<p style='color:green'>✅ Función $func() existe</p>";
            } else {
                echo "<p style='color:red'>❌ Función $func() NO existe</p>";
            }
        }
        
    } catch (Exception $e) {
        echo "<p style='color:red'>❌ Error al incluir functions.php: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color:red'>❌ includes/functions.php NO existe</p>";
    echo "<p>Ruta completa buscada: " . realpath('includes/functions.php') . "</p>";
}

// Test 4: Verificar database.php
if (file_exists('config/database.php')) {
    echo "<p style='color:green'>✅ config/database.php existe</p>";
    
    try {
        require_once 'config/database.php';
        echo "<p style='color:green'>✅ config/database.php se incluyó correctamente</p>";
        
        if (class_exists('Database')) {
            echo "<p style='color:green'>✅ Clase Database existe</p>";
            
            $db = new Database();
            $conn = $db->getConnection();
            
            if ($conn) {
                echo "<p style='color:green'>✅ Conexión a BD exitosa</p>";
            } else {
                echo "<p style='color:red'>❌ No se pudo conectar a la BD</p>";
            }
        } else {
            echo "<p style='color:red'>❌ Clase Database NO existe</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color:red'>❌ Error con database.php: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color:red'>❌ config/database.php NO existe</p>";
}

echo "<hr>";
echo "<p>Si ves errores arriba, copia el mensaje exacto y lo corregimos.</p>";
echo "<p>Si todo está ✅, entonces <a href='index.php'>ir a index.php</a></p>";
?>