<?php

function create_bonus_user_table()
{

    global $wpdb;
    $table_name = $wpdb->prefix . "qr_bonus_users";
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
      id bigint(20) NOT NULL AUTO_INCREMENT,
      user_unique varchar(254) NOT NULL,
      device varchar(254) NULL,
      created_at datetime NOT NULL,
      PRIMARY KEY id (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

register_activation_hook(PLUGIN_FILE_URL, 'create_bonus_user_table');


function create_bonus_table()
{

    global $wpdb;
    $table_name = $wpdb->prefix . "qr_bonuses";
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
      id bigint(20) NOT NULL AUTO_INCREMENT,
      bonus_user_id bigint(20) NOT NULL,
      checksum varchar(254) NOT NULL,
      status tinyint(1) NOT NULL,
      created_at datetime NOT NULL,
      PRIMARY KEY id (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

register_activation_hook(PLUGIN_FILE_URL, 'create_bonus_table');

function create_bonus_wins_table()
{

    global $wpdb;
    $table_name = $wpdb->prefix . "qr_bonus_wins";
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
      id bigint(20) NOT NULL AUTO_INCREMENT,
      bonus_user_id bigint(20) NOT NULL,
      bonus_ids varchar(254) NOT NULL,
      created_at datetime NOT NULL,
      PRIMARY KEY id (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

register_activation_hook(PLUGIN_FILE_URL, 'create_bonus_wins_table');
