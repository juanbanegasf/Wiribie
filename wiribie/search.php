<!-- Posts Results -->
<?php if (($filter == 'all' || $filter == 'posts') && !empty($results['posts'])): ?>
    <div class="mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Publicaciones</h2>
        <div class="space-y-4">
            <?php foreach ($results['posts'] as $post): ?>
                <div class="bg-white rounded-2xl shadow-sm p-4 hover:shadow-md smooth-transition">
                    <div class="flex items-start space-x-3 mb-3">
                        <a href="profile.php?user=<?php echo sanitize($post['username']); ?>">
                            <img src="uploads/profiles/<?php echo sanitize($post['profile_pic']); ?>" 
                                 class="w-10 h-10 rounded-full object-cover">
                        </a>
                        <div class="flex-1">
                            <a href="profile.php?user=<?php echo sanitize($post['username']); ?>" 
                               class="font-semibold hover:underline">
                                <?php echo sanitize($post['full_name'] ?? $post['username']); ?>
                            </a>
                            <p class="text-sm text-gray-500">@<?php echo sanitize($post['username']); ?> · <?php echo timeAgo($post['created_at']); ?></p>
                        </div>
                    </div>
                    <!-- Usar linkify aquí -->
                    <div class="text-gray-800 mb-2 post-content"><?php echo linkify($post['content']); ?></div>
                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                        <span>❤️ <?php echo formatNumber($post['likes_count']); ?></span>
                        <span>💬 <?php echo formatNumber($post['comments_count']); ?></span>
                        <span>🔄 <?php echo formatNumber($post['reposts_count']); ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>