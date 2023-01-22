<?php

add_action('admin_menu', 'qrbc_add_qr_bonus_win_menu_to_admin');

function qrbc_add_qr_bonus_win_menu_to_admin()
{
    add_submenu_page('qr-bonus-card', __('Winners', 'qrbc'), __('Winners', 'qrbc'), 'manage_options', 'qr-bonus-card-wins', 'qrbc_qr_bonus_win_admin_page', 'dashicons-tickets-alt');
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
        $query .= $wpdb->remove_placeholder_escape($wpdb->prepare("WHERE {$bonus_user_table_name}.user_unique LIKE %s ", "%" . $wpdb->esc_like($s) . "%"));
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

    $items_count = $wpdb->get_var($wpdb->prepare("{$count_query}"));
    if (@$_GET['export']) {
        $items = $wpdb->get_results($wpdb->prepare("{$query} ORDER BY id DESC"));
    } else {
        $items = $wpdb->get_results($wpdb->prepare("{$query} ORDER BY id DESC LIMIT %d,%d", $from, $num));
    }
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline"><?php _e('Winners', 'qrbc') ?></h1>
        <form action="" method="GET" class="qr-search-form">
            <input type="hidden" name="page" value="qr-bonus-card-wins">
            <p class="search-box" style="margin-bottom: 10px;">
                <input type="text" id="search-input" name="s" value="<?php echo esc_html(@$_GET['s']) ?>"
                       placeholder="<?php _e('Search', 'qrbc') ?>...">
                <input type="submit" id="search-submit" class="button" value="<?php _e('Search', 'qrbc') ?>"></p>
            <p class="search-box" style="margin: 0 20px 10px;">
                <input type="text" id="from-date-input" name="from_date"
                       value="<?php echo esc_html(@$_GET['from_date']) ?>"
                       placeholder="<?php _e('from: ', 'qrbc') ?>DD.MM.YYYY">
                <input type="text" id="to-date-input" name="to_date"
                       value="<?php echo esc_html(@$_GET['to_date']) ?>"
                       placeholder="<?php _e('to: ', 'qrbc') ?>DD.MM.YYYY">
                <input type="submit" id="date-submit" class="button" value="<?php _e('Search by date', 'qrbc') ?>"></p>
        </form>
        <div><?php echo esc_html((@$_GET['s'] && strlen(@$_GET['s']) == 27) || @$_GET['date'] ? __('win count: ') . $items_count : '') ?></div>
        <div class="print-block"><?php echo esc_html(__('from: ', 'qrbc') . $from_date . ' - ' . __('to: ', 'qrbc') . $to_date) ?></div>
        <table class="wp-list-table widefat striped table-view-list pagination-table">
            <thead>
            <tr>
                <th>ID</th>
                <th><?php _e('user', 'qrbc') ?></th>
                <th><?php _e('scan count', 'qrbc') ?></th>
                <th><?php _e('status', 'qrbc') ?></th>
                <th><?php _e('win date', 'qrbc') ?></th>
                <th><?php _e('use of bonus card', 'qrbc') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($items)) {
                foreach ($items as $item) { ?>
                    <tr>
                        <td style="width: 10%"><?php echo esc_html($item->id) ?></td>
                        <td>
                            <a href="<?php echo esc_url(admin_url('admin.php?page=qr-bonus-card-wins&s=' . $item->user_unique)) ?>"><?php echo esc_html($item->user_unique) ?></a>
                        </td>
                        <td>
                            <a href="<?php echo esc_url(admin_url('admin.php?page=qr-bonus-card&id_list=' . str_replace(',', '|', $item->bonus_ids))) ?>"><?php echo esc_html(substr_count($item->bonus_ids, ",") + 1) ?></a>
                        </td>
                        <td><?php $item->status == 1 ? _e('used', 'qrbc') : _e('not used', 'qrbc') ?></td>
                        <td><?php echo esc_html(date($date_format, strtotime($item->created_at))) ?></td>
                        <td>
                            <?php if ($item->status == 0) { ?>
                                <button type="button" class="print-none"
                                        onclick="inactive_win_card(<?php echo esc_js($item->id) ?>)">
                                    <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path d="M16 3.93552C14.795 3.33671 13.4368 3 12 3C7.02944 3 3 7.02944 3 12C3 16.9706 7.02944 21 12 21C16.9706 21 21 16.9706 21 12C21 11.662 20.9814 11.3283 20.9451 11M21 5L12 14L9 11"
                                              stroke="#267d00" stroke-width="2" stroke-linecap="round"
                                              stroke-linejoin="round"/>
                                    </svg>
                                </button>
                            <?php } else {
                                echo $item->used_at ? esc_html(date($date_format, strtotime($item->used_at))) : '-';
                            } ?>
                        </td>
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
    <script type="text/javascript"
            src="<?php echo esc_url(plugins_url('/assets/sweetalert.min.js', QRBC_PLUGIN_FILE_URL)) ?>"></script>
    <script>
        setTimeout(function () {
            table_pagination(<?php echo esc_html($items_count) ?>, <?php echo esc_html($num) ?>, <?php echo esc_html($pagination) ?>, "<?php _e('pages', 'qrbc'); ?>");
        }, 200)
        jQuery(function ($) {
            $('#from-date-input, #to-date-input').datepicker({
                dateFormat: "dd.mm.yy"
            });
        });
        jQuery('#export-button').click(function () {
            window.location.href = window.location.href + '&export=1'
        })
        <?php echo esc_html(@$_GET['export'] ? 'print();' : '') ?>

        function inactive_win_card(win_id) {
            let xhr = new XMLHttpRequest();
            xhr.open("POST", `${window.location.origin}/wp-admin/admin-ajax.php`, true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            let params = 'action=qrbc_inactive_qr_bonus_card_win&win_id=' + win_id;
            xhr.onreadystatechange = () => {

                if (xhr.readyState == 4) {
                    if (xhr.status == 200) {
                        if (xhr.responseText) {
                            swal(xhr.responseText, '', "success");
                            setTimeout(function () {
                                window.location.reload()
                            }, 2000)
                        }
                    } else {
                        console.log("SOME ERROR HTTP");
                        console.log(xhr.responseText);
                        swal(xhr.responseText, '', "error");
                    }
                    setTimeout(function () {
                        output_table_el.classList.remove('loading');
                        document.querySelector('.loading-area').remove();
                    }, 1500)
                }
            };

            xhr.send(params);
        }
    </script>
    <?php
}