═══════════════════════════════════════════════════════════════════════
  🚀 WIRIBIE v2.0 - RED SOCIAL COMPLETA
  Documentación Completa con Nuevas Características
═══════════════════════════════════════════════════════════════════════

📋 ÍNDICE
─────────────────────────────────────────────────────────────────────
1. NOVEDADES DE LA VERSIÓN 2.0
2. INSTALACIÓN PASO A PASO
3. ACTUALIZACIÓN DESDE v1.0
4. CARACTERÍSTICAS IMPLEMENTADAS
5. ESTRUCTURA DE LA BASE DE DATOS
6. GUÍA DE USO DE FUNCIONALIDADES
7. RESOLUCIÓN DE PROBLEMAS
8. CONFIGURACIÓN AVANZADA
9. API Y ENDPOINTS
10. CRÉDITOS Y LICENCIA

═══════════════════════════════════════════════════════════════════════
1️⃣ NOVEDADES DE LA VERSIÓN 2.0
═══════════════════════════════════════════════════════════════════════

✨ NUEVAS CARACTERÍSTICAS:

🔹 SISTEMA DE SEGUIDORES
   - Seguir/Dejar de seguir usuarios
   - Contador de seguidores y siguiendo
   - Notificaciones al recibir nuevos seguidores
   - Badge verificado para usuarios

🔹 BÚSQUEDA INTELIGENTE
   - Búsqueda por usuarios, posts, imágenes y videos
   - Filtros avanzados
   - Página dedicada de búsqueda
   - Resultados en tiempo real

🔹 SISTEMA DE NOTIFICACIONES
   - Notificaciones de likes, comentarios, reposts
   - Notificaciones de nuevos seguidores
   - Badge rojo con contador de notificaciones no leídas
   - Página dedicada de notificaciones

🔹 REPOSTS MEJORADOS
   - Modal con texto opcional al repostear
   - Indicador visual "X reposteó el post de Y"
   - Contador de reposts en BD

🔹 LIKES Y RESPUESTAS EN COMENTARIOS
   - Sistema de likes en comentarios
   - Respuestas anidadas a comentarios
   - Contador de likes en cada comentario

🔹 VIDEOS AUTO-REPRODUCIBLES
   - Reproducción automática al entrar en viewport
   - Pausa automática al salir del viewport
   - Solo un video se reproduce a la vez
   - Sistema de Intersection Observer

🔹 MENÚ DE OPCIONES EN POSTS
   - Editar/Eliminar (posts propios)
   - Reportar (posts de otros)
   - Modal de confirmación
   - Sistema de reportes

🔹 VISTA FULLSCREEN
   - Click en imagen/video abre fullscreen
   - Sin canvas, imagen completa
   - Estadísticas al pie (likes, comentarios, reposts)
   - Navegación mejorada

🔹 CONTADORES EN BASE DE DATOS
   - Campo likes_count en posts
   - Campo comments_count en posts
   - Campo reposts_count en posts
   - Campo followers_count en users
   - Campo following_count en users
   - Triggers automáticos para mantener sincronización

🔹 FORMATEO DE NÚMEROS
   - 1,000 → 1K
   - 11,200 → 11.2K
   - 1,000,000 → 1M
   - Aplicado en todo el sitio

🔹 FEED ALEATORIO
   - Posts ordenados aleatoriamente cada 5 minutos
   - Sistema de seed basado en tiempo
   - Mantiene frescura del contenido

🔹 DISEÑO MEJORADO
   - Stories section en index
   - Gradientes y animaciones
   - Badges verificados
   - UI más moderna y colorida
   - Mejor UX en móvil

🔹 TIEMPO CORREGIDO
   - Fix: Ya no empieza desde 7h
   - Formato correcto: Ahora, 5m, 2h, 3d, 1sem, 2mes, 1a
   - timeAgo() corregido

🔹 BRANDING ACTUALIZADO
   - "SocialHub" → "Wiribie"
   - Nuevo logo con "W"
   - Colores: Azul/Púrpura/Rosa
   - Favicon personalizado

═══════════════════════════════════════════════════════════════════════
2️⃣ INSTALACIÓN PASO A PASO
═══════════════════════════════════════════════════════════════════════

REQUISITOS PREVIOS:
------------------
✓ XAMPP/WAMP/MAMP (PHP 7.4+, MySQL 5.7+)
✓ Navegador moderno (Chrome, Firefox, Edge, Safari)
✓ Editor de código (VS Code recomendado)

PASO 1: CREAR ESTRUCTURA
-------------------------
1. Crear carpeta: socialhub/
2. Crear subcarpetas:
   - config/
   - includes/
   - actions/
   - uploads/profiles/
   - uploads/posts/
   - assets/js/
   - database/

3. Establecer permisos (Linux/Mac):
   chmod -R 755 socialhub/
   chmod -R 777 uploads/

PASO 2: BASE DE DATOS
----------------------
1. Abrir phpMyAdmin: http://localhost/phpmyadmin
2. Crear nueva base de datos: "socialhub"
3. Seleccionar "socialhub"
4. Ir a pestaña "SQL"
5. Copiar y ejecutar database.sql (v1.0)
6. Copiar y ejecutar database_update_v2.sql (v2.0)
7. Verificar que se crearon 8 tablas:
   ✓ users
   ✓ posts
   ✓ likes
   ✓ comments
   ✓ reposts
   ✓ follows
   ✓ notifications
   ✓ reports
   ✓ comment_likes

PASO 3: CONFIGURACIÓN
---------------------
1. Editar config/database.php:

   private $host = "localhost";
   private $db_name = "socialhub";
   private $username = "root";      // Tu usuario MySQL
   private $password = "";          // Tu contraseña MySQL

2. Verificar que Apache y MySQL estén corriendo

PASO 4: ARCHIVOS
-----------------
Copiar todos los archivos proporcionados en su ubicación:

RAÍZ:
- index.php
- login.php
- register.php
- profile.php
- edit-profile.php
- search.php
- notifications.php
- logout.php
- .htaccess

CONFIG/:
- database.php

INCLUDES/:
- functions.php
- header.php
- navbar.php

ACTIONS/:
- login_action.php
- register_action.php
- post_action.php
- like_action.php
- comment_action.php
- comment_like_action.php
- repost_action.php
- follow_action.php
- upload_profile_pic.php
- update_profile.php
- delete_post_action.php
- report_action.php
- get_post_action.php
- get_post_stats.php
- mark_notifications_read.php

ASSETS/JS/:
- main.js
- video-autoplay.js

PASO 5: AVATAR DEFAULT
-----------------------
1. Descargar imagen desde:
   https://ui-avatars.com/api/?name=User&size=400&background=3B82F6&color=fff&rounded=true

2. Guardar como: uploads/profiles/default-avatar.png

O crear tu propia imagen 400x400px

PASO 6: VERIFICACIÓN
---------------------
1. Abrir: http://localhost/socialhub/
2. Debería redirigir a login.php
3. Hacer click en "Regístrate"
4. Crear una cuenta de prueba
5. Iniciar sesión
6. Crear una publicación de prueba

¡LISTO! 🎉

═══════════════════════════════════════════════════════════════════════
3️⃣ ACTUALIZACIÓN DESDE v1.0
═══════════════════════════════════════════════════════════════════════

Si ya tienes la versión 1.0 instalada:

PASO 1: BACKUP
--------------
1. Exportar base de datos actual:
   phpMyAdmin → Exportar → Guardar como: backup_v1.sql

2. Copiar carpeta uploads/ a lugar seguro

PASO 2: ACTUALIZAR BASE DE DATOS
---------------------------------
1. En phpMyAdmin, seleccionar base de datos "socialhub"
2. Ir a pestaña SQL
3. Ejecutar database_update_v2.sql (proporcionado anteriormente)
4. Verificar que se agregaron nuevas columnas y tablas

PASO 3: ACTUALIZAR ARCHIVOS
----------------------------
1. Reemplazar TODOS los archivos PHP con las nuevas versiones
2. Agregar nuevos archivos:
   - search.php
   - notifications.php
   - actions/follow_action.php
   - actions/comment_like_action.php
   - actions/delete_post_action.php
   - actions/report_action.php
   - actions/get_post_action.php
   - actions/get_post_stats.php
   - actions/mark_notifications_read.php
   - assets/js/video-autoplay.js

3. Actualizar archivos existentes:
   - includes/functions.php
   - includes/navbar.php
   - index.php
   - profile.php
   - edit-profile.php
   - login.php
   - register.php
   - assets/js/main.js

PASO 4: LIMPIAR CACHÉ
----------------------
1. Ctrl + Shift + Delete en navegador
2. Limpiar cookies de localhost
3. Recargar con Ctrl + F5

═══════════════════════════════════════════════════════════════════════
4️⃣ CARACTERÍSTICAS IMPLEMENTADAS
═══════════════════════════════════════════════════════════════════════

✅ AUTENTICACIÓN
   - Registro con validación
   - Login con sesiones PHP
   - Logout
   - Protección de rutas

✅ PERFILES
   - Perfil personalizable
   - Foto de perfil (JPG, PNG, GIF)
   - Biografía (300 caracteres)
   - Estadísticas: Posts, Seguidores, Siguiendo
   - URL limpia: /profile.php?user=username

✅ PUBLICACIONES
   - Crear posts con texto
   - Subir imágenes (JPG, PNG, GIF)
   - Subir videos (MP4)
   - Contenedor 3:2 aspect ratio
   - Vista previa antes de publicar
   - Editar posts (próximamente)
   - Eliminar posts

✅ INTERACCIONES
   - Likes en posts
   - Likes en comentarios
   - Comentarios
   - Respuestas a comentarios
   - Reposts con texto opcional
   - Compartir posts

✅ SOCIAL
   - Seguir/Dejar de seguir usuarios
   - Ver seguidores y siguiendo
   - Notificaciones en tiempo real
   - Badge de notificaciones no leídas

✅ BÚSQUEDA
   - Buscar usuarios
   - Buscar posts
   - Filtrar por imágenes
   - Filtrar por videos
   - Resultados instantáneos

✅ NOTIFICACIONES
   - Nuevos seguidores
   - Likes en tus posts
   - Comentarios en tus posts
   - Reposts de tus posts
   - Respuestas a tus comentarios
   - Likes en tus comentarios

✅ MODERACIÓN
   - Reportar posts
   - Sistema de reportes
   - Eliminar posts propios

✅ MULTIMEDIA
   - Videos autoplay en viewport
   - Pausa automática
   - Vista fullscreen
   - Optimización de carga

✅ UX/UI
   - Diseño responsive
   - Mobile-first
   - Animaciones suaves
   - Gradientes modernos
   - Dark patterns evitados
   - Accesibilidad mejorada

═══════════════════════════════════════════════════════════════════════
5️⃣ ESTRUCTURA DE LA BASE DE DATOS
═══════════════════════════════════════════════════════════════════════

DIAGRAMA DE RELACIONES:

users (1) ──── (N) posts
  │               │
  │               ├── (N) likes
  │               ├── (N) comments ──── (N) comment_likes
  │               ├── (N) reposts
  │               └── (N) reports
  │
  ├── (N) follows (como follower)
  ├── (N) follows (como following)
  └── (N) notifications

TABLAS DETALLADAS:

📊 users
┌─────────────────┬──────────┬─────────┬──────────────────┐
│ Campo           │ Tipo     │ Null    │ Default          │
├─────────────────┼──────────┼─────────┼──────────────────┤
│ id              │ INT      │ NO      │ AUTO_INCREMENT   │
│ username        │ VARCHAR  │ NO      │ -                │
│ email           │ VARCHAR  │ NO      │ -                │
│ password        │ VARCHAR  │ NO      │ -                │
│ full_name       │ VARCHAR  │ YES     │ NULL             │
│ bio             │ TEXT     │ YES     │ NULL             │
│ profile_pic     │ VARCHAR  │ NO      │ default-avatar   │
│ followers_count │ INT      │ NO      │ 0                │
│ following_count │ INT      │ NO      │ 0                │
│ created_at      │ TIMESTAMP│ NO      │ CURRENT_TIME     │
└─────────────────┴──────────┴─────────┴──────────────────┘

📊 posts
┌──────────────┬──────────┬─────────┬──────────────────┐
│ Campo        │ Tipo     │ Null    │ Default          │
├──────────────┼──────────┼─────────┼──────────────────┤
│ id           │ INT      │ NO      │ AUTO_INCREMENT   │
│ user_id      │ INT      │ NO      │ -                │
│ content      │ TEXT     │ YES     │ NULL             │
│ media_type   │ ENUM     │ NO      │ 'none'           │
│ media_url    │ VARCHAR  │ YES     │ NULL             │
│ likes_count  │ INT      │ NO      │ 0                │
│ comments_cnt │ INT      │ NO      │ 0                │
│ reposts_cnt  │ INT      │ NO      │ 0                │
│ created_at   │ TIMESTAMP│ NO      │ CURRENT_TIME     │
└──────────────┴──────────┴─────────┴──────────────────┘

📊 follows
┌──────────────┬──────────┬─────────┬──────────────────┐
│ Campo        │ Tipo     │ Null    │ Default          │
├──────────────┼──────────┼─────────┼──────────────────┤
│ id           │ INT      │ NO      │ AUTO_INCREMENT   │
│ follower_id  │ INT      │ NO      │ -                │
│ following_id │ INT      │ NO      │ -                │
│ created_at   │ TIMESTAMP│ NO      │ CURRENT_TIME     │
└──────────────┴──────────┴─────────┴──────────────────┘
UNIQUE: (follower_id, following_id)

📊 notifications
┌──────────────┬──────────┬─────────┬──────────────────┐
│ Campo        │ Tipo     │ Null    │ Default          │
├──────────────┼──────────┼─────────┼──────────────────┤
│ id           │ INT      │ NO      │ AUTO_INCREMENT   │
│ user_id      │ INT      │ NO      │ -                │
│ from_user_id │ INT      │ NO      │ -                │
│ type         │ ENUM     │ NO      │ -                │
│ post_id      │ INT      │ YES     │ NULL             │
│ comment_id   │ INT      │ YES     │ NULL             │
│ is_read      │ BOOLEAN  │ NO      │ FALSE            │
│ created_at   │ TIMESTAMP│ NO      │ CURRENT_TIME     │
└──────────────┴──────────┴─────────┴──────────────────┘

TIPOS DE NOTIFICACIONES:
- 'like': Alguien dio like a tu post
- 'comment': Alguien comentó tu post
- 'repost': Alguien reposteó tu post
- 'follow': Alguien te siguió
- 'comment_reply': Alguien respondió tu comentario

═══════════════════════════════════════════════════════════════════════
6️⃣ GUÍA DE USO DE FUNCIONALIDADES
═══════════════════════════════════════════════════════════════════════

🔹 CREAR PUBLICACIÓN
-------------------
1. Click en "¿Qué estás pensando?" (Desktop)
   O botón "+" flotante (Mobile)
2. Escribir texto (opcional)
3. Seleccionar imagen/video (opcional)
4. Vista previa aparece automáticamente
5. Click "Publicar"

🔹 DAR LIKE
-----------
1. Click en ❤️ bajo la publicación
2. El corazón se llena de rojo
3. El contador aumenta
4. El autor recibe notificación

🔹 COMENTAR
-----------
1. Click en 💬 "Comentar"
2. Escribir comentario
3. Presionar Enter o click en ➤
4. El comentario aparece inmediatamente
5. El autor recibe notificación

🔹 RESPONDER COMENTARIO
-----------------------
1. Click en "Responder" bajo un comentario
2. Se abre campo de texto
3. Escribir respuesta
4. El autor del comentario recibe notificación

🔹 DAR LIKE A COMENTARIO
------------------------
1. Click en "Me gusta (X)" bajo el comentario
2. El número cambia de color
3. El contador aumenta

🔹 REPOSTEAR
------------
1. Click en 🔄 "Repostear"
2. Se abre modal
3. Opcionalmente agregar texto
4. Click "Repostear"
5. Aparece en tu perfil con indicador
6. El autor original recibe notificación

🔹 SEGUIR USUARIO
-----------------
1. Visitar perfil del usuario
2. Click en "Seguir"
3. El botón cambia a "Siguiendo"
4. Tus contadores se actualizan
5. El usuario recibe notificación

🔹 VER NOTIFICACIONES
---------------------
1. Click en 🔔 (badge rojo indica nuevas)
2. Se abre página de notificaciones
3. Notificaciones agrupadas por tipo
4. Badge desaparece al abrir

🔹 BUSCAR
---------
1. Click en barra de búsqueda
2. Escribir término
3. Presionar Enter
4. Usar filtros: Todo, Usuarios, Posts, Imágenes, Videos
5. Click en resultado para ver

🔹 VER IMAGEN/VIDEO FULLSCREEN
-------------------------------
1. Click en imagen/video de post
2. Se abre en fullscreen
3. Swipe o ESC para cerrar
4. Estadísticas en la parte inferior

🔹 REPORTAR POST
----------------
1. Click en ⋮ (tres puntos) en post
2. Seleccionar "Reportar"
3. Escribir motivo
4. Click "Reportar"
5. El reporte se envía a moderación

🔹 ELIMINAR POST
----------------
1. Click en ⋮ en tu propio post
2. Seleccionar "Eliminar"
3. Confirmar
4. El post se elimina permanentemente

🔹 EDITAR PERFIL
----------------
1. Ir a tu perfil
2. Click "Editar perfil"
3. Cambiar foto de perfil
4. Actualizar información
5. Click "Guardar Cambios"

🔹 CERRAR SESIÓN
----------------
Opción 1: Click en "Salir" en menú inferior (mobile)
Opción 2: Editar perfil → Zona Peligrosa → Cerrar Sesión

═══════════════════════════════════════════════════════════════════════
7️⃣ RESOLUCIÓN DE PROBLEMAS
═══════════════════════════════════════════════════════════════════════

❌ PROBLEMA: "Connection Error"
✅ SOLUCIÓN:
   1. Verificar que MySQL esté corriendo
   2. Verificar credenciales en config/database.php
   3. Verificar que existe base de datos "socialhub"
   4. Verificar usuario/contraseña MySQL

❌ PROBLEMA: Videos no se reproducen automáticamente
✅ SOLUCIÓN:
   1. Verificar que video-autoplay.js esté cargado
   2. Abrir DevTools → Console para ver errores
   3. Los navegadores bloquean autoplay con sonido
   4. Los videos están en muted por defecto

❌ PROBLEMA: Notificaciones no aparecen
✅ SOLUCIÓN:
   1. Verificar triggers en base de datos
   2. Verificar que createNotification() se llama
   3. Revisar Console del navegador
   4. Verificar permisos de tabla notifications

❌ PROBLEMA: Imágenes no se suben
✅ SOLUCIÓN:
   1. Verificar permisos: chmod 777 uploads/
   2. Verificar tamaño máximo en php.ini:
      upload_max_filesize = 50M
      post_max_size = 50M
   3. Verificar que carpetas uploads/posts/ exista
   4. Verificar tipo MIME permitido

❌ PROBLEMA: El tiempo aparece incorrecto
✅ SOLUCIÓN:
   1. Verificar zona horaria del servidor
   2. En php.ini: date.timezone = "America/Mexico_City"
   3. O en código: date_default_timezone_set()
   4. Verificar que se usa timeAgo() corregido

❌ PROBLEMA: Los contadores no se actualizan
✅ SOLUCIÓN:
   1. Verificar que los triggers existan
   2. Ejecutar en SQL:
      SHOW TRIGGERS;
   3. Re-ejecutar database_update_v2.sql
   4. Actualizar contadores manualmente:
      UPDATE posts p SET likes_count = (SELECT COUNT(*) FROM likes WHERE post_id = p.id);

❌ PROBLEMA: El feed está vacío
✅ SOLUCIÓN:
   1. Crear al menos una publicación
   2. Verificar que getFeedPosts() se ejecuta
   3. Revisar Console para errores JS
   4. Verificar que el usuario esté logueado

❌ PROBLEMA: No puedo seguir a nadie
✅ SOLUCIÓN:
   1. Verificar que tabla follows exista
   2. Verificar follow_action.php
   3. Ver errores en Console
   4. Verificar restricción UNIQUE

❌ PROBLEMA: Búsqueda no funciona
✅ SOLUCIÓN:
   1. Verificar que search.php exista
   2. Verificar función search() en functions.php
   3. Probar búsqueda simple primero
   4. Revisar errores SQL

═══════════════════════════════════════════════════════════════════════
8️⃣ CONFIGURACIÓN AVANZADA
═══════════════════════════════════════════════════════════════════════

🔧 LÍMITES DE ARCHIVO
---------------------
Editar .htaccess:

php_value upload_max_filesize 100M
php_value post_max_size 100M
php_value max_execution_time 600
php_value max_input_time 600

🔧 TIMEZONE
-----------
En config/database.php, después de la conexión:

$this->conn->exec("SET time_zone = '+00:00'");

O en includes/functions.php al inicio:

date_default_timezone_set('America/Mexico_City');

🔧 SEGURIDAD ADICIONAL
----------------------
1. Habilitar HTTPS en producción
2. Agregar CSRF tokens:

   // En formularios:
   $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
   <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

   // Al procesar:
   if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
       die('CSRF token inválido');
   }

3. Rate limiting:

   // En login_action.php
   if (!isset($_SESSION['login_attempts'])) {
       $_SESSION['login_attempts'] = 0;
   }
   if ($_SESSION['login_attempts'] >= 5) {
       die('Demasiados intentos. Espera 15 minutos.');
   }

🔧 OPTIMIZACIÓN
---------------
1. Habilitar compresión GZIP en .htaccess:

   <IfModule mod_deflate.c>
       AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript
   </IfModule>

2. Agregar caché de navegador:

   <IfModule mod_expires.c>
       ExpiresActive On
       ExpiresByType image/jpg "access plus 1 year"
       ExpiresByType image/jpeg "access plus 1 year"
       ExpiresByType image/gif "access plus 1 year"
       ExpiresByType image/png "access plus 1 year"
       ExpiresByType text/css "access plus 1 month"
       ExpiresByType application/javascript "access plus 1 month"
   </IfModule>

3. Optimizar consultas SQL:
   - Usar EXPLAIN antes de consultas complejas
   - Agregar índices donde sea necesario
   - Limitar resultados con LIMIT

🔧 BACKUP AUTOMÁTICO
--------------------
Crear script backup.sh:

#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u root -p socialhub > backups/socialhub_$DATE.sql
tar -czf backups/uploads_$DATE.tar.gz uploads/

Ejecutar diariamente con cron:
0 2 * * * /ruta/backup.sh

═══════════════════════════════════════════════════════════════════════
9️⃣ API Y ENDPOINTS
═══════════════════════════════════════════════════════════════════════

ENDPOINTS AJAX DISPONIBLES:

📍 POST /actions/like_action.php
   Parámetros: post_id
   Respuesta: {success: bool, liked: bool, count: int}

📍 POST /actions/comment_action.php
   Parámetros: post_id, content, parent_id (opcional)
   Respuesta: {success: bool}

📍 POST /actions/comment_like_action.php
   Parámetros: comment_id
   Respuesta: {success: bool, liked: bool}

📍 POST /actions/repost_action.php
   Parámetros: post_id, repost_text (opcional)
   Respuesta: {success: bool, reposted: bool, count: int}

📍 POST /actions/follow_action.php
   Parámetros: user_id
   Respuesta: {success: bool, following: bool}

📍 POST /actions/delete_post_action.php
   Parámetros: post_id
   Respuesta: {success: bool}

📍 POST /actions/report_action.php
   Parámetros: post_id, reason
   Respuesta: {success: bool}

📍 GET /actions/get_post_action.php
   Parámetros: id
   Respuesta: {success: bool, post: object}

📍 GET /actions/get_post_stats.php
   Parámetros: id
   Respuesta: {success: bool, stats: object}

📍 POST /actions/mark_notifications_read.php
   Sin parámetros
   Respuesta: {success: bool}

EJEMPLO DE USO:

fetch('actions/like_action.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'post_id=123'
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        console.log('Like:', data.liked);
        console.log('Total:', data.count);
    }
});

═══════════════════════════════════════════════════════════════════════
🔟 CRÉDITOS Y LICENCIA
═══════════════════════════════════════════════════════════════════════

💻 TECNOLOGÍAS UTILIZADAS:

Frontend:
- HTML5
- Tailwind CSS 3.x (CDN)
- JavaScript (Vanilla ES6+)
- Intersection Observer API
- Fetch API

Backend:
- PHP 7.4+
- PDO (PHP Data Objects)
- MySQL 5.7+ / MariaDB 10.2+

Librerías:
- Google Fonts (Inter)
- UI Avatars (avatares por defecto)

🎨 DISEÑO:
- Diseño minimalista
- Mobile-first approach
- Paleta de colores: Azul (#3B82F6), Púrpura (#A855F7), Rosa (#EC4899)
- Iconos: Heroicons (inline SVG)

📜 LICENCIA:
Este proyecto es de código abierto y puede ser usado libremente
para fines educativos y personales.

Para uso comercial, se requiere atribución.

⚖️ TÉRMINOS:
- No hay garantías de ningún tipo
- Úsalo bajo tu propio riesgo
- El desarrollador no se hace responsable de mal uso

✨ DESARROLLADO POR:
Claude AI Assistant

📅 VERSIÓN: 2.0
📅 FECHA: 2024

🌟 CARACTERÍSTICAS FUTURAS SUGERIDAS:

- Mensajes directos (DMs)
- Grupos/Comunidades
- Stories que desaparecen en 24h
- Transmisiones en vivo
- Hashtags y trending topics
- Encuestas
- Stickers y emojis personalizados
- Modo oscuro
- Múltiples idiomas (i18n)
- PWA (Progressive Web App)
- Notificaciones push
- Verificación de cuenta
- Monetización (ads, premium)
- Analytics avanzado

═══════════════════════════════════════════════════════════════════════

¡GRACIAS POR USAR WIRIBIE! 🚀

Para soporte o preguntas, consulta este README.

═══════════════════════════════════════════════════════════════════════