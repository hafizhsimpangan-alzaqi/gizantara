-- CR-03 Additive Schema Reference (lihat migrate_cr03.php untuk eksekusi idempoten)

ALTER TABLE users
    MODIFY COLUMN role ENUM('super_admin', 'admin', 'user') NOT NULL DEFAULT 'user',
    ADD COLUMN status ENUM('active', 'banned') NOT NULL DEFAULT 'active' AFTER role,
    ADD COLUMN banned_at TIMESTAMP NULL DEFAULT NULL AFTER status,
    ADD COLUMN ban_reason VARCHAR(255) NULL DEFAULT NULL AFTER banned_at,
    ADD COLUMN banned_by INT UNSIGNED NULL DEFAULT NULL AFTER ban_reason,
    ADD INDEX idx_users_status (status),
    ADD INDEX idx_users_banned_by (banned_by),
    ADD CONSTRAINT fk_users_banned_by FOREIGN KEY (banned_by) REFERENCES users(id) ON DELETE SET NULL;

ALTER TABLE articles
    ADD COLUMN moderation_status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending' AFTER cover_image,
    ADD COLUMN moderated_at TIMESTAMP NULL DEFAULT NULL AFTER moderation_status,
    ADD COLUMN moderated_by INT UNSIGNED NULL DEFAULT NULL AFTER moderated_at,
    ADD COLUMN rejection_reason VARCHAR(500) NULL DEFAULT NULL AFTER moderated_by,
    ADD INDEX idx_articles_moderation_status (moderation_status),
    ADD INDEX idx_articles_moderated_by (moderated_by),
    ADD CONSTRAINT fk_articles_moderated_by FOREIGN KEY (moderated_by) REFERENCES users(id) ON DELETE SET NULL;

UPDATE articles SET moderation_status = 'approved' WHERE moderated_at IS NULL AND moderation_status = 'pending';

ALTER TABLE foods
    ADD COLUMN moderation_status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending' AFTER source_name,
    ADD COLUMN moderated_at TIMESTAMP NULL DEFAULT NULL AFTER moderation_status,
    ADD COLUMN moderated_by INT UNSIGNED NULL DEFAULT NULL AFTER moderated_at,
    ADD COLUMN rejection_reason VARCHAR(500) NULL DEFAULT NULL AFTER moderated_by,
    ADD INDEX idx_foods_moderation_status (moderation_status),
    ADD INDEX idx_foods_moderated_by (moderated_by),
    ADD CONSTRAINT fk_foods_moderated_by FOREIGN KEY (moderated_by) REFERENCES users(id) ON DELETE SET NULL;

UPDATE foods SET moderation_status = 'approved' WHERE moderated_at IS NULL AND moderation_status = 'pending';

CREATE TABLE IF NOT EXISTS moderation_logs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    content_type ENUM('article', 'food') NOT NULL,
    content_id INT UNSIGNED NOT NULL,
    action ENUM('approved', 'rejected') NOT NULL,
    moderator_id INT UNSIGNED NULL,
    reason VARCHAR(500) NULL DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_moderation_target (content_type, content_id, created_at),
    INDEX idx_moderation_moderator (moderator_id),
    CONSTRAINT fk_moderation_moderator FOREIGN KEY (moderator_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
