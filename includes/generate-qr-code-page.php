<?php
include_once(plugin_dir_path(QRBC_PLUGIN_FILE_URL) . '/includes/phpqrcode/qrlib.php');
$url = site_url('/qr-bonus-profile/?checksum=' . sanitize_text_field($_GET['string']));
if (@$_GET['count']) {
    $url .= '--' . sanitize_text_field($_GET['count']);
}
return QRcode::png(esc_url($url));
?>