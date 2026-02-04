<?php
// profile.php - VERSION 2.1 CON COMPONENTE
require_once 'includes/functions.php';
requireLogin();

require_once 'config/database.php';
$database = new Database();
$conn = $database->getConnection();
$current_user = getCurrentUser($conn);

// Obtener usuario del perfil
$username = $_GET['user'] ?? '';
if (empty($username)) {
    header("Location: index.php");
    exit();
}

$profile_user = getUserByUsername($conn, $username);
if (!$profile_user) {
    header("Location: index.php");
    exit();
}

$is_own_profile = ($profile_user['id'] == $current_user['id']);
$is_following = !$is_own_profile && isFollowing($conn, $current_user['id'], $profile_user['id']);
$posts = getUserPosts($conn, $profile_user['id']);
$posts_count = getUserPostsCount($conn, $profile_user['id']);

$pageTitle = "@" . $profile_user['username'] . " - Wiribie";
include 'includes/header.php';
include 'includes/navbar.php';
require_once 'includes/post_component.php';
?>

<div class="max-w-4xl mx-auto px-4 py-6 pb-24 md:pb-6">
    
    <!-- Profile Header -->
    <div class="bg-white rounded-3xl shadow-lg overflow-hidden mb-6">
        <!-- Cover Image -->
        <div class="h-40 md:h-56 bg-gradient-to-r from-blue-400 via-purple-500 to-pink-500 relative">
            <div class="absolute inset-0 bg-black bg-opacity-20"></div>
        </div>
        
        <div class="px-6 pb-6">
            <!-- Profile Picture & Actions -->
            <div class="flex flex-col md:flex-row md:items-end md:justify-between -mt-20 md:-mt-24 relative z-10">
                <div class="flex flex-col md:flex-row md:items-end md:space-x-5">
                    <img src="uploads/profiles/<?php echo sanitize($profile_user['profile_pic']); ?>" 
                         alt="<?php echo sanitize($profile_user['username']); ?>"
                         class="w-36 h-36 md:w-44 md:h-44 rounded-full border-4 border-white object-cover shadow-2xl ring-4 ring-gray-100">
                    
                    <div class="mt-4 md:mt-0 md:mb-4">
                        <div class="flex items-center space-x-2">
                            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
                                <?php echo sanitize($profile_user['full_name'] ?? $profile_user['username']); ?>
                            </h1>
                            <svg class="w-6 h-6 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-gray-600 text-lg">@<?php echo sanitize($profile_user['username']); ?></p>
                        <p class="text-sm text-gray-500 mt-1">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Se unió en <?php echo date('F Y', strtotime($profile_user['created_at'])); ?>
                        </p>
                    </div>
                </div>
                
                <div class="mt-4 md:mt-0 flex space-x-3">
                    <?php if ($is_own_profile): ?>
                        <a href="edit-profile.php" 
                           class="flex-1 md:flex-none inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-semibold rounded-xl smooth-transition shadow-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Editar perfil
                        </a>
                    <?php else: ?>
                        <button onclick="toggleFollow(<?php echo $profile_user['id']; ?>)"
                                id="follow-btn"
                                class="flex-1 md:flex-none inline-flex items-center justify-center px-6 py-3 font-semibold rounded-xl smooth-transition shadow-lg <?php echo $is_following ? 'bg-gray-200 hover:bg-gray-300 text-gray-700' : 'bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white'; ?>">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <?php if ($is_following): ?>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                <?php else: ?>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                <?php endif; ?>
                            </svg>
                            <?php echo $is_following ? 'Siguiendo' : 'Seguir'; ?>
                        </button>
                        <button class="px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl smooth-transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Bio con linkify -->
            <?php if ($profile_user['bio']): ?>
                <div class="mt-6">
                    <div class="text-gray-700 text-lg leading-relaxed post-content">
                        <?php echo linkify($profile_user['bio']); ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Stats -->
            <div class="flex flex-wrap gap-6 mt-6 pt-6 border-t border-gray-200">
                <div class="text-center">
                    <div class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                        <?php echo formatNumber($posts_count); ?>
                    </div>
                    <div class="text-sm text-gray-600 font-medium mt-1">Publicaciones</div>
                </div>
                
                <div class="text-center cursor-pointer hover:bg-gray-50 px-4 py-2 rounded-xl smooth-transition">
                    <div class="text-3xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent followers-count">
                        <?php echo formatNumber($profile_user['followers_count']); ?>
                    </div>
                    <div class="text-sm text-gray-600 font-medium mt-1">Seguidores</div>
                </div>
                
                <div class="text-center cursor-pointer hover:bg-gray-50 px-4 py-2 rounded-xl smooth-transition">
                    <div class="text-3xl font-bold bg-gradient-to-r from-pink-600 to-red-600 bg-clip-text text-transparent">
                        <?php echo formatNumber($profile_user['following_count']); ?>
                    </div>
                    <div class="text-sm text-gray-600 font-medium mt-1">Siguiendo</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Posts Tabs -->
    <div class="bg-white rounded-2xl shadow-sm mb-6">
        <div class="border-b border-gray-200">
            <div class="flex">
                <button class="flex-1 px-4 py-4 text-center font-semibold text-blue-500 border-b-2 border-blue-500 smooth-transition">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    Publicaciones
                </button>
            </div>
        </div>
    </div>

    <!-- User Posts -->
    <div class="space-y-6">
        <?php if (empty($posts)): ?>
            <div class="bg-white rounded-2xl shadow-sm p-12 text-center">
                <svg class="w-20 h-20 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">
                    <?php echo $is_own_profile ? 'Aún no has publicado' : 'No hay publicaciones'; ?>
                </h3>
                <p class="text-gray-500 mb-4">
                    <?php echo $is_own_profile ? '¡Comparte tu primera publicación!' : 'Este usuario aún no ha compartido nada'; ?>
                </p>
                <?php if ($is_own_profile): ?>
                    <button onclick="document.getElementById('postModal').classList.remove('hidden')"
                            class="px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-semibold rounded-xl smooth-transition shadow-lg">
                        Crear Publicación
                    </button>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <?php renderPost($post, $current_user, $conn); ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Modales (igual que index.php) -->
<!-- COPIAR TODOS LOS MODALES DE INDEX.PHP AQUÍ -->

<script src="assets/js/main.js"></script>
<script src="assets/js/video-autoplay.js"></script>
</body>
</html>