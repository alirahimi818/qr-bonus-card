<?php

wp_enqueue_style('new_style', plugins_url('/assets/style.css', PLUGIN_FILE_URL), false, '1.0', 'all');
wp_enqueue_script('new_script', plugins_url('/assets/generate.js', PLUGIN_FILE_URL), false, '1.0', 'all');

$html = "";

if (current_user_can('manage_options')) {

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
    <title>Bonus Generate</title>
    <link rel="stylesheet" href="<?php echo plugins_url('/assets/style.css', PLUGIN_FILE_URL) ?>">
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
