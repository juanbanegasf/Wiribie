<?php
// includes/navbar.php - VERSION 2.0
$current_user = getCurrentUser($conn);
$unread_notifications = getUnreadNotificationsCount($conn, $current_user['id']);
?>
<nav class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
    <div class="max-w-6xl mx-auto px-4">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <a href="index.php" class="flex items-center space-x-2 hover:opacity-80 smooth-transition">
                <div class="w-10 h-10 bg-gradient-to-tr from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                    <span class="text-white font-bold text-xl">W</span>
                </div>
                <span class="text-xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent hidden sm:inline">Wiribie</span>
            </a>

            <!-- Search Bar (Desktop) -->
            <div class="hidden md:block flex-1 max-w-md mx-8">
                <form action="search.php" method="GET">
                    <div class="relative">
                        <svg class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" 
                               name="q"
                               placeholder="Buscar en Wiribie..." 
                               class="w-full pl-10 pr-4 py-2 bg-gray-100 rounded-full focus:ring-2 focus:ring-blue-500 focus:bg-white border border-transparent focus:border-blue-500 outline-none smooth-transition">
                    </div>
                </form>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center space-x-2">
                <a href="index.php" class="flex flex-col items-center text-gray-700 hover:text-blue-500 hover:bg-blue-50 rounded-xl px-4 py-2 smooth-transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span class="text-xs mt-1">Inicio</span>
                </a>
                
                <a href="notifications.php" class="flex flex-col items-center text-gray-700 hover:text-blue-500 hover:bg-blue-50 rounded-xl px-4 py-2 smooth-transition relative">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <?php if ($unread_notifications > 0): ?>
                        <span class="absolute top-1 right-2 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center animate-pulse">
                            <?php echo $unread_notifications > 9 ? '9+' : $unread_notifications; ?>
                        </span>
                    <?php endif; ?>
                    <span class="text-xs mt-1">Notif.</span>
                </a>
                
                <a href="profile.php?user=<?php echo $current_user['username']; ?>" class="flex items-center space-x-2 hover:bg-gray-100 rounded-full px-3 py-2 smooth-transition">
                    <img src="uploads/profiles/<?php echo sanitize($current_user['profile_pic']); ?>" 
                         alt="Profile" 
                         class="w-9 h-9 rounded-full object-cover ring-2 ring-gray-200">
                    <span class="font-medium text-gray-700">@<?php echo sanitize($current_user['username']); ?></span>
                </a>
            </div>

            <!-- Mobile Search Icon -->
            <div class="md:hidden">
                <a href="search.php" class="text-gray-700 hover:text-blue-500 p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- Mobile Bottom Navigation -->
<nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50 shadow-lg">
    <div class="flex justify-around items-center h-16 px-2">
        <a href="index.php" class="flex flex-col items-center text-gray-700 hover:text-blue-500 flex-1 py-2 smooth-transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span class="text-xs mt-1">Inicio</span>
        </a>
        
        <a href="search.php" class="flex flex-col items-center text-gray-700 hover:text-blue-500 flex-1 py-2 smooth-transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <span class="text-xs mt-1">Buscar</span>
        </a>
        
        <button onclick="document.getElementById('postModal').classList.remove('hidden')" 
                class="flex flex-col items-center text-blue-500 flex-1 py-2 smooth-transition">
            <div class="w-12 h-12 bg-gradient-to-tr from-blue-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg -mt-6">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
        </button>
        
        <a href="notifications.php" class="flex flex-col items-center text-gray-700 hover:text-blue-500 flex-1 py-2 smooth-transition relative">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <?php if ($unread_notifications > 0): ?>
                <span id="notifications-badge" class="absolute top-0 right-4 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center animate-pulse">
                    <?php echo $unread_notifications > 9 ? '9+' : $unread_notifications; ?>
                </span>
            <?php endif; ?>
            <span class="text-xs mt-1">Notif.</span>
        </a>
        
        <a href="profile.php?user=<?php echo $current_user['username']; ?>" 
           class="flex flex-col items-center text-gray-700 hover:text-blue-500 flex-1 py-2 smooth-transition">
            <img src="uploads/profiles/<?php echo sanitize($current_user['profile_pic']); ?>" 
                 class="w-6 h-6 rounded-full object-cover">
            <span class="text-xs mt-1">Perfil</span>
        </a>
    </div>
</nav>