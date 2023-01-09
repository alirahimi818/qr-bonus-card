<?php

add_action('admin_menu', 'qrbc_add_qr_bonus_win_menu_to_admin');

function qrbc_add_qr_bonus_win_menu_to_admin()
{
    add_submenu_page('qr-bonus-card', __('Users bonuses', 'qrbc'), __('Users bonuses', 'qrbc'), 'manage_options', 'qr-bonus-card-wins', 'qrbc_qr_bonus_win_admin_page', 'dashicons-tickets-alt');
}

function qrbc_qr_bonus_win_admin_page()
{
    wp_enqueue_script('new_script', plugins_url('/assets/admin.js', QRBC_PLUGIN_FILE_URL), false, '1.0', 'all');
    wp_enqueue_style('new_style', plugins_url('/assets/admin.css', QRBC_PLUGIN_FILE_URL), false, '1.0', 'all');
    wp_enqueue_script('jquery-ui-datepicker');

    $date_format = get_option('qr_bonus_date_format');

    global $wpdb;
    $wins_table_name = $wpdb->prefix . "qr_bonus_wins";
    $bonus_user_table_name = $wpdb->prefix . "qr_bonus_users";

    $num = 20;
    $from = 0;
    $pagination = 1;
    if (@$_GET['pagination']) {
        $pagination = (int)sanitize_text_field($_GET['pagination']);
        $from = ($pagination - 1) * $num;
    }

    $query = "FROM {$wins_table_name} INNER JOIN {$bonus_user_table_name} ON {$wins_table_name}.bonus_user_id={$bonus_user_table_name}.id ";

    if (@$_GET['s']) {
        $s = sanitize_text_field($_GET['s']);
        $query .= "WHERE {$bonus_user_table_name}.user_unique LIKE '%{$s}%' ";
    }

    $to_date = date('d.m.Y', strtotime("last day of this month"));
    $from_date = date('d.m.Y', strtotime("first day of this month"));
    if (@$_GET['from_date'] and @$_GET['to_date']) {
        $to_date = sanitize_text_field($_GET['to_date']);
        $from_date = sanitize_text_field($_GET['from_date']);
    }
    $query = qrbc_qr_where_between_date_query($query, "{$wins_table_name}.created_at", $from_date, $to_date);
    $count_query = "SELECT COUNT(*) " . $query;
    $query = "SELECT {$wins_table_name}.*, {$bonus_user_table_name}.user_unique " . $query;

    $items_count = $wpdb->get_var("{$count_query}");
    if (@$_GET['export']) {
        $items = $wpdb->get_results("{$query} ORDER BY id DESC");
    } else {
        $items = $wpdb->get_results("{$query} ORDER BY id DESC LIMIT {$from},{$num}");
    }
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline"><?php _e('Users bonuses', 'qrbc') ?></h1>
        <form action="" method="GET" class="qr-search-form">
            <input type="hidden" name="page" value="qr-bonus-card-wins">
            <p class="search-box" style="margin-bottom: 10px;">
                <input type="text" id="search-input" name="s" value="<?php echo sanitize_text_field(@$_GET['s']) ?>"
                       placeholder="<?php _e('Search', 'qrbc') ?>...">
                <input type="submit" id="search-submit" class="button" value="<?php _e('Search', 'qrbc') ?>"></p>
            <p class="search-box" style="margin: 0 20px 10px;">
                <input type="text" id="from-date-input" name="from_date"
                       value="<?php echo sanitize_text_field(@$_GET['from_date']) ?>"
                       placeholder="<?php _e('from: ', 'qrbc') ?>DD.MM.YYYY">
                <input type="text" id="to-date-input" name="to_date"
                       value="<?php echo sanitize_text_field(@$_GET['to_date']) ?>"
                       placeholder="<?php _e('to: ', 'qrbc') ?>DD.MM.YYYY">
                <input type="submit" id="date-submit" class="button" value="<?php _e('Search by date', 'qrbc') ?>"></p>
        </form>
        <div><?php echo (@$_GET['s'] && strlen(@$_GET['s']) == 27) || @$_GET['date'] ? __('win count: ') . $items_count : '' ?></div>
        <div class="print-block"><?php echo __('from: ', 'qrbc') . $from_date . ' - ' . __('to: ', 'qrbc') . $to_date ?></div>
        <table class="wp-list-table widefat striped table-view-list pagination-table">
            <thead>
            <tr>
                <th>ID</th>
                <th><?php _e('user', 'qrbc') ?></th>
                <th><?php _e('scan count', 'qrbc') ?></th>
                <th><?php _e('date', 'qrbc') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($items)) {
                foreach ($items as $item) { ?>
                    <tr>
                        <td style="width: 10%"><?php echo $item->id ?></td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=qr-bonus-card-wins&s=' . $item->user_unique) ?>"><?php echo $item->user_unique ?></a>
                        </td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=qr-bonus-card&id_list=' . str_replace(',', '|', $item->bonus_ids)) ?>"><?php echo substr_count($item->bonus_ids, ",") + 1 ?></a>
                        </td>
                        <td><?php echo date($date_format, strtotime($item->created_at)) ?></td>
                    </tr>
                <?php }
            } else {
                ?>
                <tr>
                    <td class="text-center red" colspan="4"><?php _e('Not found!', 'qrbc') ?></td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
        <div class="alignleft actions bulkactions">
            <button type="button" id="export-button"
                    class="button print-none qr-export-btn"><?php _e('export', 'qrbc') ?></button>
        </div>
    </div>
    <script>
        setTimeout(function () {
            table_pagination(<?php echo $items_count; ?>, <?php echo $num; ?>, <?php echo $pagination; ?>, "<?php _e('pages', 'qrbc'); ?>");
        }, 200)
        jQuery(function ($) {
            $('#from-date-input, #to-date-input').datepicker({
                dateFormat: "dd.mm.yy"
            });
        });
        jQuery('#export-button').click(function () {
            window.location.href = window.location.href + '&export=1'
        })
        <?php echo @$_GET['export'] ? 'print();' : '' ?>
    </script>
    <?php
}