<?php
if (!defined('APP_ENTRY')) { http_response_code(403); exit; }
require_once __DIR__ . '/../common/upload.php';

$tipe_moderasi = ($page ?? '') === 'moderasi_pangan' ? 'food' : 'article';
$antrean_moderasi = [];
$riwayat_moderasi = [];

try {
    $koneksi = koneksi_database();

    if ($tipe_moderasi === 'article') {
        $hasil = mysqli_query(
            $koneksi,
            "SELECT a.id, a.title, a.category, a.cover_image, a.created_at, u.username AS author, u.status AS author_status
             FROM articles a
             JOIN users u ON u.id = a.user_id
             WHERE a.moderation_status = 'pending'
             ORDER BY a.created_at ASC, a.id ASC"
        );
        $antrean_moderasi = mysqli_fetch_all($hasil, MYSQLI_ASSOC);
        foreach ($antrean_moderasi as &$item) {
            $item['cover_url'] = path_sampul_artikel($item['cover_image']);
        }
        unset($item);

        $log_hasil = mysqli_query(
            $koneksi,
            "SELECT m.id, m.content_id, m.action, m.reason, m.created_at,
                    COALESCE(a.title, CONCAT('Artikel #', m.content_id)) AS content_title,
                    COALESCE(u.username, 'Moderator terhapus') AS moderator_name
             FROM moderation_logs m
             LEFT JOIN articles a ON a.id = m.content_id
             LEFT JOIN users u ON u.id = m.moderator_id
             WHERE m.content_type = 'article'
             ORDER BY m.created_at DESC, m.id DESC
             LIMIT 20"
        );
        $riwayat_moderasi = mysqli_fetch_all($log_hasil, MYSQLI_ASSOC);
    } else {
        $hasil = mysqli_query(
            $koneksi,
            "SELECT f.id, f.name, f.food_state, f.image, f.energy_kcal, f.protein_g, f.fat_g, f.carbohydrate_g, f.source_name, f.created_at,
                    u.username AS author, u.status AS author_status
             FROM foods f
             JOIN users u ON u.id = f.user_id
             WHERE f.moderation_status = 'pending'
             ORDER BY f.created_at ASC, f.id ASC"
        );
        $antrean_moderasi = mysqli_fetch_all($hasil, MYSQLI_ASSOC);
        foreach ($antrean_moderasi as &$item) {
            $item['image_url'] = path_gambar_pangan($item['image']);
        }
        unset($item);

        $log_hasil = mysqli_query(
            $koneksi,
            "SELECT m.id, m.content_id, m.action, m.reason, m.created_at,
                    COALESCE(f.name, CONCAT('Bahan Pangan #', m.content_id)) AS content_title,
                    COALESCE(u.username, 'Moderator terhapus') AS moderator_name
             FROM moderation_logs m
             LEFT JOIN foods f ON f.id = m.content_id
             LEFT JOIN users u ON u.id = m.moderator_id
             WHERE m.content_type = 'food'
             ORDER BY m.created_at DESC, m.id DESC
             LIMIT 20"
        );
        $riwayat_moderasi = mysqli_fetch_all($log_hasil, MYSQLI_ASSOC);
    }
} catch (Throwable $error) {
    error_log('Antrean moderasi gagal dimuat.');
    http_response_code(500);
    exit('Antrean moderasi belum dapat ditampilkan.');
}
