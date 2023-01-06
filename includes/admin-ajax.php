<?php

add_action("wp_ajax_generate_qr_bonus_card", "generate_qr_bonus_card");
add_action("wp_ajax_nopriv_generate_qr_bonus_card", "generate_qr_bonus_card");
function generate_qr_bonus_card()
{
    if (current_user_can('manage_options')) {
        $count = @$_REQUEST['count'];
        if (!$count or $count < 1) {
            echo plugins_url('/assets/error-qr.png', PLUGIN_FILE_URL);
        } else {
            $checksum_with_count = uniqid() . '--' . $count;
            update_option('qr_bonus_checksum', $checksum_with_count);
            $return_url = site_url('/qr-bonus-generate/?string=' . $checksum_with_count);
            echo $return_url;
        }
    }
    wp_die();
}

add_action("wp_ajax_cookie_qr_bonus_card_checksum", "cookie_qr_bonus_card_checksum");
add_action("wp_ajax_nopriv_cookie_qr_bonus_card_checksum", "cookie_qr_bonus_card_checksum");
function cookie_qr_bonus_card_checksum()
{
    if (@$_REQUEST['checksum'] and @$_REQUEST['bonus_user']) {
        $checksum = $_REQUEST['checksum'];
        $user = $_REQUEST['bonus_user'];
        $qrCodeBonus = new QrCodeBonus($user);
        $option_checksum = get_option('qr_bonus_checksum');
        if ($checksum == $option_checksum) {
            $create_bonus = $qrCodeBonus->createbonus($checksum);
            if ($create_bonus['status']) {
                echo '{"status":"success","message":"' . $create_bonus['message'] . '","url":"' . site_url('/qr-bonus-profile/') . '"}';
            } else {
                echo '{"status":"failed","message":"' . $create_bonus['message'] . '","url":"' . site_url('/qr-bonus-profile/') . '"}';
            }
        }
    }
    wp_die();
}

add_action("wp_ajax_cookie_qr_bonus_card_user", "cookie_qr_bonus_card_user");
add_action("wp_ajax_nopriv_cookie_qr_bonus_card_user", "cookie_qr_bonus_card_user");
function cookie_qr_bonus_card_user()
{
    global $wpdb;
    $table_name = $wpdb->prefix . "qr_bonus_users";
    $user_unique_id = uniqid('qr-') . '-' . time();
    $date = current_time('mysql');
    $wpdb->insert($table_name, ['user_unique' => $user_unique_id, 'device' => @$_SERVER['HTTP_USER_AGENT'], 'created_at' => $date]);
    echo $user_unique_id;
    wp_die();
}
