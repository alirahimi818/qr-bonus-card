<?php

add_action("wp_ajax_qrbc_generate_qr_bonus_card", "qrbc_generate_qr_bonus_card");
add_action("wp_ajax_nopriv_qrbc_generate_qr_bonus_card", "qrbc_generate_qr_bonus_card");
function qrbc_generate_qr_bonus_card()
{
    if (current_user_can('manage_options')) {
        $count = sanitize_text_field(@$_REQUEST['count']);
        if (!$count or !is_numeric($count) or $count < 1) {
            echo esc_url(plugins_url('/assets/error-qr.png', QRBC_PLUGIN_FILE_URL));
        } else {
            $checksum_with_count = uniqid() . '--' . $count;
            update_option('qr_bonus_checksum', $checksum_with_count);
            $return_url = site_url('/qr-bonus-profile/?checksum=' . $checksum_with_count);
            echo esc_url($return_url);
        }
    }
    wp_die();
}

add_action("wp_ajax_qrbc_latest_history_qr_bonus", "qrbc_latest_history_qr_bonus");
add_action("wp_ajax_nopriv_qrbc_latest_history_qr_bonus", "qrbc_latest_history_qr_bonus");
function qrbc_latest_history_qr_bonus()
{
    if (current_user_can('manage_options')) {
        global $wpdb;
        $table_name = $wpdb->prefix . "qr_bonuses";
        $win_table_name = $wpdb->prefix . "qr_bonus_wins";

        $bonuses = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT bonus_user_id, MAX(created_at)  FROM $table_name GROUP BY bonus_user_id ORDER BY MAX(created_at) DESC LIMIT 15"));
        if ($bonuses and @$bonuses[0]) {
            $date_format = get_option('qr_bonus_date_format');
            $arr = [];
            foreach ($bonuses as $bonus) {

                $last_scan = $wpdb->get_results($wpdb->prepare("SELECT created_at,checksum FROM $table_name WHERE bonus_user_id = %d ORDER BY id DESC LIMIT 1", $bonus->bonus_user_id));
                $last_win = $wpdb->get_results($wpdb->prepare("SELECT created_at FROM $win_table_name WHERE bonus_user_id = %d ORDER BY id DESC LIMIT 1", $bonus->bonus_user_id));
                $item['user_id'] = $bonus->bonus_user_id;
                $item['last_win'] = '-';
                $item['active_bonus'] = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE bonus_user_id = %d AND status = 1", $bonus->bonus_user_id));
                $item['count'] = '-';
                $item['created_at'] = '-';
                $item['win_cards'] = [];
                $item['win_count'] = 0;
                if (@$last_scan[0]) {
                    $checksum_arr = explode('--', $last_scan[0]->checksum);
                    $item['count'] = @$checksum_arr[1];
                    $item['created_at'] = date($date_format, strtotime($last_scan[0]->created_at));
                }
                if (@$last_win[0]) {
                    $item['last_win'] = date($date_format, strtotime($last_win[0]->created_at));
                    $active_wins = $wpdb->get_results($wpdb->prepare("SELECT id FROM $win_table_name WHERE bonus_user_id = %d AND status = 0 ORDER BY id DESC", $bonus->bonus_user_id));
                    $item['win_cards'] = $active_wins;
                    $win_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $win_table_name WHERE bonus_user_id = %d", $bonus->bonus_user_id));
                    $item['win_count'] = $win_count;
                }

                $arr[] = $item;
            }
            $arr = array_values($arr);
            echo json_encode($arr);
        }
    }
    wp_die();
}

add_action("wp_ajax_qrbc_inactive_qr_bonus_card_win", "qrbc_inactive_qr_bonus_card_win");
add_action("wp_ajax_nopriv_qrbc_inactive_qr_bonus_card_win", "qrbc_inactive_qr_bonus_card_win");
function qrbc_inactive_qr_bonus_card_win()
{
    if (current_user_can('manage_options')) {
        $win_id = sanitize_text_field(@$_REQUEST['win_id']);
        if ($win_id and is_numeric($win_id)) {
            global $wpdb;
            $win_table_name = $wpdb->prefix . "qr_bonus_wins";
            $wpdb->update($win_table_name, ['status' => 1, 'used_at' => date('Y-m-d H:i:s')], ['id' => $win_id]);
            echo __('success submitted', 'qrbc');
        }
    }
    wp_die();
}
