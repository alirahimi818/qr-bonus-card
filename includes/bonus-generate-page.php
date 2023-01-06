<?php

wp_enqueue_style('new_style', plugins_url('/assets/style.css', PLUGIN_FILE_URL), false, '1.0', 'all');
wp_enqueue_script('new_script', plugins_url('/assets/generate.js', PLUGIN_FILE_URL), false, '1.0', 'all');

get_header();

if (current_user_can('manage_options')) {

    $checksum_with_count = uniqid() . '--1';
    update_option('qr_bonus_checksum', $checksum_with_count);

    $html = "";
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
    echo $html;
} else {
    wp_redirect(wp_login_url(site_url('/qr-bonus-show/')));
}

get_footer();
?>