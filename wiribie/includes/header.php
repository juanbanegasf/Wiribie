<?php
// includes/header.php - VERSION 2.1
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Wiribie'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        * { 
            font-family: 'Inter', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }
        
        /* Media container con ratio 3:2 */
        .media-container {
            position: relative;
            width: 100%;
            padding-bottom: 66.67%;
            background: #000;
            border-radius: 0.5rem;
            overflow: hidden;
        }
        
        .media-container img,
        .media-container video {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            max-width: 100%;
            max-height: 100%;
            width: auto;
            height: auto;
            object-fit: contain;
        }

        /* OCULTAR COMPLETAMENTE CONTROLES DE PLAY */
        video::-webkit-media-controls-start-playback-button {
            display: none !important;
            -webkit-appearance: none;
        }

        video::-webkit-media-controls-play-button {
            display: none !important;
        }

        video::-webkit-media-controls-overlay-play-button {
            display: none !important;
        }

        /* Firefox */
        video::-moz-media-controls-play-button {
            display: none !important;
        }

        /* Ocultar overlay de play personalizado */
        .media-container .absolute.pointer-events-none {
            display: none !important;
        }

        /* Mostrar controles solo al hover en desktop */
        .media-container video {
            cursor: pointer;
        }

        .media-container:hover video::-webkit-media-controls {
            opacity: 1;
        }

        /* Smooth transitions */
        .smooth-transition {
            transition: all 0.2s ease;
        }

        .hover-scale:hover {
            transform: scale(1.02);
        }

        /* Animaciones de botones */
        button {
            transition: transform 0.2s ease, background-color 0.2s ease, color 0.2s ease;
        }

        button:active {
            transform: scale(0.95);
        }

        /* Links en contenido */
        .post-content a {
            word-break: break-word;
            transition: color 0.2s ease;
        }

        .post-content a:hover {
            text-decoration: underline;
        }

        /* Scrollbar personalizado */
        ::-webkit-scrollbar { 
            width: 8px; 
            height: 8px;
        }
        
        ::-webkit-scrollbar-track { 
            background: #f1f1f1; 
        }
        
        ::-webkit-scrollbar-thumb { 
            background: #888; 
            border-radius: 4px; 
        }
        
        ::-webkit-scrollbar-thumb:hover { 
            background: #555; 
        }

        /* Animaciones de entrada */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.3s ease;
        }

        /* Loading spinner */
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .animate-spin {
            animation: spin 1s linear infinite;
        }

        /* Truncate text */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Pulsating animation for notifications */
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: .5;
            }
        }

        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        /* Focus visible para accesibilidad */
        *:focus-visible {
            outline: 2px solid #3b82f6;
            outline-offset: 2px;
        }

        /* Prevenir selección de texto en botones */
        button, .prevent-select {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
    </style>
</head>
<body class="bg-gray-50">