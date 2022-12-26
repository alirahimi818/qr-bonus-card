<?php

add_action('wp', 'redirect_private_page_to_login');
function redirect_private_page_to_login()
{
    $queried_object = get_queried_object();
    if (isset($queried_object->post_status) && 'private' === $queried_object->post_status && !current_user_can('manage_options')) {
        wp_redirect(wp_login_url(get_permalink($queried_object->ID)));
    }
}

add_action("wp_ajax_generate_qr_bonus_card", "generate_qr_bonus_card");
add_action("wp_ajax_nopriv_generate_qr_bonus_card", "generate_qr_bonus_card");
function generate_qr_bonus_card()
{
    if (current_user_can('manage_options')) {
        $count = @$_REQUEST['count'];
        if (!$count or $count < 1) {
            echo plugins_url('/assets/error-qr.png', PLUGIN_FILE_URL);
        } else {
            $checksum_with_count = uniqid() . '--' . $count;
            update_option('qr_bonus_checksum', $checksum_with_count);
            $return_url = site_url('/qr-bonus-generate/?string=' . $checksum_with_count);
            echo $return_url;
        }
    }
    wp_die();
}

function qr_bonus_show_page_func($atts)
{
    if (current_user_can('manage_options')) {
        wp_enqueue_style('new_style', plugins_url('/assets/style.css', PLUGIN_FILE_URL), false, '1.0', 'all');
        wp_enqueue_script('new_script', plugins_url('/assets/generate.js', PLUGIN_FILE_URL), false, '1.0', 'all');

        $checksum_with_count = uniqid() . '--1';
        update_option('qr_bonus_checksum', $checksum_with_count);

        $html = "";
        $html .= "<div class='qr-generate-page'>
                    <div class='barcode-area'><img width='320' src='" . site_url('/qr-bonus-generate/?string=' . $checksum_with_count) . "'></div>
                    <div class='qr-control-area'>
                        <button type='button' class='control-btn minus'>-</button>
                        <input type='number' class='input-number' min='1' value='1' autocomplete='off' aria-autocomplete='off'>
                        <button type='button' class='control-btn plus'>+</button>
                    </div>
                    <div class='qr-control-area'>
                        <button type='button' class='new-qr-btn'>" . __('New QR-Code', 'qrdc') . "</button>
                    </div>
                  </div>
        ";
        return $html;
    }
    wp_redirect(wp_login_url(site_url('/qr-bonus-show/')));
}

add_shortcode('QR_BONUS_SHOW', 'qr_bonus_show_page_func');

function qr_bonus_generate_page_func($atts)
{
    if (@$_GET['string']) {
        include(plugin_dir_path(PLUGIN_FILE_URL) . '/includes/phpqrcode/qrlib.php');
        $url = site_url('/qr-bonus-profile/?checksum=' . $_GET['string']);
        if (@$_GET['count']) {
            $url .= '--' . $_GET['count'];
        }
        return QRcode::png($url);
    }
}

add_shortcode('QR_bonus_GENERATE', 'qr_bonus_generate_page_func');


function qr_bonus_profile_page_func($atts)
{
    $qrCodeBonus = new QrCodeBonus(@$_COOKIE["bonus_user"]);
    $qrCodeBonus->createBonusWin();
    if (@$_GET['checksum']) {
        $checksum = $_GET['checksum'];
        $option_checksum = get_option('qr_bonus_checksum');
        if ($checksum == $option_checksum) {
            $create_bonus = $qrCodeBonus->createbonus($checksum);
            if ($create_bonus['status']) {
                setcookie('qr_bonus_response_status', 'success', time() + (86400 * 30), "/");
            } else {
                setcookie('qr_bonus_response_status', 'failed', time() + (86400 * 30), "/");
            }
            setcookie('qr_bonus_response_message', $create_bonus['message'], time() + (86400 * 30), "/");
            wp_redirect(get_site_url() . '/qr-bonus-profile');
        }
    }

    wp_enqueue_style('new_style', plugins_url('/assets/style.css', PLUGIN_FILE_URL), false, '1.0', 'all');
    $html = qr_bonus_cookie_message();
    $html .= "<div class='bonus-cart'>";
    $default_win_count = get_option('qr_bonus_win_count');
    $background_deactivate_img_url = get_option('qr_bonus_card_deactivate_img_url');
    $background_active_img_url = get_option('qr_bonus_card_active_img_url');
    $bonuses = $qrCodeBonus->getActiveBonuses();
    foreach ($bonuses as $bonus) {
        $html .= "<span class='bonus-cart-item active'><span class='bg-img' style='background-image: url(" . $background_active_img_url . ")'></span></span>";
    }

    $number_to_win = (int)$default_win_count - count($bonuses);
    for ($i = 1; $i <= $number_to_win; $i++) {
        $html .= "<span class='bonus-cart-item'><span class='bg-img' style='background-image: url(" . $background_deactivate_img_url . ")'></span></span>";
    }

    $html .= "</div>
              <div class='text-center font-10'>" . __('Last scanned coupon at:', 'qrdc') . "
                <span class='text-green'>" . $qrCodeBonus->getLastBonusDate() . "</span>
              </div>
              <div class='text-center font-10'>" . __('Last win at:', 'qrdc') . "
                <span class='text-green'>" . $qrCodeBonus->getLastWinDate() . "</span>
              </div>
              <div class='text-center font-10'>" . __('Win count:', 'qrdc') . "
                <span class='text-green'>" . $qrCodeBonus->getWinCount() . " " . __('times', 'qrdc') . "</span>
              </div>";
    return $html;
}

add_shortcode('QR_BONUS_PROFILE', 'qr_bonus_profile_page_func');

function qr_bonus_cookie_message()
{
    $html = "";
    if (@$_COOKIE['qr_bonus_response_status'] and @$_COOKIE['qr_bonus_response_message']) {
        if ($_COOKIE['qr_bonus_response_status'] == 'success') {
            $html .= "<div class='success-color'>" . $_COOKIE['qr_bonus_response_message'] . "</div>";
        } else {
            $html .= "<div class='failed-color'>" . $_COOKIE['qr_bonus_response_message'] . "</div>";
        }
        unset($_COOKIE['qr_bonus_response_status']);
        unset($_COOKIE['qr_bonus_response_message']);
        setcookie('qr_bonus_response_status', null, -1, '/');
        setcookie('qr_bonus_response_message', null, -1, '/');
    }
    return $html;
}