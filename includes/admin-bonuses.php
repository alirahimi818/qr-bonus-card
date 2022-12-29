<?php

add_action('admin_menu', 'add_qr_bonus_menu_to_admin');

function add_qr_bonus_menu_to_admin()
{
    add_menu_page(__('QR-Code Bonuses', 'qrbc'), __('Bonus Card', 'qrbc'), 'manage_options', 'qr-bonus-card', 'qr_bonus_admin_page', 'dashicons-tickets-alt', 44);
}

function qr_bonus_admin_page()
{
    wp_enqueue_script('new_script', plugins_url('/assets/admin.js', PLUGIN_FILE_URL), false, '1.0', 'all');
    wp_enqueue_style('new_style', plugins_url('/assets/admin.css', PLUGIN_FILE_URL), false, '1.0', 'all');
    wp_enqueue_script( 'jquery-ui-datepicker' );

    $date_format = get_option('qr_bonus_date_format');

    global $wpdb;
    $bonus_table_name = $wpdb->prefix . "qr_bonuses";
    $bonus_user_table_name = $wpdb->prefix . "qr_bonus_users";

    $num = 20;
    $from = 0;
    $pagination = 1;
    if (@$_GET['pagination']) {
        $pagination = (int)$_GET['pagination'];
        $from = ($pagination - 1) * $num;
    }

    $query = "FROM {$bonus_table_name} INNER JOIN {$bonus_user_table_name} ON {$bonus_table_name}.bonus_user_id={$bonus_user_table_name}.id ";

    if (@$_GET['s']) {
        $s = $_GET['s'];
        if (str_contains($s, 'qr-')) {
            $query .= "WHERE {$bonus_user_table_name}.user_unique LIKE '%{$s}%'";
        } else {
            $query .= "WHERE checksum LIKE '%{$s}%'";
        }
    } else if (@$_GET['id_list']) {
        $ids = str_replace('|', ',', $_GET['id_list']);
        $query .= "WHERE {$bonus_table_name}.id IN ({$ids})";
    }

    if (@$_GET['date']) {
        $query = qr_where_date_query($query, "{$bonus_table_name}.created_at", $_GET['date']);
    }

    $count_query = "SELECT COUNT(*) " . $query;
    $query = "SELECT {$bonus_table_name}.*, {$bonus_user_table_name}.user_unique " . $query;

    $items_count = $wpdb->get_var("{$count_query}");
    $items = $wpdb->get_results("{$query} ORDER BY id DESC LIMIT {$from},{$num}");
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline"><?php _e('QR-Code Bonuses', 'qrbc') ?></h1>
        <a href="<?php echo site_url('/qr-bonus-show/') ?>" target="_blank"
           class="page-title-action"><?php _e('Add New', 'qrbc') ?></a>
        <form action="" method="GET" class="qr-search-form">
            <input type="hidden" name="page" value="qr-bonus-card">
            <p class="search-box" style="margin-bottom: 10px;">
                <input type="text" id="search-input" name="s" value="" placeholder="<?php _e('Search', 'qrbc') ?>...">
                <input type="submit" id="search-submit" class="button" value="<?php _e('Search', 'qrbc') ?>"></p>
            <p class="search-box" style="margin: 0 20px 10px;">
                <input type="text" id="date-input" name="date" value="" placeholder="DD.MM.YYYY">
                <input type="submit" id="date-submit" class="button" value="<?php _e('Search by date', 'qrbc') ?>"></p>
        </form>
        <div><?php echo @$_GET['id_list'] || @$_GET['date'] ? __('scan count: ') . $items_count : '' ?></div>
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
                        <td><?php echo $item->id ?></td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=qr-bonus-card&s=' . $item->user_unique) ?>"><?php echo $item->user_unique ?></a>
                        </td>
                        <td><?php echo $item->checksum ?></td>
                        <td><?php $item->status == 1 ? _e('not used', 'qrbc') : _e('used', 'qrbc') ?></td>
                        <td><?php echo date($date_format, strtotime($item->created_at)) ?></td>
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
            table_pagination(<?php echo $items_count; ?>, <?php echo $num; ?>, <?php echo $pagination; ?>, "<?php _e('pages', 'qrbc'); ?>");
        }, 500)
        jQuery('#search-input').val('<?php echo @$_GET['s']; ?>');
        jQuery( function( $ ) {
            $( '#date-input' ).datepicker({
                dateFormat: "dd.mm.yy"
            });
        });
    </script>
    <?php
}