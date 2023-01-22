<?php
/*
Plugin Name: QR-Code Bonus Card
Plugin URI: https://github.com/alirahimi818/qr-bonus-card
Description: generate QR-Code for Bonus Card.
Author: Ali Rahimi
Version: 1.2.0
Author URI: https://alirahimi818.ir
*/

define('QRBC_PLUGIN_FILE_URL', __FILE__);
define('QRBC_PLUGIN_BASE_URL', plugin_dir_path(__FILE__));

require_once(QRBC_PLUGIN_BASE_URL . 'includes/database.php');
require_once(QRBC_PLUGIN_BASE_URL . 'includes/pages.php');
require_once(QRBC_PLUGIN_BASE_URL . 'includes/QRBC_QrCodeBonus.php');
require_once(QRBC_PLUGIN_BASE_URL . 'includes/admin-ajax.php');
require_once(QRBC_PLUGIN_BASE_URL . 'includes/admin-bonuses.php');
require_once(QRBC_PLUGIN_BASE_URL . 'includes/admin-bonus-wins.php');
require_once(QRBC_PLUGIN_BASE_URL . 'includes/setting.php');

function qrbc_run_default_setting()
{
    update_option('qr_bonus_checksum', uniqid() . '--1');
    update_option('qr_bonus_win_count', get_option('qr_bonus_win_count') ?: '12');
    update_option('qr_bonus_date_format', get_option('qr_bonus_date_format') ?: 'D. d.m.Y H:i');
    update_option('qr_bonus_card_deactivate_img_url', get_option('qr_bonus_card_deactivate_img_url') ?: plugins_url('/assets/coffee.jpg', QRBC_PLUGIN_FILE_URL));
    update_option('qr_bonus_card_active_img_url', get_option('qr_bonus_card_active_img_url') ?: plugins_url('/assets/coffee-active.jpg', QRBC_PLUGIN_FILE_URL));
}

register_activation_hook(QRBC_PLUGIN_FILE_URL, 'qrbc_run_default_setting');

function qrbc_load_textdomain()
{
    load_textdomain('qrbc', QRBC_PLUGIN_BASE_URL . 'languages/qrbc-' . get_locale() . '.mo');
}

add_action('init', 'qrbc_load_textdomain');

function qrbc_upgrade_to_new_version($upgrader_object, $options)
{
    $current_plugin_path_name = plugin_basename(__FILE__);

    if ($options['action'] == 'update' && $options['type'] == 'plugin' && isset( $options['plugins'] )) {
        foreach ($options['plugins'] as $each_plugin) {
            if ($each_plugin == $current_plugin_path_name) {
                qrbc_update_databse_to_new_version();
            }
        }
    }
}

add_action('upgrader_process_complete', 'qrbc_upgrade_to_new_version', 10, 2);