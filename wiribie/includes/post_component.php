<?php
// includes/post_component.php - VERSION COMPLETA 2.1
function renderPost($post, $current_user, $conn) {
    $has_liked = hasLiked($conn, $post['id'], $current_user['id']);
    $has_reposted = hasReposted($conn, $post['id'], $current_user['id']);
    $is_own_post = ($post['user_id'] == $current_user['id']);
    $is_own_repost = ($post['repost_user_id'] && $post['repost_user_id'] == $current_user['id']);
    
    // Procesar contenido con linkify y truncate
    $content_display = !empty($post['content']) ? truncateText($post['content'], 280) : '';
    ?>
    
    <div class="bg-white rounded-2xl shadow-sm hover:shadow-md smooth-transition overflow-hidden" 
         data-post-id="<?php echo $post['id']; ?>">
        
        <!-- Repost Header -->
        <?php if ($post['repost_user_id']): ?>
            <div class="px-4 pt-3 pb-2 bg-gradient-to-r from-green-50 to-emerald-50 border-b border-gray-100">
                <div class="flex items-center text-sm text-green-700">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M7 7h10v3l4-4-4-4v3H5v6h2V7zm10 10H7v-3l-4 4 4 4v-3h12v-6h-2v4z"/>
                    </svg>
                    <a href="profile.php?user=<?php echo sanitize($post['repost_username']); ?>" 
                       class="font-semibold hover:underline">
                        <?php echo sanitize($post['repost_username']); ?>
                    </a>
                    <span class="ml-1">reposteó</span>
                </div>
                <?php if ($post['repost_text']): ?>
                    <p class="text-sm text-gray-700 mt-1 ml-6 post-content"><?php echo linkify($post['repost_text']); ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="p-4">
            <!-- User Info -->
            <div class="flex items-start justify-between mb-3">
                <div class="flex items-start space-x-3 flex-1">
                    <a href="profile.php?user=<?php echo sanitize($post['username']); ?>">
                        <img src="uploads/profiles/<?php echo sanitize($post['profile_pic']); ?>" 
                             alt="<?php echo sanitize($post['username']); ?>"
                             class="w-12 h-12 rounded-full object-cover ring-2 ring-gray-100">
                    </a>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center space-x-2">
                            <a href="profile.php?user=<?php echo sanitize($post['username']); ?>" 
                               class="font-semibold text-gray-800 hover:underline truncate">
                                <?php echo sanitize($post['full_name'] ?? $post['username']); ?>
                            </a>
                            <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex items-center space-x-2 text-sm text-gray-500">
                            <a href="profile.php?user=<?php echo sanitize($post['username']); ?>" 
                               class="hover:underline">@<?php echo sanitize($post['username']); ?></a>
                            <span>•</span>
                            <span><?php echo timeAgo($post['repost_time'] ?? $post['created_at']); ?></span>
                        </div>
                    </div>
                </div>
                
                <!-- Menu de 3 puntos -->
                <div class="relative">
                    <button onclick="togglePostMenu(<?php echo $post['id']; ?>)" 
                            class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full p-2 smooth-transition">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                        </svg>
                    </button>
                    <div id="post-menu-<?php echo $post['id']; ?>" 
                         class="hidden absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl z-50 border border-gray-100 overflow-hidden">
                        
                        <?php if ($is_own_repost): ?>
                            <!-- Si es MI REPOST -->
                            <button onclick="deleteRepost(<?php echo $post['id']; ?>)"
                                    class="w-full text-left px-4 py-3 hover:bg-red-50 flex items-center space-x-2 text-red-600 smooth-transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                <span>Eliminar mi repost</span>
                            </button>
                            
                        <?php elseif ($is_own_post): ?>
                            <!-- Si es MI POST ORIGINAL -->
                            <button onclick="editPost(<?php echo $post['id']; ?>)"
                                    class="w-full text-left px-4 py-3 hover:bg-gray-50 flex items-center space-x-2 text-gray-700 smooth-transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                <span>Editar publicación</span>
                            </button>
                            <button onclick="deletePost(<?php echo $post['id']; ?>)"
                                    class="w-full text-left px-4 py-3 hover:bg-red-50 flex items-center space-x-2 text-red-600 smooth-transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                <span>Eliminar publicación</span>
                            </button>
                            
                        <?php else: ?>
                            <!-- Si es POST DE OTRO -->
                            <button onclick="reportPost(<?php echo $post['id']; ?>)"
                                    class="w-full text-left px-4 py-3 hover:bg-red-50 flex items-center space-x-2 text-red-600 smooth-transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <span>Reportar publicación</span>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Content con linkify -->
            <?php if ($post['content']): ?>
                <div class="text-gray-800 mb-3 text-lg leading-relaxed post-content break-words">
                    <?php echo $content_display; ?>
                </div>
            <?php endif; ?>

            <!-- Media -->
            <?php if ($post['media_type'] != 'none' && $post['media_url']): ?>
                <div class="media-container mb-3 cursor-pointer relative" 
                     onclick="openFullscreen(<?php echo $post['id']; ?>)">
                    <?php if ($post['media_type'] == 'video'): ?>
                        <video class="rounded-lg video-player" 
                               data-video-id="<?php echo $post['id']; ?>"
                               loop 
                               muted
                               playsinline>
                            <source src="uploads/posts/<?php echo sanitize($post['media_url']); ?>" type="video/mp4">
                        </video>
                    <?php else: ?>
                        <img src="uploads/posts/<?php echo sanitize($post['media_url']); ?>" 
                             alt="Post media"
                             class="rounded-lg">
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Stats -->
            <div class="flex items-center justify-between py-2 text-sm text-gray-500 border-b border-gray-100">
                <div class="flex items-center space-x-4">
                    <span class="flex items-center">
                        <span class="flex -space-x-1 mr-1">
                            <span class="w-5 h-5 rounded-full bg-red-500 flex items-center justify-center text-white text-xs">❤️</span>
                            <span class="w-5 h-5 rounded-full bg-blue-500 flex items-center justify-center text-white text-xs">👍</span>
                        </span>
                        <?php echo formatNumber($post['likes_count']); ?>
                    </span>
                </div>
                <div class="flex items-center space-x-4">
                    <span><?php echo formatNumber($post['comments_count']); ?> comentarios</span>
                    <span><?php echo formatNumber($post['reposts_count']); ?> reposts</span>
                </div>
            </div>

            <!-- Actions CON NÚMEROS INLINE -->
            <div class="flex items-center justify-around pt-2">
                <!-- Like -->
                <button onclick="toggleLike(<?php echo $post['id']; ?>)" 
                        id="like-btn-<?php echo $post['id']; ?>"
                        class="flex-1 flex items-center justify-center space-x-2 text-gray-600 hover:bg-red-50 hover:text-red-500 py-2 rounded-lg smooth-transition <?php echo $has_liked ? 'text-red-500' : ''; ?>">
                    <svg class="w-6 h-6 <?php echo $has_liked ? 'fill-current text-red-500' : ''; ?>" 
                         fill="<?php echo $has_liked ? 'currentColor' : 'none'; ?>" 
                         stroke="currentColor" 
                         stroke-width="2"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                    <span class="font-medium like-number"><?php echo formatNumber($post['likes_count']); ?></span>
                </button>

                <!-- Comment -->
                <button onclick="toggleComments(<?php echo $post['id']; ?>)" 
                        id="comment-btn-<?php echo $post['id']; ?>"
                        class="flex-1 flex items-center justify-center space-x-2 text-gray-600 hover:bg-blue-50 hover:text-blue-500 py-2 rounded-lg smooth-transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <span class="font-medium comment-number"><?php echo formatNumber($post['comments_count']); ?></span>
                </button>

                <!-- Repost -->
                <button onclick="openRepostModal(<?php echo $post['id']; ?>)" 
                        id="repost-btn-<?php echo $post['id']; ?>"
                        class="flex-1 flex items-center justify-center space-x-2 text-gray-600 hover:bg-green-50 hover:text-green-500 py-2 rounded-lg smooth-transition <?php echo $has_reposted ? 'text-green-500' : ''; ?>">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M7 7h10v3l4-4-4-4v3H5v6h2V7zm10 10H7v-3l-4 4 4 4v-3h12v-6h-2v4z"/>
                    </svg>
                    <span class="font-medium repost-number"><?php echo formatNumber($post['reposts_count']); ?></span>
                </button>

                <!-- Share -->
                <button onclick="sharePost(<?php echo $post['id']; ?>)"
                        class="flex-1 flex items-center justify-center space-x-2 text-gray-600 hover:bg-purple-50 hover:text-purple-500 py-2 rounded-lg smooth-transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                    </svg>
                </button>
            </div>

            <!-- Comments Section -->
            <div id="comments-<?php echo $post['id']; ?>" class="hidden mt-4 pt-4 border-t border-gray-100">
                <!-- Comment Form -->
                <form onsubmit="addComment(event, <?php echo $post['id']; ?>)" class="flex items-start space-x-2 mb-4">
                    <img src="uploads/profiles/<?php echo sanitize($current_user['profile_pic']); ?>" 
                         alt="You" 
                         class="w-9 h-9 rounded-full object-cover">
                    <div class="flex-1 relative">
                        <input type="text" 
                               id="comment-input-<?php echo $post['id']; ?>"
                               placeholder="Escribe un comentario..." 
                               required
                               class="w-full px-4 py-3 pr-12 bg-gray-100 rounded-full focus:ring-2 focus:ring-blue-500 focus:bg-white border border-transparent focus:border-blue-500 outline-none smooth-transition">
                        <button type="submit" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-blue-500 hover:text-blue-600">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                            </svg>
                        </button>
                    </div>
                </form>

                <!-- Comments List -->
                <div id="comments-list-<?php echo $post['id']; ?>" class="space-y-4 max-h-96 overflow-y-auto">
                    <?php 
                    $comments = getComments($conn, $post['id']);
                    foreach ($comments as $comment): 
                        $comment_liked = hasLikedComment($conn, $comment['id'], $current_user['id']);
                        $replies = getCommentReplies($conn, $comment['id']);
                    ?>
                        <div class="flex items-start space-x-2">
                            <a href="profile.php?user=<?php echo sanitize($comment['username']); ?>">
                                <img src="uploads/profiles/<?php echo sanitize($comment['profile_pic']); ?>" 
                                     alt="<?php echo sanitize($comment['username']); ?>"
                                     class="w-9 h-9 rounded-full object-cover">
                            </a>
                            <div class="flex-1">
                                <div class="bg-gray-100 rounded-2xl px-4 py-2">
                                    <a href="profile.php?user=<?php echo sanitize($comment['username']); ?>" 
                                       class="font-semibold text-sm hover:underline">
                                        <?php echo sanitize($comment['full_name'] ?? $comment['username']); ?>
                                    </a>
                                    <p class="text-sm text-gray-800 post-content"><?php echo linkify($comment['content']); ?></p>
                                </div>
                                <div class="flex items-center space-x-4 mt-1 px-4 text-xs text-gray-500">
                                    <span><?php echo timeAgo($comment['created_at']); ?></span>
                                    <button onclick="toggleCommentLike(<?php echo $comment['id']; ?>)"
                                            id="comment-like-<?php echo $comment['id']; ?>"
                                            class="font-semibold hover:underline <?php echo $comment_liked ? 'text-red-500' : ''; ?>">
                                        Me gusta (<?php echo formatNumber($comment['likes_count']); ?>)
                                    </button>
                                    <button onclick="replyToComment(<?php echo $post['id']; ?>, <?php echo $comment['id']; ?>, '<?php echo sanitize($comment['username']); ?>')"
                                            class="font-semibold hover:underline">
                                        Responder
                                    </button>
                                </div>
                                
                                <!-- Respuestas -->
                                <?php if (!empty($replies)): ?>
                                    <div class="mt-3 space-y-3 ml-4 border-l-2 border-gray-200 pl-3">
                                        <?php foreach ($replies as $reply): ?>
                                            <div class="flex items-start space-x-2">
                                                <a href="profile.php?user=<?php echo sanitize($reply['username']); ?>">
                                                    <img src="uploads/profiles/<?php echo sanitize($reply['profile_pic']); ?>" 
                                                         class="w-7 h-7 rounded-full object-cover">
                                                </a>
                                                <div class="flex-1">
                                                    <div class="bg-gray-100 rounded-2xl px-3 py-2">
                                                        <a href="profile.php?user=<?php echo sanitize($reply['username']); ?>" 
                                                           class="font-semibold text-xs hover:underline">
                                                            <?php echo sanitize($reply['full_name'] ?? $reply['username']); ?>
                                                        </a>
                                                        <p class="text-xs text-gray-800 post-content"><?php echo linkify($reply['content']); ?></p>
                                                    </div>
                                                    <span class="text-xs text-gray-500 ml-3"><?php echo timeAgo($reply['created_at']); ?></span>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    
    <?php
}
?>