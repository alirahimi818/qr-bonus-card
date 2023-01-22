<?php
$favicon_url = "";
$html = "";

if (current_user_can('manage_options')) {

    $favicon_url = wp_get_attachment_image_url(get_option('qr_bonus_card_active_favicon_img'), 'full');
    $checksum_with_count = uniqid() . '--1';
    update_option('qr_bonus_checksum', $checksum_with_count);

    $html .= "<div class='qr-generate-page'>
                    <div class='barcode-area'><img width='320' src='" . esc_url(site_url('/qr-bonus-generate/?string=' . $checksum_with_count)) . "'></div>
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
    <meta name="msapplication-TileColor" content="#267d00">
    <meta name="theme-color" content="#ffffff">
    <link rel="shortcut icon" href="<?php echo esc_url($favicon_url) ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo esc_url($favicon_url) ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo esc_url($favicon_url) ?>">
    <link rel="apple-touch-icon" href="<?php echo esc_url($favicon_url) ?>">
    <link rel="apple-touch-icon" sizes="114x114" href="<?php echo esc_url($favicon_url) ?>">
    <link rel="apple-touch-icon" sizes="72x72" href="<?php echo esc_url($favicon_url) ?>">
    <link rel="apple-touch-icon" sizes="144x144" href="<?php echo esc_url($favicon_url) ?>">
    <title>Bonus Generate</title>
    <link rel="manifest" href="<?php echo esc_url(plugins_url('/assets/pwa-manifest.json', QRBC_PLUGIN_FILE_URL)) ?>">
    <link rel="stylesheet" href="<?php echo esc_url(plugins_url('/assets/style.css', QRBC_PLUGIN_FILE_URL)) ?>">
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('<?php echo esc_js(esc_url(plugins_url('/assets/pwa-sw.js', QRBC_PLUGIN_FILE_URL))) ?>')
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
    <?php the_content(''); ?>
    <div class='qr-generate-page'>
        <div class='barcode-area'>
            <div class="barcode-image"><img width='320'
                      src='<?php echo esc_url(plugins_url('/assets/error-qr.png', QRBC_PLUGIN_FILE_URL)) ?>'></div>
        </div>
        <div class='qr-control-area'>
            <button type='button' class='control-btn minus'>-</button>
            <input type='number' class='input-number' min='1' value='1' autocomplete='off' aria-autocomplete='off'>
            <button type='button' class='control-btn plus'>+</button>
        </div>
        <div class='qr-control-area'>
            <button type='button' class='new-qr-btn'><?php _e('New QR-Code', 'qrbc') ?></button>
        </div>
    </div>
    <p class="text-center mt-2"><span
                class="cursor-pointer text-green bonus-today-history-toggle-btn"><?php _e('show latest history', 'qrbc') ?></span>
    </p>
    <div class="qr-bonuses-history-area display-none">
        <table class="qr-bonuses-history">
            <thead>
            <tr>
                <th><?php _e('user', 'qrbc') ?></th>
                <th><?php _e('last scan date', 'qrbc') ?></th>
                <th><?php _e('count of last scan', 'qrbc') ?></th>
                <th><?php _e('active bonus', 'qrbc') ?></th>
                <th><?php _e('last win date', 'qrbc') ?></th>
                <th><?php _e('count of win', 'qrbc') ?></th>
                <th><?php _e('use of bonus card', 'qrbc') ?></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            </tbody>
        </table>
        <div class="qr-bonuses-history-refresh-btn">
            <svg fill="#000000" height="20px" width="20px" id="Capa_1" xmlns="http://www.w3.org/2000/svg"
                 xmlns:xlink="http://www.w3.org/1999/xlink"
                 viewBox="0 0 489.698 489.698" xml:space="preserve">
            <g>
                <g>
                    <path d="M468.999,227.774c-11.4,0-20.8,8.3-20.8,19.8c-1,74.9-44.2,142.6-110.3,178.9c-99.6,54.7-216,5.6-260.6-61l62.9,13.1
                        c10.4,2.1,21.8-4.2,23.9-15.6c2.1-10.4-4.2-21.8-15.6-23.9l-123.7-26c-7.2-1.7-26.1,3.5-23.9,22.9l15.6,124.8
                        c1,10.4,9.4,17.7,19.8,17.7c15.5,0,21.8-11.4,20.8-22.9l-7.3-60.9c101.1,121.3,229.4,104.4,306.8,69.3
                        c80.1-42.7,131.1-124.8,132.1-215.4C488.799,237.174,480.399,227.774,468.999,227.774z"/>
                    <path d="M20.599,261.874c11.4,0,20.8-8.3,20.8-19.8c1-74.9,44.2-142.6,110.3-178.9c99.6-54.7,216-5.6,260.6,61l-62.9-13.1
                        c-10.4-2.1-21.8,4.2-23.9,15.6c-2.1,10.4,4.2,21.8,15.6,23.9l123.8,26c7.2,1.7,26.1-3.5,23.9-22.9l-15.6-124.8
                        c-1-10.4-9.4-17.7-19.8-17.7c-15.5,0-21.8,11.4-20.8,22.9l7.2,60.9c-101.1-121.2-229.4-104.4-306.8-69.2
                        c-80.1,42.6-131.1,124.8-132.2,215.3C0.799,252.574,9.199,261.874,20.599,261.874z"/>
                </g>
            </g>
        </svg>
        </div>
    </div>
</div>
<script type="text/javascript"
        src="<?php echo esc_url(plugins_url('/assets/sweetalert.min.js', QRBC_PLUGIN_FILE_URL)) ?>"></script>
<script type="text/javascript"
        src="<?php echo esc_url(plugins_url('/assets/qrcode.min.js', QRBC_PLUGIN_FILE_URL)) ?>"></script>
<script type="text/javascript"
        src="<?php echo esc_url(plugins_url('/assets/generate.js', QRBC_PLUGIN_FILE_URL)) ?>"></script>
</body>
</html>
