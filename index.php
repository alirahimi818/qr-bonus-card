<?php
/*
Plugin Name: QR-Code Bonus Card
Plugin URI: https://github.com/alirahimi818/qr-discount-coupon
Description: generate QR-Code for Bonus Card.
Author: Ali Rahimi
Version: 1.0
Author URI: https://alirahimi818.ir
*/

define('PLUGIN_FILE_URL', __FILE__);
define('PLUGIN_BASE_URL', plugin_dir_path(__FILE__));

require_once(PLUGIN_BASE_URL . 'includes/database.php');
require_once(PLUGIN_BASE_URL . 'includes/pages.php');
require_once(PLUGIN_BASE_URL . 'includes/QrCodeDiscount.php');
require_once(PLUGIN_BASE_URL . 'includes/shortcode.php');
require_once(PLUGIN_BASE_URL . 'includes/admin-create-qr.php');
require_once(PLUGIN_BASE_URL . 'includes/setting.php');

function run_default_setting()
{
    update_option('qr_bonus_checksum', uniqid() . '--1');
    update_option('qr_bonus_win_count', '12');
    update_option('qr_bonus_date_format', 'D. d.m.Y H:i');
    update_option('qr_bonus_card_deactivate_img_url', plugins_url('/assets/coffee.jpg', PLUGIN_FILE_URL));
    update_option('qr_bonus_card_active_img_url', plugins_url('/assets/coffee-active.jpg', PLUGIN_FILE_URL));
}

register_activation_hook(PLUGIN_FILE_URL, 'run_default_setting');

function qrbc_load_textdomain()
{
    load_textdomain('qrbc', PLUGIN_BASE_URL . 'languages/qrbc-' . get_locale() . '.mo');
}
add_action('init', 'qrbc_load_textdomain');