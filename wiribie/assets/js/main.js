// assets/js/main.js - VERSIÓN COMPLETA CON AJAX

let currentRepostId = null;
let currentReportId = null;

// Toggle Like - AJAX COMPLETO SIN RECARGA
function toggleLike(postId) {
    const btn = document.getElementById('like-btn-' + postId);
    const originalState = btn.innerHTML;
    
    fetch('actions/like_action.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'post_id=' + postId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const svg = btn.querySelector('svg');
            const numberSpan = btn.querySelector('.like-number');
            
            if (data.liked) {
                svg.classList.add('fill-current', 'text-red-500');
                svg.setAttribute('fill', 'currentColor');
                btn.classList.add('text-red-500');
            } else {
                svg.classList.remove('fill-current', 'text-red-500');
                svg.setAttribute('fill', 'none');
                btn.classList.remove('text-red-500');
            }
            
            // Actualizar número inline
            if (numberSpan) {
                numberSpan.textContent = formatNumber(data.count);
            }
            
            // Actualizar stats section
            updateStatsSection(postId, data.count, null, null);
            
            // Animación de feedback
            btn.style.transform = 'scale(1.2)';
            setTimeout(() => btn.style.transform = 'scale(1)', 200);
            
        } else {
            showNotification(data.message || 'Error al dar like', 'error');
            btn.innerHTML = originalState;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error de conexión', 'error');
        btn.innerHTML = originalState;
    });
}

// Toggle Comments
function toggleComments(postId) {
    const commentsDiv = document.getElementById('comments-' + postId);
    commentsDiv.classList.toggle('hidden');
    
    // Si se abre, hacer focus en el input
    if (!commentsDiv.classList.contains('hidden')) {
        const input = document.getElementById('comment-input-' + postId);
        setTimeout(() => input.focus(), 100);
    }
}

// Add Comment - SIN RECARGA
function addComment(event, postId) {
    event.preventDefault();
    const input = document.getElementById('comment-input-' + postId);
    const content = input.value.trim();
    
    if (!content) return;
    
    const parentId = input.dataset.parentId || null;
    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalBtnContent = submitBtn.innerHTML;
    
    // Mostrar loading
    submitBtn.innerHTML = '<svg class="animate-spin w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
    submitBtn.disabled = true;
    
    fetch('actions/comment_action.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'post_id=' + postId + '&content=' + encodeURIComponent(content) + (parentId ? '&parent_id=' + parentId : '')
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            input.value = '';
            delete input.dataset.parentId;
            
            // Actualizar contador de comentarios
            const commentBtn = document.querySelector(`#comment-btn-${postId} .comment-number`);
            if (commentBtn) {
                const currentCount = parseInt(commentBtn.textContent) || 0;
                commentBtn.textContent = formatNumber(currentCount + 1);
            }
            
            // Actualizar stats section
            updateStatsSection(postId, null, data.new_count || currentCount + 1, null);
            
            // Agregar comentario dinámicamente
            if (data.comment_html) {
                const commentsList = document.getElementById('comments-list-' + postId);
                commentsList.insertAdjacentHTML('beforeend', data.comment_html);
            } else {
                // Fallback: recargar solo los comentarios
                loadComments(postId);
            }
            
            showNotification('Comentario publicado', 'success');
        } else {
            showNotification(data.message || 'Error al comentar', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error de conexión', 'error');
    })
    .finally(() => {
        submitBtn.innerHTML = originalBtnContent;
        submitBtn.disabled = false;
    });
}

// Load Comments dynamically
function loadComments(postId) {
    fetch(`actions/get_comments.php?post_id=${postId}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const commentsList = document.getElementById('comments-list-' + postId);
            commentsList.innerHTML = data.html;
        }
    })
    .catch(error => console.error('Error:', error));
}

// Reply to Comment
function replyToComment(postId, commentId, username) {
    const input = document.getElementById('comment-input-' + postId);
    input.value = '@' + username + ' ';
    input.focus();
    input.dataset.parentId = commentId;
    input.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

// Toggle Comment Like - SIN RECARGA
function toggleCommentLike(commentId) {
    const btn = document.getElementById('comment-like-' + commentId);
    const originalText = btn.textContent;
    
    fetch('actions/comment_like_action.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'comment_id=' + commentId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            btn.classList.toggle('text-red-500');
            btn.textContent = data.liked ? `Me gusta (${formatNumber(data.count)})` : `Me gusta (${formatNumber(data.count)})`;
            
            // Animación
            btn.style.transform = 'scale(1.1)';
            setTimeout(() => btn.style.transform = 'scale(1)', 200);
        } else {
            showNotification(data.message || 'Error', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error de conexión', 'error');
    });
}

// Open Repost Modal
function openRepostModal(postId) {
    currentRepostId = postId;
    document.getElementById('repostModal').classList.remove('hidden');
    document.getElementById('repostText').value = '';
    document.getElementById('repostText').focus();
}

// Close Repost Modal
function closeRepostModal() {
    document.getElementById('repostModal').classList.add('hidden');
    currentRepostId = null;
}

// Confirm Repost - SIN RECARGA
// Confirm Repost - CORREGIDO
function confirmRepost() {
    if (!currentRepostId) return;
    
    const text = document.getElementById('repostText').value.trim();
    const confirmBtn = document.querySelector('#repostModal button[onclick="confirmRepost()"]');
    const originalText = confirmBtn.innerHTML;
    
    confirmBtn.innerHTML = '<svg class="animate-spin w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Reposteando...';
    confirmBtn.disabled = true;
    
    const formData = new URLSearchParams();
    formData.append('post_id', currentRepostId);
    formData.append('repost_text', text);
    
    fetch('actions/repost_action.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: formData.toString()
    })
    .then(response => {
        // Primero verificar el status
        if (!response.ok && response.status !== 200) {
            throw new Error('HTTP error ' + response.status);
        }
        
        // Intentar parsear JSON
        return response.text().then(text => {
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('Error parsing JSON:', text);
                throw new Error('Respuesta inválida del servidor');
            }
        });
    })
    .then(data => {
        console.log('Repost response:', data);
        
        if (data.success) {
            closeRepostModal();
            
            const btn = document.getElementById('repost-btn-' + currentRepostId);
            const numberSpan = btn ? btn.querySelector('.repost-number') : null;
            
            if (data.reposted) {
                if (btn) btn.classList.add('text-green-500');
                showNotification('¡Publicación reposteada!', 'success');
            } else {
                if (btn) btn.classList.remove('text-green-500');
                showNotification('Repost eliminado', 'info');
            }
            
            // Actualizar número
            if (numberSpan) {
                numberSpan.textContent = formatNumber(data.count);
            }
            
            // Actualizar stats section
            updateStatsSection(currentRepostId, null, null, data.count);
            
            // Animación
            if (btn) {
                btn.style.transform = 'scale(1.2)';
                setTimeout(() => btn.style.transform = 'scale(1)', 200);
            }
            
        } else {
            showNotification(data.message || 'Error al repostear', 'error');
        }
    })
    .catch(error => {
        console.error('Repost error completo:', error);
        showNotification('Error de conexión. Intenta de nuevo.', 'error');
    })
    .finally(() => {
        confirmBtn.innerHTML = originalText;
        confirmBtn.disabled = false;
    });
}

// Follow/Unfollow - SIN RECARGA
function toggleFollow(userId) {
    const btn = document.getElementById('follow-btn');
    const originalHTML = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
    
    fetch('actions/follow_action.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'user_id=' + userId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Actualizar botón
            if (data.following) {
                btn.className = 'flex-1 md:flex-none inline-flex items-center justify-center px-6 py-3 font-semibold rounded-xl smooth-transition shadow-lg bg-gray-200 hover:bg-gray-300 text-gray-700';
                btn.innerHTML = `<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Siguiendo`;
                showNotification('Ahora sigues a este usuario', 'success');
            } else {
                btn.className = 'flex-1 md:flex-none inline-flex items-center justify-center px-6 py-3 font-semibold rounded-xl smooth-transition shadow-lg bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white';
                btn.innerHTML = `<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>Seguir`;
                showNotification('Dejaste de seguir', 'info');
            }
            
            // Actualizar contadores si existen
            if (data.followers_count !== undefined) {
                const followersEl = document.querySelector('.followers-count');
                if (followersEl) {
                    followersEl.textContent = formatNumber(data.followers_count);
                }
            }
            
        } else {
            showNotification(data.message || 'Error al seguir', 'error');
            btn.innerHTML = originalHTML;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error de conexión', 'error');
        btn.innerHTML = originalHTML;
    })
    .finally(() => {
        btn.disabled = false;
    });
}

// Update Stats Section
function updateStatsSection(postId, likes, comments, reposts) {
    const postElement = document.querySelector(`[data-post-id="${postId}"]`);
    if (!postElement) return;
    
    const statsSection = postElement.querySelector('.flex.items-center.justify-between.py-2');
    if (!statsSection) return;
    
    if (likes !== null && likes !== undefined) {
        const likesSpan = statsSection.querySelector('.flex.items-center span');
        if (likesSpan) likesSpan.textContent = formatNumber(likes);
    }
    
    if (comments !== null && comments !== undefined) {
        const spans = statsSection.querySelectorAll('span');
        if (spans[1]) spans[1].textContent = formatNumber(comments) + ' comentarios';
    }
    
    if (reposts !== null && reposts !== undefined) {
        const spans = statsSection.querySelectorAll('span');
        if (spans[2]) spans[2].textContent = formatNumber(reposts) + ' reposts';
    }
}

// Share Post
function sharePost(postId) {
    const url = window.location.origin + window.location.pathname.replace(/\/[^\/]*$/, '') + '/post.php?id=' + postId;
    
    if (navigator.share) {
        navigator.share({
            title: 'Publicación en Wiribie',
            url: url
        }).catch(err => console.log('Error sharing:', err));
    } else {
        navigator.clipboard.writeText(url).then(() => {
            showNotification('¡Enlace copiado!', 'success');
        }).catch(() => {
            showNotification('No se pudo copiar', 'error');
        });
    }
}

// Toggle Post Menu
function togglePostMenu(postId) {
    const menu = document.getElementById('post-menu-' + postId);
    document.querySelectorAll('[id^="post-menu-"]').forEach(m => {
        if (m.id !== 'post-menu-' + postId) m.classList.add('hidden');
    });
    menu.classList.toggle('hidden');
}

// Close menus on outside click
document.addEventListener('click', function(e) {
    if (!e.target.closest('[id^="post-menu-"]') && !e.target.closest('button[onclick^="togglePostMenu"]')) {
        document.querySelectorAll('[id^="post-menu-"]').forEach(m => m.classList.add('hidden'));
    }
});

// Edit Post
function editPost(postId) {
    showNotification('Función de edición próximamente', 'info');
    togglePostMenu(postId);
}

// Delete Post - CON ANIMACIÓN
function deletePost(postId) {
    if (!confirm('¿Estás seguro de eliminar esta publicación? Esta acción no se puede deshacer.')) return;
    
    const postElement = document.querySelector(`[data-post-id="${postId}"]`);
    
    fetch('actions/delete_post_action.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'post_id=' + postId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Publicación eliminada', 'success');
            if (postElement) {
                postElement.style.transition = 'all 0.3s ease';
                postElement.style.opacity = '0';
                postElement.style.transform = 'scale(0.9)';
                setTimeout(() => postElement.remove(), 300);
            }
        } else {
            showNotification(data.message || 'Error al eliminar', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error de conexión', 'error');
    });
}

// Delete Repost - NUEVO
function deleteRepost(postId) {
    if (!confirm('¿Eliminar este repost? La publicación original no se afectará.')) return;
    
    const postElement = document.querySelector(`[data-post-id="${postId}"]`);
    
    fetch('actions/delete_repost_action.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'post_id=' + postId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Repost eliminado', 'success');
            if (postElement) {
                postElement.style.transition = 'all 0.3s ease';
                postElement.style.opacity = '0';
                postElement.style.transform = 'scale(0.9)';
                setTimeout(() => postElement.remove(), 300);
            }
        } else {
            showNotification(data.message || 'Error al eliminar', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error de conexión', 'error');
    });
}

// Report Post
function reportPost(postId) {
    currentReportId = postId;
    document.getElementById('reportModal').classList.remove('hidden');
    document.getElementById('reportReason').value = '';
    document.getElementById('reportReason').focus();
    togglePostMenu(postId);
}

// Close Report Modal
function closeReportModal() {
    document.getElementById('reportModal').classList.add('hidden');
    currentReportId = null;
}

// Confirm Report
function confirmReport() {
    if (!currentReportId) return;
    
    const reason = document.getElementById('reportReason').value.trim();
    if (!reason) {
        showNotification('Por favor describe el motivo', 'error');
        return;
    }
    
    fetch('actions/report_action.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'post_id=' + currentReportId + '&reason=' + encodeURIComponent(reason)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeReportModal();
            showNotification('Reporte enviado. Gracias.', 'success');
        } else {
            showNotification(data.message || 'Error', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error de conexión', 'error');
    });
}

// Open Fullscreen
function openFullscreen(postId) {
    fetch('actions/get_post_action.php?id=' + postId)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const post = data.post;
            const modal = document.getElementById('fullscreenModal');
            const content = document.getElementById('fullscreenContent');
            
            let mediaHtml = '';
            if (post.media_type === 'video') {
                mediaHtml = `<video controls autoplay class="max-h-full max-w-full">
                    <source src="uploads/posts/${post.media_url}" type="video/mp4">
                </video>`;
            } else if (post.media_type === 'image' || post.media_type === 'gif') {
                mediaHtml = `<img src="uploads/posts/${post.media_url}" class="max-h-full max-w-full object-contain">`;
            }
            
            content.innerHTML = mediaHtml;
            document.getElementById('fs-likes').textContent = formatNumber(post.likes_count);
            document.getElementById('fs-comments').textContent = formatNumber(post.comments_count);
            document.getElementById('fs-reposts').textContent = formatNumber(post.reposts_count);
            
            modal.classList.remove('hidden');
        }
    })
    .catch(error => console.error('Error:', error));
}

// Close Fullscreen
function closeFullscreen() {
    document.getElementById('fullscreenModal').classList.add('hidden');
}

// Preview Media
function previewMedia(input) {
    const preview = document.getElementById('mediaPreview');
    const imagePreview = document.getElementById('imagePreview');
    const videoPreview = document.getElementById('videoPreview');
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();
        
        reader.onload = function(e) {
            if (file.type.startsWith('video/')) {
                videoPreview.src = e.target.result;
                videoPreview.classList.remove('hidden');
                imagePreview.classList.add('hidden');
            } else {
                imagePreview.src = e.target.result;
                imagePreview.classList.remove('hidden');
                videoPreview.classList.add('hidden');
            }
            preview.classList.remove('hidden');
        }
        reader.readAsDataURL(file);
    } else {
        preview.classList.add('hidden');
    }
}

// Format Numbers
function formatNumber(num) {
    num = parseInt(num);
    if (isNaN(num)) return '0';
    if (num >= 1000000) {
        const millions = num / 1000000;
        return (millions % 1 === 0) ? millions + 'M' : millions.toFixed(1) + 'M';
    }
    if (num >= 1000) {
        const thousands = num / 1000;
        return (thousands % 1 === 0) ? thousands + 'K' : thousands.toFixed(1) + 'K';
    }
    return num.toLocaleString();
}

// Show Notification
function showNotification(message, type = 'info') {
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        info: 'bg-blue-500',
        warning: 'bg-yellow-500'
    };
    
    const icons = {
        success: '✓',
        error: '✕',
        info: 'ℹ',
        warning: '⚠'
    };
    
    const notification = document.createElement('div');
    notification.className = `fixed top-20 right-4 ${colors[type]} text-white px-6 py-4 rounded-xl shadow-2xl z-50 transform transition-all duration-300 translate-x-full flex items-center space-x-3 max-w-sm`;
    notification.innerHTML = `
        <span class="text-2xl">${icons[type]}</span>
        <span class="font-medium">${message}</span>
    `;
    
    document.body.appendChild(notification);
    setTimeout(() => notification.classList.remove('translate-x-full'), 100);
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Mark notifications as read
function markNotificationsRead() {
    fetch('actions/mark_notifications_read.php', { method: 'POST' })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const badge = document.getElementById('notifications-badge');
            if (badge) badge.classList.add('hidden');
        }
    });
}

// Toggle expand/collapse text
function toggleText(element) {
    const fullText = element.previousElementSibling;
    const shortText = fullText.previousElementSibling;
    
    if (fullText.classList.contains('hidden')) {
        fullText.classList.remove('hidden');
        shortText.classList.add('hidden');
        element.textContent = 'ver menos';
    } else {
        fullText.classList.add('hidden');
        shortText.classList.remove('hidden');
        element.textContent = 'ver más';
    }
}

// ESC to close modals
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeRepostModal();
        closeReportModal();
        closeFullscreen();
        const postModal = document.getElementById('postModal');
        if (postModal && !postModal.classList.contains('hidden')) {
            postModal.classList.add('hidden');
        }
    }
});

console.log('🚀 Wiribie v2.0 - Loaded');

// Toggle expand/collapse text - CORREGIDO
function toggleText(button) {
    const container = button.closest('.post-text-container');
    if (!container) return;
    
    const shortDiv = container.querySelector('.post-text-short');
    const fullDiv = container.querySelector('.post-text-full');
    
    if (shortDiv && fullDiv) {
        shortDiv.classList.toggle('hidden');
        fullDiv.classList.toggle('hidden');
    }
}