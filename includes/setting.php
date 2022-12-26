<?php

function qr_bonus_card_plugin_register_settings()
{
    register_setting('qr_bonus_card_plugin_options_group', 'qr_bonus_win_count', ['type' => 'number']);
    register_setting('qr_bonus_card_plugin_options_group', 'qr_bonus_date_format', ['type' => 'string']);
    register_setting('qr_bonus_card_plugin_options_group', 'qr_bonus_card_deactivate_img_url', ['type' => 'string']);
    register_setting('qr_bonus_card_plugin_options_group', 'qr_bonus_card_active_img_url', ['type' => 'string']);

}

add_action('admin_init', 'qr_bonus_card_plugin_register_settings');

function qr_bonus_card_plugin_setting_page()
{
    add_submenu_page('qr-bonus-card', 'QR Bonus Card Setting', 'Setting', 'manage_options', 'qr-bonus-card/setting', 'qr_bonus_card_plugin_setting_form');
}

add_action('admin_menu', 'qr_bonus_card_plugin_setting_page');

function qr_bonus_card_plugin_setting_form()
{
    ?>
    <div class="wrap">
        <h2><?php _e("QR Bonus Card Setting", "qrbc") ?></h2>
        <form method="post" action="options.php">
            <?php settings_fields('qr_bonus_card_plugin_options_group'); ?>

            <table class="form-table">
                <tr>
                    <th><label for="qr_bonus_win_count"><?php _e("Number of wins", "qrbc") ?></label></th>
                    <td>
                        <input type='number' class="regular-text" id="qr_bonus_win_count"
                               name="qr_bonus_win_count" value="<?php echo get_option('qr_bonus_win_count'); ?>">
                        <div><?php _e("After this number, the user bonus card is reset.", "qrbc") ?></div>
                    </td>
                </tr>
                <tr>
                    <th><label for="qr_bonus_date_format"><?php _e("Date Format", "qrbc") ?></label></th>
                    <td>
                        <input type='text' class="regular-text" id="qr_bonus_date_format"
                               name="qr_bonus_date_format"
                               value="<?php echo get_option('qr_bonus_date_format'); ?>">
                    </td>
                </tr>
                <tr style="border-top: 1px solid #ccc;">
                    <th>
                        <label for="qr_bonus_card_deactivate_img_url"><?php _e("Card deactivate item image url", "qrbc") ?></label>
                    </th>
                    <td>
                        <input type='text' class="regular-text" id="qr_bonus_card_deactivate_img_url"
                               name="qr_bonus_card_deactivate_img_url"
                               value="<?php echo get_option('qr_bonus_card_deactivate_img_url'); ?>">
                        <div><?php _e("best size 80x80 pixel", "qrbc") ?></div>
                    </td>
                </tr>
                <tr>
                    <th><label for="qr_bonus_card_active_img_url"><?php _e("Card active item image url", "qrbc") ?></label>
                    </th>
                    <td>
                        <input type='text' class="regular-text" id="qr_bonus_card_active_img_url"
                               name="qr_bonus_card_active_img_url"
                               value="<?php echo get_option('qr_bonus_card_active_img_url'); ?>">
                        <div><?php _e("best size 80x80 pixel", "qrbc") ?></div>
                    </td>
                </tr>

            </table>

            <?php submit_button(); ?>

    </div>
<?php } ?>