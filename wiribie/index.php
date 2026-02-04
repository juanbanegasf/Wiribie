<?php
// index.php - VERSION COMPLETA 2.1
require_once 'includes/functions.php';
requireLogin();

require_once 'config/database.php';
$database = new Database();
$conn = $database->getConnection();

$current_user = getCurrentUser($conn);
$posts = getFeedPosts($conn);

$pageTitle = "Inicio - Wiribie";
include 'includes/header.php';
include 'includes/navbar.php';
require_once 'includes/post_component.php';
?>

<div class="max-w-2xl mx-auto px-4 py-6 pb-24 md:pb-6">
    
    <!-- Stories Section -->
    <div class="bg-white rounded-2xl shadow-sm p-4 mb-6 overflow-x-auto">
        <div class="flex space-x-4">
            <!-- Tu historia -->
            <div class="flex flex-col items-center flex-shrink-0 cursor-pointer group">
                <div class="w-16 h-16 rounded-full bg-gradient-to-tr from-yellow-400 via-red-500 to-purple-600 p-0.5">
                    <div class="w-full h-full rounded-full border-2 border-white overflow-hidden">
                        <img src="uploads/profiles/<?php echo sanitize($current_user['profile_pic']); ?>" 
                             class="w-full h-full object-cover">
                    </div>
                </div>
                <span class="text-xs mt-1 text-gray-600 group-hover:text-blue-500">Tu historia</span>
            </div>
            
            <!-- Historias de otros -->
            <?php for($i = 1; $i <= 8; $i++): ?>
            <div class="flex flex-col items-center flex-shrink-0 cursor-pointer group">
                <div class="w-16 h-16 rounded-full bg-gradient-to-tr from-purple-400 to-pink-600 p-0.5">
                    <div class="w-full h-full rounded-full border-2 border-white overflow-hidden bg-gray-300">
                        <svg class="w-full h-full text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                    </div>
                </div>
                <span class="text-xs mt-1 text-gray-600 group-hover:text-blue-500 truncate w-16 text-center">Usuario<?php echo $i; ?></span>
            </div>
            <?php endfor; ?>
        </div>
    </div>

    <!-- Create Post Button (Desktop) -->
    <div class="hidden md:block bg-white rounded-2xl shadow-sm p-4 mb-6 hover:shadow-md transition-shadow">
        <div class="flex items-center space-x-3">
            <img src="uploads/profiles/<?php echo sanitize($current_user['profile_pic']); ?>" 
                 class="w-10 h-10 rounded-full object-cover">
            <button onclick="document.getElementById('postModal').classList.remove('hidden')"
                    class="flex-1 text-left px-4 py-3 bg-gray-100 hover:bg-gray-200 rounded-full text-gray-500 smooth-transition">
                ¿Qué estás pensando, <?php echo sanitize($current_user['full_name'] ?? $current_user['username']); ?>?
            </button>
        </div>
        <div class="flex justify-around mt-3 pt-3 border-t border-gray-100">
            <button onclick="document.getElementById('postModal').classList.remove('hidden')" 
                    class="flex items-center space-x-2 text-gray-600 hover:bg-gray-100 px-4 py-2 rounded-lg smooth-transition">
                <svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                </svg>
                <span class="font-medium">Foto/Video</span>
            </button>
            <button onclick="document.getElementById('postModal').classList.remove('hidden')"
                    class="flex items-center space-x-2 text-gray-600 hover:bg-gray-100 px-4 py-2 rounded-lg smooth-transition">
                <svg class="w-6 h-6 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"/>
                </svg>
                <span class="font-medium">Sentimiento</span>
            </button>
        </div>
    </div>

    <!-- Posts Feed -->
    <div class="space-y-6" id="postsContainer">
        <?php if (empty($posts)): ?>
            <div class="bg-white rounded-2xl shadow-sm p-12 text-center">
                <svg class="w-20 h-20 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">No hay publicaciones aún</h3>
                <p class="text-gray-500 mb-4">¡Sé el primero en compartir algo increíble!</p>
                <button onclick="document.getElementById('postModal').classList.remove('hidden')"
                        class="px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-semibold rounded-xl smooth-transition shadow-lg">
                    Crear Publicación
                </button>
            </div>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <?php renderPost($post, $current_user, $conn); ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Create Post Modal -->
<div id="postModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center rounded-t-2xl z-10">
            <h3 class="text-xl font-semibold">Crear publicación</h3>
            <button onclick="document.getElementById('postModal').classList.add('hidden')" 
                    class="text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-full p-2 smooth-transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form action="actions/post_action.php" method="POST" enctype="multipart/form-data" class="p-6">
            <div class="flex items-start space-x-3 mb-4">
                <img src="uploads/profiles/<?php echo sanitize($current_user['profile_pic']); ?>" 
                     alt="You" 
                     class="w-12 h-12 rounded-full object-cover">
                <div class="flex-1">
                    <p class="font-semibold"><?php echo sanitize($current_user['full_name'] ?? $current_user['username']); ?></p>
                    <p class="text-sm text-gray-500">@<?php echo sanitize($current_user['username']); ?></p>
                </div>
            </div>

            <textarea name="content" 
                      rows="4" 
                      placeholder="¿Qué estás pensando?"
                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none resize-none text-lg"></textarea>
            
            <p class="text-xs text-gray-500 mt-2">
                Usa <span class="text-sky-500 font-semibold">#hashtags</span>, 
                <span class="text-blue-800 font-semibold">@menciones</span> y 
                <span class="text-blue-600 font-semibold">enlaces</span>
            </p>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Agregar multimedia</label>
                <input type="file" 
                       name="media" 
                       accept="image/*,video/*,.gif"
                       onchange="previewMedia(this)"
                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <p class="text-xs text-gray-500 mt-1">Imágenes, GIFs o Videos (Máx. 50MB)</p>
            </div>

            <div id="mediaPreview" class="mt-4 hidden">
                <div class="media-container">
                    <img id="imagePreview" class="hidden rounded-xl" alt="Preview">
                    <video id="videoPreview" class="hidden rounded-xl" controls></video>
                </div>
            </div>

            <button type="submit" 
                    class="w-full mt-6 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-semibold py-3 rounded-xl smooth-transition shadow-lg hover:shadow-xl">
                Publicar
            </button>
        </form>
    </div>
</div>

<!-- Repost Modal -->
<div id="repostModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-xl font-semibold">Repostear</h3>
            <button onclick="closeRepostModal()" 
                    class="text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-full p-2 smooth-transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="p-6">
            <div class="flex items-start space-x-3 mb-4">
                <img src="uploads/profiles/<?php echo sanitize($current_user['profile_pic']); ?>" 
                     alt="You" 
                     class="w-12 h-12 rounded-full object-cover">
                <div class="flex-1">
                    <p class="font-semibold"><?php echo sanitize($current_user['full_name'] ?? $current_user['username']); ?></p>
                    <p class="text-sm text-gray-500">@<?php echo sanitize($current_user['username']); ?></p>
                </div>
            </div>

            <textarea id="repostText" 
                      rows="3" 
                      placeholder="Agrega un comentario (opcional)..."
                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none resize-none mb-4"></textarea>

            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl p-4 mb-4">
                <p class="text-sm text-green-800 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M7 7h10v3l4-4-4-4v3H5v6h2V7zm10 10H7v-3l-4 4 4 4v-3h12v-6h-2v4z"/>
                    </svg>
                    Repostearás esta publicación en tu perfil
                </p>
            </div>

            <div class="flex space-x-3">
                <button onclick="closeRepostModal()" 
                        class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 smooth-transition">
                    Cancelar
                </button>
                <button onclick="confirmRepost()" 
                        class="flex-1 px-4 py-3 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-semibold rounded-xl smooth-transition">
                    Repostear
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Fullscreen Post Modal -->
<div id="fullscreenModal" class="hidden fixed inset-0 bg-black z-50">
    <div class="h-full flex flex-col">
        <!-- Header -->
        <div class="flex items-center justify-between p-4 bg-black bg-opacity-50">
            <button onclick="closeFullscreen()" class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <div class="flex items-center space-x-3">
                <button class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Content -->
        <div class="flex-1 flex items-center justify-center p-4" id="fullscreenContent">
            <!-- Contenido dinámico -->
        </div>

        <!-- Footer Stats -->
        <div class="bg-black bg-opacity-50 p-4 text-white">
            <div class="flex items-center justify-around max-w-md mx-auto">
                <div class="text-center">
                    <div class="text-2xl font-bold" id="fs-likes">0</div>
                    <div class="text-xs text-gray-300">Me gusta</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold" id="fs-comments">0</div>
                    <div class="text-xs text-gray-300">Comentarios</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold" id="fs-reposts">0</div>
                    <div class="text-xs text-gray-300">Reposts</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Report Modal -->
<div id="reportModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-xl font-semibold">Reportar publicación</h3>
        </div>
        <div class="p-6">
            <p class="text-gray-600 mb-4">¿Por qué reportas esta publicación?</p>
            <textarea id="reportReason" 
                      rows="4" 
                      placeholder="Describe el motivo del reporte..."
                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent outline-none resize-none mb-4"></textarea>
            <div class="flex space-x-3">
                <button onclick="closeReportModal()" 
                        class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 smooth-transition">
                    Cancelar
                </button>
                <button onclick="confirmReport()" 
                        class="flex-1 px-4 py-3 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-xl smooth-transition">
                    Reportar
                </button>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/main.js"></script>
<script src="assets/js/video-autoplay.js"></script>
</body>
</html>