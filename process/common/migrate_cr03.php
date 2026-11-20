<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Akses ditolak.');
}

require_once __DIR__ . '/bootstrap.php';

function kolom_ada(mysqli $koneksi, string $tabel, string $kolom): bool
{
    $stmt = mysqli_prepare(
        $koneksi,
        'SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ? LIMIT 1'
    );
    mysqli_stmt_bind_param($stmt, 'ss', $tabel, $kolom);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    $ada = mysqli_stmt_num_rows($stmt) > 0;
    mysqli_stmt_close($stmt);
    return $ada;
}

function indeks_ada(mysqli $koneksi, string $tabel, string $indeks): bool
{
    $stmt = mysqli_prepare(
        $koneksi,
        'SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND INDEX_NAME = ? LIMIT 1'
    );
    mysqli_stmt_bind_param($stmt, 'ss', $tabel, $indeks);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    $ada = mysqli_stmt_num_rows($stmt) > 0;
    mysqli_stmt_close($stmt);
    return $ada;
}

function constraint_ada(mysqli $koneksi, string $tabel, string $constraint): bool
{
    $stmt = mysqli_prepare(
        $koneksi,
        'SELECT 1 FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ? LIMIT 1'
    );
    mysqli_stmt_bind_param($stmt, 'ss', $tabel, $constraint);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    $ada = mysqli_stmt_num_rows($stmt) > 0;
    mysqli_stmt_close($stmt);
    return $ada;
}

$koneksi = koneksi_database();

// 1. Update users.role enum + status/ban columns
$kolom_role = mysqli_query($koneksi, "SHOW COLUMNS FROM users LIKE 'role'");
$info_role = mysqli_fetch_assoc($kolom_role);
if ($info_role && strpos($info_role['Type'], "'super_admin'") === false) {
    mysqli_query($koneksi, "ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'admin', 'user') NOT NULL DEFAULT 'user'");
}
if (!kolom_ada($koneksi, 'users', 'status')) {
    mysqli_query($koneksi, "ALTER TABLE users ADD COLUMN status ENUM('active', 'banned') NOT NULL DEFAULT 'active' AFTER role");
}
if (!kolom_ada($koneksi, 'users', 'banned_at')) {
    mysqli_query($koneksi, 'ALTER TABLE users ADD COLUMN banned_at TIMESTAMP NULL DEFAULT NULL AFTER status');
}
if (!kolom_ada($koneksi, 'users', 'ban_reason')) {
    mysqli_query($koneksi, 'ALTER TABLE users ADD COLUMN ban_reason VARCHAR(255) NULL DEFAULT NULL AFTER banned_at');
}
if (!kolom_ada($koneksi, 'users', 'banned_by')) {
    mysqli_query($koneksi, 'ALTER TABLE users ADD COLUMN banned_by INT UNSIGNED NULL DEFAULT NULL AFTER ban_reason');
}
if (!indeks_ada($koneksi, 'users', 'idx_users_status')) {
    mysqli_query($koneksi, 'ALTER TABLE users ADD INDEX idx_users_status (status)');
}
if (!indeks_ada($koneksi, 'users', 'idx_users_banned_by')) {
    mysqli_query($koneksi, 'ALTER TABLE users ADD INDEX idx_users_banned_by (banned_by)');
}
if (!constraint_ada($koneksi, 'users', 'fk_users_banned_by')) {
    mysqli_query($koneksi, 'ALTER TABLE users ADD CONSTRAINT fk_users_banned_by FOREIGN KEY (banned_by) REFERENCES users(id) ON DELETE SET NULL');
}

// 2. Update articles moderation columns (existing rows -> approved)
$artikel_baru = !kolom_ada($koneksi, 'articles', 'moderation_status');
if ($artikel_baru) {
    mysqli_query($koneksi, "ALTER TABLE articles ADD COLUMN moderation_status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending' AFTER cover_image");
}
if (!kolom_ada($koneksi, 'articles', 'moderated_at')) {
    mysqli_query($koneksi, 'ALTER TABLE articles ADD COLUMN moderated_at TIMESTAMP NULL DEFAULT NULL AFTER moderation_status');
}
if (!kolom_ada($koneksi, 'articles', 'moderated_by')) {
    mysqli_query($koneksi, 'ALTER TABLE articles ADD COLUMN moderated_by INT UNSIGNED NULL DEFAULT NULL AFTER moderated_at');
}
if (!kolom_ada($koneksi, 'articles', 'rejection_reason')) {
    mysqli_query($koneksi, 'ALTER TABLE articles ADD COLUMN rejection_reason VARCHAR(500) NULL DEFAULT NULL AFTER moderated_by');
}
if (!indeks_ada($koneksi, 'articles', 'idx_articles_moderation_status')) {
    mysqli_query($koneksi, 'ALTER TABLE articles ADD INDEX idx_articles_moderation_status (moderation_status)');
}
if (!indeks_ada($koneksi, 'articles', 'idx_articles_moderated_by')) {
    mysqli_query($koneksi, 'ALTER TABLE articles ADD INDEX idx_articles_moderated_by (moderated_by)');
}
if (!constraint_ada($koneksi, 'articles', 'fk_articles_moderated_by')) {
    mysqli_query($koneksi, 'ALTER TABLE articles ADD CONSTRAINT fk_articles_moderated_by FOREIGN KEY (moderated_by) REFERENCES users(id) ON DELETE SET NULL');
}
if ($artikel_baru) {
    mysqli_query($koneksi, "UPDATE articles SET moderation_status = 'approved' WHERE moderated_at IS NULL AND moderation_status = 'pending'");
}

// 3. Update foods moderation columns (existing rows -> approved)
$pangan_baru = !kolom_ada($koneksi, 'foods', 'moderation_status');
if ($pangan_baru) {
    mysqli_query($koneksi, "ALTER TABLE foods ADD COLUMN moderation_status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending' AFTER source_name");
}
if (!kolom_ada($koneksi, 'foods', 'moderated_at')) {
    mysqli_query($koneksi, 'ALTER TABLE foods ADD COLUMN moderated_at TIMESTAMP NULL DEFAULT NULL AFTER moderation_status');
}
if (!kolom_ada($koneksi, 'foods', 'moderated_by')) {
    mysqli_query($koneksi, 'ALTER TABLE foods ADD COLUMN moderated_by INT UNSIGNED NULL DEFAULT NULL AFTER moderated_at');
}
if (!kolom_ada($koneksi, 'foods', 'rejection_reason')) {
    mysqli_query($koneksi, 'ALTER TABLE foods ADD COLUMN rejection_reason VARCHAR(500) NULL DEFAULT NULL AFTER moderated_by');
}
if (!indeks_ada($koneksi, 'foods', 'idx_foods_moderation_status')) {
    mysqli_query($koneksi, 'ALTER TABLE foods ADD INDEX idx_foods_moderation_status (moderation_status)');
}
if (!indeks_ada($koneksi, 'foods', 'idx_foods_moderated_by')) {
    mysqli_query($koneksi, 'ALTER TABLE foods ADD INDEX idx_foods_moderated_by (moderated_by)');
}
if (!constraint_ada($koneksi, 'foods', 'fk_foods_moderated_by')) {
    mysqli_query($koneksi, 'ALTER TABLE foods ADD CONSTRAINT fk_foods_moderated_by FOREIGN KEY (moderated_by) REFERENCES users(id) ON DELETE SET NULL');
}
if ($pangan_baru) {
    mysqli_query($koneksi, "UPDATE foods SET moderation_status = 'approved' WHERE moderated_at IS NULL AND moderation_status = 'pending'");
}

// 4. Create moderation_logs table
mysqli_query(
    $koneksi,
    "CREATE TABLE IF NOT EXISTS moderation_logs (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
);

echo "Migrasi CR-03 selesai.\n";
