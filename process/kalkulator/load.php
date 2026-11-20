<?php
if (!defined('APP_ENTRY')) { http_response_code(403); exit; }
try {
    $hasil = mysqli_query(koneksi_database(), "SELECT id, name, food_state, energy_kcal, protein_g, fat_g, carbohydrate_g, source_name FROM foods WHERE moderation_status = 'approved' ORDER BY name, id");
    $opsi_pangan = mysqli_fetch_all($hasil, MYSQLI_ASSOC);
} catch (Throwable $error) { error_log('Data kalkulator gagal dimuat.'); http_response_code(500); exit('Kalkulator belum dapat ditampilkan.'); }
