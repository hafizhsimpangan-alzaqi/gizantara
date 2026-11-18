<?php

function login_rate_limit_directory() {
    static $direktori = null;
    static $gagal = false;
    if ($direktori !== null) return $direktori;
    if ($gagal) return false;

    // shortcut: state is local to this host; use shared storage before adding PHP nodes.
    $pilihan = getenv('GIZANTARA_RATE_LIMIT_DIR');
    $pilihan = is_string($pilihan) && $pilihan !== ''
        ? $pilihan
        : sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'gizantara-login-rate-limit';
    $induk = realpath(dirname($pilihan));
    if ($induk === false) { $gagal = true; return false; }
    $path = $induk . DIRECTORY_SEPARATOR . basename($pilihan);
    if (is_link($path) || (!is_dir($path) && !@mkdir($path, 0700))) { $gagal = true; return false; }
    if (is_link($path) || !is_dir($path) || !@chmod($path, 0700)
        || (fileperms($path) & 0777) !== 0700) { $gagal = true; return false; }

    $dokumen = realpath($_SERVER['DOCUMENT_ROOT'] ?? '');
    $real = realpath($path);
    if ($real === false || ($dokumen !== false
        && ($real === $dokumen || str_starts_with($real, rtrim($dokumen, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR)))) {
        $gagal = true;
        return false;
    }
    if (function_exists('posix_geteuid') && fileowner($real) !== posix_geteuid()) {
        $gagal = true;
        return false;
    }

    $direktori = $real;
    return $direktori;
}

function login_rate_limit_secret($direktori) {
    $path = $direktori . DIRECTORY_SEPARATOR . 'secret.key';
    if (is_link($path)) return false;
    $file = @fopen($path, 'c+b');
    if ($file === false) return false;
    @chmod($path, 0600);
    if ((fileperms($path) & 0777) !== 0600 || !flock($file, LOCK_EX)) {
        fclose($file);
        return false;
    }

    rewind($file);
    $secret = stream_get_contents($file);
    if ($secret === '' || $secret === false) {
        try { $secret = random_bytes(32); } catch (Throwable $error) { $secret = false; }
        if ($secret !== false) {
            rewind($file);
            if (!ftruncate($file, 0) || fwrite($file, $secret) !== 32 || !fflush($file)) $secret = false;
        }
    }
    flock($file, LOCK_UN);
    fclose($file);
    return is_string($secret) && strlen($secret) === 32 ? $secret : false;
}

function login_rate_limit_state() {
    $direktori = login_rate_limit_directory();
    if ($direktori === false) return false;
    $secret_simpan = login_rate_limit_secret($direktori);
    if ($secret_simpan === false) return false;

    $path = $direktori . DIRECTORY_SEPARATOR . 'state.json';
    if (is_link($path)) return false;
    $file = @fopen($path, 'c+b');
    if ($file === false) return false;
    @chmod($path, 0600);
    if ((fileperms($path) & 0777) !== 0600 || !flock($file, LOCK_EX)) {
        fclose($file);
        return false;
    }
    $ukuran = fstat($file)['size'] ?? 0;
    if ($ukuran > 1048576) {
        flock($file, LOCK_UN);
        fclose($file);
        return false;
    }
    rewind($file);
    $isi = stream_get_contents($file);
    if ($isi === false) {
        flock($file, LOCK_UN);
        fclose($file);
        return false;
    }
    if ($isi === '') {
        $state = ['ip' => [], 'users' => [], 'pending' => []];
    } else {
        $state = json_decode($isi, true);
        if (!is_array($state) || !isset($state['ip'], $state['users'], $state['pending'])
            || !is_array($state['ip']) || !is_array($state['users']) || !is_array($state['pending'])) {
            flock($file, LOCK_UN);
            fclose($file);
            return false;
        }
    }
    return [$file, $state, $secret_simpan];
}

function login_rate_limit_prune(&$state, $now) {
    foreach (['ip', 'users'] as $kelompok) {
        foreach ($state[$kelompok] as $id => $waktu) {
            if (!is_array($waktu)) return false;
            $state[$kelompok][$id] = array_values(array_filter($waktu, function ($nilai) use ($now) {
                return is_int($nilai) && $nilai > $now - 900;
            }));
            if (!$state[$kelompok][$id]) unset($state[$kelompok][$id]);
        }
    }
    foreach ($state['pending'] as $token => $data) {
        if (!is_array($data) || !isset($data['ip'], $data['user'], $data['time'])
            || !is_string($data['ip']) || !is_string($data['user']) || !is_int($data['time'])) return false;
        if ($data['time'] <= $now - 300) unset($state['pending'][$token]);
    }
    return true;
}

function login_rate_limit_retry($gagal, $pending, $now, $batas) {
    if (count($gagal) + count($pending) < $batas) return 0;
    $akhir = array_map(function ($waktu) { return $waktu + 900; }, $gagal);
    foreach ($pending as $waktu) $akhir[] = $waktu + 300;
    sort($akhir, SORT_NUMERIC);
    return max(1, $akhir[count($akhir) - $batas] - $now);
}

function login_rate_limit_begin($username, $ip, $now = null) {
    $now = is_int($now) ? $now : time();
    if (!is_string($username) || !is_string($ip) || filter_var($ip, FILTER_VALIDATE_IP) === false) return false;
    $terbuka = login_rate_limit_state();
    if ($terbuka === false) return false;
    [$file, $state, $secret] = $terbuka;
    $id_ip = hash_hmac('sha256', "ip\0" . $ip, $secret);
    $nama = strtolower(trim($username));
    $id_user = hash_hmac('sha256', "user\0" . $nama . "\0" . $ip, $secret);

    if (!login_rate_limit_prune($state, $now)) {
        flock($file, LOCK_UN); fclose($file); return false;
    }
    $ip_waktu = $state['ip'][$id_ip] ?? [];
    $user_waktu = $state['users'][$id_user] ?? [];
    $ip_pending = [];
    $user_pending = [];
    foreach ($state['pending'] as $data) {
        if ($data['ip'] === $id_ip) $ip_pending[] = $data['time'];
        if ($data['user'] === $id_user) $user_pending[] = $data['time'];
    }
    $ip_wait = login_rate_limit_retry($ip_waktu, $ip_pending, $now, 20);
    $user_wait = login_rate_limit_retry($user_waktu, $user_pending, $now, 5);
    if ($ip_wait || $user_wait) {
        if (!$ip_wait && $user_wait) $state['ip'][$id_ip][] = $now;
        login_rate_limit_write($file, $state);
        flock($file, LOCK_UN); fclose($file);
        return ['allowed' => false, 'retry_after' => max($ip_wait, $user_wait), 'reason' => $ip_wait ? 'ip' : 'user'];
    }

    try { $token = bin2hex(random_bytes(16)); } catch (Throwable $error) {
        flock($file, LOCK_UN); fclose($file); return false;
    }
    $state['pending'][$token] = ['ip' => $id_ip, 'user' => $id_user, 'time' => $now];
    $berhasil = login_rate_limit_write($file, $state);
    flock($file, LOCK_UN); fclose($file);
    return $berhasil ? ['allowed' => true, 'token' => $token] : false;
}

function login_rate_limit_rekey($username, $ip, $token, $account_id, $now = null) {
    $now = is_int($now) ? $now : time();
    if (!is_string($username) || !is_string($ip) || filter_var($ip, FILTER_VALIDATE_IP) === false
        || !is_string($token) || !is_int($account_id) || $account_id < 1) return false;
    $terbuka = login_rate_limit_state();
    if ($terbuka === false) return false;
    [$file, $state, $secret] = $terbuka;
    $id_ip = hash_hmac('sha256', "ip\0" . $ip, $secret);
    $id_lama = hash_hmac('sha256', "user\0" . strtolower(trim($username)) . "\0" . $ip, $secret);
    $id_akun = hash_hmac('sha256', "account\0" . $account_id . "\0" . $ip, $secret);
    $pending = $state['pending'][$token] ?? null;
    if (!$pending || $pending['ip'] !== $id_ip || $pending['user'] !== $id_lama
        || !login_rate_limit_prune($state, $now)) {
        flock($file, LOCK_UN); fclose($file); return false;
    }

    $gagal = $state['users'][$id_akun] ?? [];
    $menunggu = [];
    foreach ($state['pending'] as $kunci => $data) {
        if ($kunci !== $token && $data['user'] === $id_akun) $menunggu[] = $data['time'];
    }
    $retry_after = login_rate_limit_retry($gagal, $menunggu, $now, 5);
    if ($retry_after) {
        unset($state['pending'][$token]);
        $state['ip'][$id_ip][] = $now;
        $berhasil = login_rate_limit_write($file, $state);
        flock($file, LOCK_UN); fclose($file);
        return $berhasil ? ['allowed' => false, 'retry_after' => $retry_after, 'reason' => 'user'] : false;
    }

    $state['pending'][$token]['user'] = $id_akun;
    $berhasil = login_rate_limit_write($file, $state);
    flock($file, LOCK_UN); fclose($file);
    return $berhasil ? ['allowed' => true] : false;
}

function login_rate_limit_finish($username, $ip, $token, $hasil, $now = null, $account_id = null) {
    $now = is_int($now) ? $now : time();
    if (!is_string($username) || !is_string($ip) || filter_var($ip, FILTER_VALIDATE_IP) === false
        || !is_string($token) || !in_array($hasil, ['failed', 'success', 'neutral'], true)
        || ($account_id !== null && (!is_int($account_id) || $account_id < 1))) return false;
    $terbuka = login_rate_limit_state();
    if ($terbuka === false) return false;
    [$file, $state, $secret] = $terbuka;
    $id_user = $account_id === null
        ? hash_hmac('sha256', "user\0" . strtolower(trim($username)) . "\0" . $ip, $secret)
        : hash_hmac('sha256', "account\0" . $account_id . "\0" . $ip, $secret);
    $pending = $state['pending'][$token] ?? null;
    if (!$pending || $pending['user'] !== $id_user) {
        flock($file, LOCK_UN); fclose($file); return false;
    }
    unset($state['pending'][$token]);
    if ($hasil === 'failed') {
        $state['ip'][$pending['ip']][] = $now;
        $state['users'][$id_user][] = $now;
    } elseif ($hasil === 'success') {
        unset($state['users'][$id_user]);
    }
    $berhasil = login_rate_limit_prune($state, $now) && login_rate_limit_write($file, $state);
    flock($file, LOCK_UN); fclose($file);
    return $berhasil;
}

function login_rate_limit_write($file, $state) {
    $isi = json_encode($state, JSON_UNESCAPED_SLASHES);
    if (!is_string($isi) || strlen($isi) > 1048576) return false;
    rewind($file);
    return ftruncate($file, 0) && fwrite($file, $isi) === strlen($isi) && fflush($file);
}
