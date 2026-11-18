<?php
function data_monitoring_valid($tanggal, $air, $tidur, $aktivitas) {
    return is_string($tanggal) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)
        && checkdate((int) substr($tanggal, 5, 2), (int) substr($tanggal, 8, 2), (int) substr($tanggal, 0, 4))
        && is_string($air) && preg_match('/^(0|[1-9][0-9]*)$/', $air) && (int) $air <= 65535
        && is_string($tidur) && preg_match('/^(?:0|[1-9][0-9]*)(?:\.[0-9]{1,2})?$/', $tidur)
        && is_finite((float) $tidur) && (float) $tidur <= 24
        && is_string($aktivitas) && preg_match('/^(0|[1-9][0-9]*)$/', $aktivitas)
        && (int) $aktivitas <= 1440;
}
