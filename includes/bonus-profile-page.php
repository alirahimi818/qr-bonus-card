<?php
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

$qrCodeBonus = new QrCodeBonus(@$_COOKIE["bonus_user"]);

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

get_header();

the_content('');

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
              <div class='text-center font-10'>" . __('Last scanned QR-Code at:', 'qrbc') . "
                <span class='text-green'>" . $qrCodeBonus->getLastBonusDate() . "</span>
              </div>
              <div class='text-center font-10'>" . __('Last win at:', 'qrbc') . "
                <span class='text-green'>" . $qrCodeBonus->getLastWinDate() . "</span>
              </div>
              <div class='text-center font-10'>" . __('Win count:', 'qrbc') . "
                <span class='text-green'>" . $qrCodeBonus->getWinCount() . " " . __('times', 'qrbc') . "</span>
              </div><br>";
echo $html;
get_footer();
?>