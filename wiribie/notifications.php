<?php
// notifications.php
require_once 'includes/functions.php';
requireLogin();

$database = new Database();
$conn = $database->getConnection();
$current_user = getCurrentUser($conn);
$notifications = getNotifications($conn, $current_user['id']);

// Marcar todas como leídas
$stmt = $conn->prepare("UPDATE notifications SET is_read = TRUE WHERE user_id = ?");
$stmt->execute([$current_user['id']]);

$pageTitle = "Notificaciones - Wiribie";
include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="max-w-2xl mx-auto px-4 py-6 pb-24 md:pb-6">
    
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-800">Notificaciones</h2>
        </div>

        <?php if (empty($notifications)): ?>
            <div class="p-12 text-center">
                <svg class="w-20 h-20 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">No tienes notificaciones</h3>
                <p class="text-gray-500">Te avisaremos cuando haya algo nuevo</p>
            </div>
        <?php else: ?>
            <div class="divide-y divide-gray-100">
                <?php foreach ($notifications as $notif): 
                    $icon = '';
                    $text = '';
                    $color = '';
                    
                    switch ($notif['type']) {
                        case 'like':
                            $icon = '<svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>';
                            $text = 'le dio me gusta a tu publicación';
                            $color = 'bg-red-50';
                            break;
                        case 'comment':
                            $icon = '<svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>';
                            $text = 'comentó tu publicación';
                            $color = 'bg-blue-50';
                            break;
                        case 'repost':
                            $icon = '<svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 24 24"><path d="M7 7h10v3l4-4-4-4v3H5v6h2V7zm10 10H7v-3l-4 4 4 4v-3h12v-6h-2v4z"/></svg>';
                            $text = 'reposteó tu publicación';
                            $color = 'bg-green-50';
                            break;
                        case 'follow':
                            $icon = '<svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>';
                            $text = 'comenzó a seguirte';
                            $color = 'bg-purple-50';
                            break;
                        case 'comment_reply':
                            $icon = '<svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>';
                            $text = 'respondió a tu comentario';
                            $color = 'bg-indigo-50';
                            break;
                    }
                ?>
                    <div class="p-4 hover:bg-gray-50 smooth-transition <?php echo !$notif['is_read'] ? $color : ''; ?>">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <?php echo $icon; ?>
                            </div>
                            <a href="profile.php?user=<?php echo sanitize($notif['username']); ?>" class="flex-shrink-0">
                                <img src="uploads/profiles/<?php echo sanitize($notif['profile_pic']); ?>" 
                                     class="w-12 h-12 rounded-full object-cover">
                            </a>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-800">
                                    <a href="profile.php?user=<?php echo sanitize($notif['username']); ?>" 
                                       class="font-semibold hover:underline">
                                        <?php echo sanitize($notif['full_name'] ?? $notif['username']); ?>
                                    </a>
                                    <span class="text-gray-600"> <?php echo $text; ?></span>
                                </p>
                                <p class="text-xs text-gray-500 mt-1"><?php echo timeAgo($notif['created_at']); ?></p>
                                <?php if ($notif['post_content'] && $notif['type'] != 'follow'): ?>
                                    <div class="mt-2 p-2 bg-gray-100 rounded-lg">
                                        <p class="text-xs text-gray-600 line-clamp-2"><?php echo sanitize($notif['post_content']); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php if ($notif['post_media'] && $notif['type'] != 'follow'): ?>
                                <div class="flex-shrink-0">
                                    <img src="uploads/posts/<?php echo sanitize($notif['post_media']); ?>" 
                                         class="w-16 h-16 rounded-lg object-cover">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="assets/js/main.js"></script>
</body>
</html>