<?php

wp_enqueue_style('new_style', plugins_url('/assets/style.css', PLUGIN_FILE_URL), false, '1.0', 'all');
wp_enqueue_script('new_script', plugins_url('/assets/generate.js', PLUGIN_FILE_URL), false, '1.0', 'all');

$favicon_url = "";
$html = "";

if (current_user_can('manage_options')) {

    $favicon_url = wp_get_attachment_image_url(get_option('qr_bonus_card_active_favicon_img'), 'full');
    $checksum_with_count = uniqid() . '--1';
    update_option('qr_bonus_checksum', $checksum_with_count);

    $html .= "<div class='qr-generate-page'>
                    <div class='barcode-area'><img width='320' src='" . site_url('/qr-bonus-generate/?string=' . $checksum_with_count) . "'></div>
                    <div class='qr-control-area'>
                        <button type='button' class='control-btn minus'>-</button>
                        <input type='number' class='input-number' min='1' value='1' autocomplete='off' aria-autocomplete='off'>
                        <button type='button' class='control-btn plus'>+</button>
                    </div>
                    <div class='qr-control-area'>
                        <button type='button' class='new-qr-btn'>" . __('New QR-Code', 'qrbc') . "</button>
                    </div>
                  </div>
        ";
} else {
    wp_redirect(wp_login_url(site_url('/qr-bonus-show/')));
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="msapplication-TileColor" content="#af0a1a">
    <meta name="theme-color" content="#ffffff">
    <link rel="shortcut icon" href="<?php echo $favicon_url ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo $favicon_url ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo $favicon_url ?>">
    <link rel="apple-touch-icon" href="<?php echo $favicon_url ?>">
    <link rel="apple-touch-icon" sizes="114x114" href="<?php echo $favicon_url ?>">
    <link rel="apple-touch-icon" sizes="72x72" href="<?php echo $favicon_url ?>">
    <link rel="apple-touch-icon" sizes="144x144" href="<?php echo $favicon_url ?>">
    <title>Bonus Generate</title>
    <link rel="manifest" href="<?php echo plugins_url('/assets/pwa-manifest.json', PLUGIN_FILE_URL) ?>">
    <link rel="stylesheet" href="<?php echo plugins_url('/assets/style.css', PLUGIN_FILE_URL) ?>">
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('<?php echo plugins_url('/assets/pwa-sw.js', PLUGIN_FILE_URL) ?>')
                    .then(function (register) {
                        console.log('PWA service worker ready');
                        register.update();
                    })
                    .catch(function (error) {
                        console.log('Register failed! Error:' + error);
                    });
            });
        }
    </script>
</head>
<body>
<div>
    <?php
    the_content('');
    echo $html;
    ?>
</div>
<script type="text/javascript" src="<?php echo plugins_url('/assets/generate.js', PLUGIN_FILE_URL) ?>"></script>
</body>
</html>
