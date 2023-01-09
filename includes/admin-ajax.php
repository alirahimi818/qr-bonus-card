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

add_action("wp_ajax_today_history_qr_bonus", "today_history_qr_bonus");
add_action("wp_ajax_nopriv_today_history_qr_bonus", "today_history_qr_bonus");
function today_history_qr_bonus()
{
    if (current_user_can('manage_options')) {
        global $wpdb;
        $table_name = $wpdb->prefix . "qr_bonuses";
        $win_table_name = $wpdb->prefix . "qr_bonus_wins";
        $date = wp_date('Y-m-d');
        $bonuses = $wpdb->get_results("SELECT * FROM $table_name WHERE created_at LIKE '%{$date}%' ORDER BY id DESC");
        if ($bonuses and @$bonuses[0]) {
            $date_format = get_option('qr_bonus_date_format');
            $arr = [];
            foreach ($bonuses as $bonus) {
                $last_scan = $wpdb->get_results("SELECT created_at,checksum FROM $table_name WHERE bonus_user_id = {$bonus->bonus_user_id} ORDER BY id DESC LIMIT 1");
                $last_win = $wpdb->get_results("SELECT created_at FROM $win_table_name WHERE bonus_user_id = {$bonus->bonus_user_id} ORDER BY id DESC LIMIT 1");
                $item['user_id'] = $bonus->bonus_user_id;
                $item['last_win'] = @$last_win[0] ? wp_date($date_format, strtotime($last_win[0]->created_at)) : '-';
                $item['active_bonus'] = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE bonus_user_id = {$bonus->bonus_user_id} AND status = 1");
                $item['count'] = '-';
                $item['created_at'] = '-';
                if(@$last_scan[0]){
                    $checksum_arr = explode('--', $last_scan[0]->checksum);
                    $item['count'] = @$checksum_arr[1];
                    $item['created_at'] = wp_date($date_format, strtotime($last_scan[0]->created_at));
                }

                $arr[$bonus->bonus_user_id] = $item;
            }
            $arr = array_values($arr);
            echo json_encode($arr);
        }
    }
    wp_die();
}
