// assets/js/video-autoplay.js - VERSION 2.1 SIN OVERLAY DE PLAY

document.addEventListener('DOMContentLoaded', function() {
    const videos = document.querySelectorAll('.video-player');
    
    if (videos.length === 0) return;
    
    // Configuración del observer
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.6 // 60% del video debe ser visible
    };
    
    let currentPlayingVideo = null;
    
    const observerCallback = (entries) => {
        entries.forEach(entry => {
            const video = entry.target;
            
            if (entry.isIntersecting) {
                // Video está en viewport
                if (currentPlayingVideo && currentPlayingVideo !== video) {
                    currentPlayingVideo.pause();
                    currentPlayingVideo.currentTime = 0; // Reset video anterior
                }
                
                // Intentar reproducir
                const playPromise = video.play();
                
                if (playPromise !== undefined) {
                    playPromise.then(() => {
                        currentPlayingVideo = video;
                    }).catch(err => {
                        console.log('Autoplay prevented:', err.name);
                        // El navegador bloqueó autoplay
                    });
                }
                
            } else {
                // Video salió del viewport
                video.pause();
                if (currentPlayingVideo === video) {
                    currentPlayingVideo = null;
                }
            }
        });
    };
    
    const observer = new IntersectionObserver(observerCallback, observerOptions);
    
    // Observar todos los videos
    videos.forEach(video => {
        observer.observe(video);
        
        // Click para pausar/reproducir manualmente
        video.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevenir apertura de fullscreen
            
            if (video.paused) {
                // Pausar otros videos primero
                videos.forEach(v => {
                    if (v !== video && !v.paused) {
                        v.pause();
                    }
                });
                
                video.play().then(() => {
                    currentPlayingVideo = video;
                }).catch(err => console.log(err));
            } else {
                video.pause();
                if (currentPlayingVideo === video) {
                    currentPlayingVideo = null;
                }
            }
        });
        
        // Prevenir que se muestre el botón de play nativo
        video.addEventListener('loadedmetadata', function() {
            video.removeAttribute('controls');
        });
    });
    
    // Pausar todos los videos cuando la página se oculta
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            videos.forEach(video => {
                if (!video.paused) {
                    video.pause();
                }
            });
            currentPlayingVideo = null;
        }
    });
});

console.log('🎬 Video Autoplay cargado correctamente');