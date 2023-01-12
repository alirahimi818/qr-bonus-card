<?php

function qrbc_qr_bonus_card_plugin_register_settings()
{
    register_setting('qr_bonus_card_plugin_options_group', 'qr_bonus_win_count', ['type' => 'number']);
    register_setting('qr_bonus_card_plugin_options_group', 'qr_bonus_date_format', ['type' => 'string']);
    register_setting('qr_bonus_card_plugin_options_group', 'qr_bonus_card_deactivate_img_url', ['type' => 'string']);
    register_setting('qr_bonus_card_plugin_options_group', 'qr_bonus_card_active_img_url', ['type' => 'string']);
    register_setting('qr_bonus_card_plugin_options_group', 'qr_bonus_card_active_favicon_img', ['type' => 'image']);

}

add_action('admin_init', 'qrbc_qr_bonus_card_plugin_register_settings');

function qrbc_qr_bonus_card_plugin_setting_page()
{
    add_submenu_page('qr-bonus-card', 'QR Bonus Card Setting', 'Setting', 'manage_options', 'qr-bonus-card/setting', 'qrbc_qr_bonus_card_plugin_setting_form');
}

add_action('admin_menu', 'qrbc_qr_bonus_card_plugin_setting_page');

function qrbc_qr_bonus_card_plugin_setting_form()
{
    if (!did_action('wp_enqueue_media')) {
        wp_enqueue_media();
    }
    $image_id = get_option('qr_bonus_card_active_favicon_img');
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
                               name="qr_bonus_win_count" value="<?php echo esc_html(get_option('qr_bonus_win_count')) ?>">
                        <div><?php _e("After this number, the user bonus card is reset.", "qrbc") ?></div>
                    </td>
                </tr>
                <tr>
                    <th><label for="qr_bonus_date_format"><?php _e("Date Format", "qrbc") ?></label></th>
                    <td>
                        <input type='text' class="regular-text" id="qr_bonus_date_format"
                               name="qr_bonus_date_format"
                               value="<?php echo esc_html(get_option('qr_bonus_date_format')) ?>">
                    </td>
                </tr>
                <tr style="border-top: 1px solid #ccc;">
                    <th>
                        <label for="qr_bonus_card_deactivate_img_url"><?php _e("Card deactivate item image url", "qrbc") ?></label>
                    </th>
                    <td>
                        <input type='text' class="regular-text" id="qr_bonus_card_deactivate_img_url"
                               name="qr_bonus_card_deactivate_img_url"
                               value="<?php echo esc_html(get_option('qr_bonus_card_deactivate_img_url')) ?>">
                        <div><?php _e("best size 80x80 pixel", "qrbc") ?></div>
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="qr_bonus_card_active_img_url"><?php _e("Card active item image url", "qrbc") ?></label>
                    </th>
                    <td>
                        <input type='text' class="regular-text" id="qr_bonus_card_active_img_url"
                               name="qr_bonus_card_active_img_url"
                               value="<?php echo esc_html(get_option('qr_bonus_card_active_img_url')) ?>">
                        <div><?php _e("best size 80x80 pixel", "qrbc") ?></div>
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="qr_bonus_card_active_favicon_img"><?php _e("Favicon image url (for PWA)", "qrbc") ?></label>
                    </th>
                    <td>
                        <?php if ($image = wp_get_attachment_image_url($image_id, 'medium')) : ?>
                            <a href="#" class="rudr-upload">
                                <img src="<?php echo esc_url($image) ?>" width="150px"/>
                            </a>
                            <a href="#" class="rudr-remove"><?php _e("Remove image", "qrbc") ?></a>
                            <input type="hidden" name="qr_bonus_card_active_favicon_img"
                                   value="<?php echo absint($image_id) ?>">
                        <?php else : ?>
                            <a href="#" class="button rudr-upload"><?php _e("Upload image", "qrbc") ?></a>
                            <a href="#" class="rudr-remove" style="display:none"><?php _e("Remove image", "qrbc") ?></a>
                            <input type="hidden" name="qr_bonus_card_active_favicon_img" value="">
                        <?php endif; ?>
                        <div><?php _e("best size 80x80 pixel", "qrbc") ?></div>
                    </td>
                </tr>

            </table>

            <?php submit_button(); ?>

            <script>
                jQuery(function ($) {
                    // on upload button click
                    $('body').on('click', '.rudr-upload', function (event) {
                        event.preventDefault(); // prevent default link click and page refresh

                        const button = $(this)
                        const imageId = button.next().next().val();

                        const customUploader = wp.media({
                            title: '<?php _e("Insert image", "qrbc") ?>', // modal window title
                            library: {
                                // uploadedTo : wp.media.view.settings.post.id, // attach to the current post?
                                type: 'image'
                            },
                            button: {
                                text: '<?php _e("Use this image", "qrbc") ?>' // button label text
                            },
                            multiple: false
                        }).on('select', function () { // it also has "open" and "close" events
                            const attachment = customUploader.state().get('selection').first().toJSON();
                            button.removeClass('button').html('<img src="' + attachment.url + '" width="150px">'); // add image instead of "Upload Image"
                            button.next().show(); // show "Remove image" link
                            button.next().next().val(attachment.id); // Populate the hidden field with image ID
                        })

                        // already selected images
                        customUploader.on('open', function () {

                            if (imageId) {
                                const selection = customUploader.state().get('selection')
                                attachment = wp.media.attachment(imageId);
                                attachment.fetch();
                                selection.add(attachment ? [attachment] : []);
                            }

                        })

                        customUploader.open()

                    });
                    // on remove button click
                    $('body').on('click', '.rudr-remove', function (event) {
                        event.preventDefault();
                        const button = $(this);
                        button.next().val(''); // emptying the hidden field
                        button.hide().prev().addClass('button').html('<?php _e("Upload image", "qrbc") ?>'); // replace the image with text
                    });
                });
            </script>
    </div>
<?php } ?>