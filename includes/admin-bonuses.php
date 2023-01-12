<?php

add_action('admin_menu', 'qrbc_add_qr_bonus_menu_to_admin');

function qrbc_add_qr_bonus_menu_to_admin()
{
    add_menu_page(__('QR-Code Bonuses', 'qrbc'), __('Bonus Card', 'qrbc'), 'manage_options', 'qr-bonus-card', 'qrbc_qr_bonus_admin_page', 'dashicons-tickets-alt', 44);
}

function qrbc_qr_bonus_admin_page()
{
    wp_enqueue_script('new_script', plugins_url('/assets/admin.js', QRBC_PLUGIN_FILE_URL), false, '1.0', 'all');
    wp_enqueue_style('new_style', plugins_url('/assets/admin.css', QRBC_PLUGIN_FILE_URL), false, '1.0', 'all');
    wp_enqueue_script('jquery-ui-datepicker');

    $date_format = get_option('qr_bonus_date_format');

    global $wpdb;
    $bonus_table_name = $wpdb->prefix . "qr_bonuses";
    $bonus_user_table_name = $wpdb->prefix . "qr_bonus_users";

    $num = 20;
    $from = 0;
    $pagination = 1;
    if (@$_GET['pagination']) {
        $pagination = (int)sanitize_text_field($_GET['pagination']);
        $from = ($pagination - 1) * $num;
    }

    $query = "FROM {$bonus_table_name} INNER JOIN {$bonus_user_table_name} ON {$bonus_table_name}.bonus_user_id={$bonus_user_table_name}.id ";

    if (@$_GET['s']) {
        $s = sanitize_text_field($_GET['s']);
        if (str_contains($s, 'qr-')) {
            $query .= $wpdb->remove_placeholder_escape($wpdb->prepare("WHERE {$bonus_user_table_name}.user_unique LIKE %s ", "%" . $wpdb->esc_like($s) . "%"));
        } else {
            $query .= $wpdb->remove_placeholder_escape($wpdb->prepare("WHERE checksum LIKE %s ", "%" . $wpdb->esc_like($s) . "%"));
        }
    } else if (@$_GET['id_list']) {
        $ids = str_replace('|', ',', sanitize_text_field($_GET['id_list']));
        $ids_arr = explode('|', sanitize_text_field($_GET['id_list']));
        $how_many = count($ids_arr);
        $placeholders = array_fill(0, $how_many, '%d');
        $format = implode(',', $placeholders);
        $query .= $wpdb->prepare("WHERE {$bonus_table_name}.id IN ($format) ", $ids_arr);
    }

    if (@$_GET['date']) {
        $query = qrbc_qr_where_date_query($query, "{$bonus_table_name}.created_at", sanitize_text_field($_GET['date']));
    }

    $count_query = "SELECT COUNT(*) " . $query;
    $query = "SELECT {$bonus_table_name}.*, {$bonus_user_table_name}.user_unique " . $query;

    $items_count = $wpdb->get_var($wpdb->prepare("{$count_query}"));
    $items = $wpdb->get_results($wpdb->prepare("{$query} ORDER BY id DESC LIMIT %d,%d", $from, $num));

    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline"><?php _e('QR-Code Bonuses', 'qrbc') ?></h1>
        <a href="<?php echo esc_url(site_url('/qr-bonus-show/')) ?>" target="_blank"
           class="page-title-action"><?php _e('Add New', 'qrbc') ?></a>
        <form action="" method="GET" class="qr-search-form">
            <input type="hidden" name="page" value="qr-bonus-card">
            <p class="search-box" style="margin-bottom: 10px;">
                <input type="text" id="search-input" name="s" value="<?php echo esc_html(@$_GET['s']) ?>"
                       placeholder="<?php _e('Search', 'qrbc') ?>...">
                <input type="submit" id="search-submit" class="button" value="<?php _e('Search', 'qrbc') ?>"></p>
            <p class="search-box" style="margin: 0 20px 10px;">
                <input type="text" id="date-input" name="date" value="<?php echo esc_html(@$_GET['date']) ?>"
                       placeholder="DD.MM.YYYY">
                <input type="submit" id="date-submit" class="button" value="<?php _e('Search by date', 'qrbc') ?>"></p>
        </form>
        <div><?php echo esc_html(@$_GET['id_list'] || @$_GET['date'] ? __('scan count') . ': ' . $items_count : '') ?></div>
        <table class="wp-list-table widefat striped table-view-list pagination-table">
            <thead>
            <tr>
                <th>ID</th>
                <th><?php _e('user', 'qrbc') ?></th>
                <th><?php _e('code', 'qrbc') ?></th>
                <th><?php _e('status', 'qrbc') ?></th>
                <th><?php _e('date', 'qrbc') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($items)) {
                foreach ($items as $item) { ?>
                    <tr>
                        <td><?php echo esc_html($item->id) ?></td>
                        <td>
                            <a href="<?php echo esc_url(admin_url('admin.php?page=qr-bonus-card&s=' . $item->user_unique)) ?>"><?php echo esc_html($item->user_unique) ?></a>
                        </td>
                        <td><?php echo esc_html($item->checksum) ?></td>
                        <td><?php $item->status == 1 ? _e('not used', 'qrbc') : _e('used', 'qrbc') ?></td>
                        <td><?php echo esc_html(date($date_format, strtotime($item->created_at))) ?></td>
                    </tr>
                <?php }
            } else {
                ?>
                <tr>
                    <td class="text-center red" colspan="5"><?php _e('Not found!', 'qrbc') ?></td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>
    <script>
        setTimeout(function () {
            table_pagination(<?php echo esc_html($items_count) ?>, <?php echo esc_html($num) ?>, <?php echo esc_html($pagination) ?>, "<?php _e('pages', 'qrbc'); ?>");
        }, 500)
        jQuery(function ($) {
            $('#date-input').datepicker({
                dateFormat: "dd.mm.yy"
            });
        });
    </script>
    <?php
}