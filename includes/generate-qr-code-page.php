<?php
include(plugin_dir_path(QRBC_PLUGIN_FILE_URL) . '/includes/phpqrcode/qrlib.php');
$url = site_url('/qr-bonus-profile/?checksum=' . $_GET['string']);
if (@$_GET['count']) {
    $url .= '--' . $_GET['count'];
}
return QRcode::png($url);
?>