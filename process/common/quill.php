<?php

function artikel_delta_lama($konten) {
    $data = is_string($konten) ? json_decode($konten, true) : null;
    return is_array($data) && isset($data['ops']) && is_array($data['ops']);
}

function salin_html_artikel($asal, $tujuan, $dokumen) {
    if ($asal->nodeType === XML_TEXT_NODE) {
        $tujuan->appendChild($dokumen->createTextNode($asal->nodeValue));
        return;
    }
    if ($asal->nodeType !== XML_ELEMENT_NODE) return;

    $tag = strtolower($asal->nodeName);
    if (in_array($tag, ['script', 'style', 'iframe', 'object', 'embed', 'form', 'input',
        'button', 'svg', 'math', 'img', 'video', 'audio', 'template'], true)) return;

    $izin = ['p', 'h1', 'h2', 'h3', 'strong', 'b', 'em', 'i', 'u', 'ol', 'ul', 'li', 'a', 'br'];
    $elemen = $tujuan;
    if (in_array($tag, $izin, true)) {
        if ($tag === 'a') {
            $href = trim($asal->getAttribute('href'));
            $skema = strtolower((string) parse_url($href, PHP_URL_SCHEME));
            if (!filter_var($href, FILTER_VALIDATE_URL) || !in_array($skema, ['http', 'https'], true)) {
                foreach ($asal->childNodes as $anak) salin_html_artikel($anak, $tujuan, $dokumen);
                return;
            }
        }
        $elemen = $dokumen->createElement($tag);
        if ($tag === 'a') $elemen->setAttribute('href', $href);
        $tujuan->appendChild($elemen);
    }
    foreach ($asal->childNodes as $anak) salin_html_artikel($anak, $elemen, $dokumen);
}

function sanitasi_html_artikel($konten) {
    if (!is_string($konten) || $konten === '' || strlen($konten) > 200000
        || !mb_check_encoding($konten, 'UTF-8') || artikel_delta_lama($konten)
        || !class_exists('DOMDocument')) return false;

    $sebelumnya = libxml_use_internal_errors(true);
    try {
        $asal = new DOMDocument();
        $opsi = LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING;
        if (!$asal->loadHTML('<!doctype html><html><head><meta charset="utf-8"></head><body><div id="konten-artikel">'
            . $konten . '</div></body></html>', $opsi)) return false;
        $wadah = $asal->getElementById('konten-artikel');
        if (!$wadah) return false;

        $bersih = new DOMDocument('1.0', 'UTF-8');
        $akar = $bersih->createElement('div');
        $bersih->appendChild($akar);
        foreach ($wadah->childNodes as $anak) salin_html_artikel($anak, $akar, $bersih);
        $teks = preg_replace('/\x{00A0}/u', ' ', $akar->textContent);
        if (trim($teks) === '') return false;

        $hasil = '';
        foreach ($akar->childNodes as $anak) $hasil .= $bersih->saveHTML($anak);
        return strlen($hasil) <= 200000 ? $hasil : false;
    } finally {
        libxml_clear_errors();
        libxml_use_internal_errors($sebelumnya);
    }
}

function ringkasan_html_artikel($konten) {
    $aman = sanitasi_html_artikel($konten);
    if ($aman === false) return 'Isi artikel belum tersedia.';
    $teks = trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($aman), ENT_QUOTES | ENT_HTML5, 'UTF-8')));
    return mb_strlen($teks, 'UTF-8') > 150 ? mb_substr($teks, 0, 150, 'UTF-8') . '…' : $teks;
}
