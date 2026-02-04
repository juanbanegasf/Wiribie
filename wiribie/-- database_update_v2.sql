-- database_update_v2.sql
-- EJECUTAR EN PHPMYADMIN DESPUÉS DE LA BD ORIGINAL

USE socialhub;

-- Agregar campos de conteo a la tabla posts
ALTER TABLE posts 
ADD COLUMN likes_count INT DEFAULT 0,
ADD COLUMN comments_count INT DEFAULT 0,
ADD COLUMN reposts_count INT DEFAULT 0;

-- Agregar campo de conteo a la tabla users
ALTER TABLE users 
ADD COLUMN followers_count INT DEFAULT 0,
ADD COLUMN following_count INT DEFAULT 0;

-- Tabla de seguidores
CREATE TABLE IF NOT EXISTS follows (
    id INT PRIMARY KEY AUTO_INCREMENT,
    follower_id INT NOT NULL,
    following_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (follower_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (following_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_follow (follower_id, following_id),
    INDEX idx_follower (follower_id),
    INDEX idx_following (following_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de notificaciones
CREATE TABLE IF NOT EXISTS notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    from_user_id INT NOT NULL,
    type ENUM('like', 'comment', 'repost', 'follow', 'comment_reply') NOT NULL,
    post_id INT DEFAULT NULL,
    comment_id INT DEFAULT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (from_user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_read (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de reportes
CREATE TABLE IF NOT EXISTS reports (
    id INT PRIMARY KEY AUTO_INCREMENT,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    reason TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_post (post_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Agregar campo de texto al repost
ALTER TABLE reposts ADD COLUMN repost_text TEXT DEFAULT NULL;

-- Agregar likes a comentarios
CREATE TABLE IF NOT EXISTS comment_likes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    comment_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (comment_id) REFERENCES comments(id) ON DELETE CASCADE,
    UNIQUE KEY unique_comment_like (user_id, comment_id),
    INDEX idx_comment (comment_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Agregar likes_count a comentarios
ALTER TABLE comments ADD COLUMN likes_count INT DEFAULT 0;

-- Agregar parent_id para respuestas a comentarios
ALTER TABLE comments ADD COLUMN parent_id INT DEFAULT NULL,
ADD FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE CASCADE;

-- TRIGGERS para mantener conteos actualizados

-- Trigger para likes
DELIMITER $$
CREATE TRIGGER after_like_insert AFTER INSERT ON likes
FOR EACH ROW BEGIN
    UPDATE posts SET likes_count = likes_count + 1 WHERE id = NEW.post_id;
END$$

CREATE TRIGGER after_like_delete AFTER DELETE ON likes
FOR EACH ROW BEGIN
    UPDATE posts SET likes_count = likes_count - 1 WHERE id = OLD.post_id;
END$$

-- Trigger para comentarios
CREATE TRIGGER after_comment_insert AFTER INSERT ON comments
FOR EACH ROW BEGIN
    UPDATE posts SET comments_count = comments_count + 1 WHERE id = NEW.post_id;
END$$

CREATE TRIGGER after_comment_delete AFTER DELETE ON comments
FOR EACH ROW BEGIN
    UPDATE posts SET comments_count = comments_count - 1 WHERE id = OLD.post_id;
END$$

-- Trigger para reposts
CREATE TRIGGER after_repost_insert AFTER INSERT ON reposts
FOR EACH ROW BEGIN
    UPDATE posts SET reposts_count = reposts_count + 1 WHERE id = NEW.post_id;
END$$

CREATE TRIGGER after_repost_delete AFTER DELETE ON reposts
FOR EACH ROW BEGIN
    UPDATE posts SET reposts_count = reposts_count - 1 WHERE id = OLD.post_id;
END$$

-- Trigger para seguidores
CREATE TRIGGER after_follow_insert AFTER INSERT ON follows
FOR EACH ROW BEGIN
    UPDATE users SET followers_count = followers_count + 1 WHERE id = NEW.following_id;
    UPDATE users SET following_count = following_count + 1 WHERE id = NEW.follower_id;
END$$

CREATE TRIGGER after_follow_delete AFTER DELETE ON follows
FOR EACH ROW BEGIN
    UPDATE users SET followers_count = followers_count - 1 WHERE id = OLD.following_id;
    UPDATE users SET following_count = following_count - 1 WHERE id = OLD.follower_id;
END$$

-- Trigger para likes en comentarios
CREATE TRIGGER after_comment_like_insert AFTER INSERT ON comment_likes
FOR EACH ROW BEGIN
    UPDATE comments SET likes_count = likes_count + 1 WHERE id = NEW.comment_id;
END$$

CREATE TRIGGER after_comment_like_delete AFTER DELETE ON comment_likes
FOR EACH ROW BEGIN
    UPDATE comments SET likes_count = likes_count - 1 WHERE id = OLD.comment_id;
END$$

DELIMITER ;

-- Actualizar conteos existentes
UPDATE posts p SET likes_count = (SELECT COUNT(*) FROM likes WHERE post_id = p.id);
UPDATE posts p SET comments_count = (SELECT COUNT(*) FROM comments WHERE post_id = p.id);
UPDATE posts p SET reposts_count = (SELECT COUNT(*) FROM reposts WHERE post_id = p.id);