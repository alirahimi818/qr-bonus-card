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


function qr_where_date_query($query, $table_field, $date, $date_format = 'd.m.Y', $table_date_format = 'Y-m-d')
{
    $date = DateTime::createFromFormat($date_format, $date);
    if ($date !== false) {
        $query .= str_contains($query, 'WHERE') ? ' AND ' : 'WHERE ';
        $query .= "{$table_field} LIKE '%{$date->format($table_date_format)}%' ";
    }
    return $query;
}
function qr_where_between_date_query($query, $table_field, $from_date, $to_date, $date_format = 'd.m.Y', $table_date_format = 'Y-m-d')
{
    $from_date = DateTime::createFromFormat($date_format, $from_date);
    $to_date = DateTime::createFromFormat($date_format, $to_date);
    if ($from_date !== false and $to_date !== false) {
        $query .= str_contains($query, 'WHERE') ? ' AND ' : 'WHERE ';
        $query .= "{$table_field} between '{$from_date->format($table_date_format)}' and '{$to_date->format($table_date_format)}' ";
    }
    return $query;
}