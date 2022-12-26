<?php

add_action( 'admin_menu', 'add_qr_bonus_menu_to_admin' );

function add_qr_bonus_menu_to_admin() {
    add_menu_page( __('Bonus Cards'), __('Bonus Card'), 'manage_options', 'qr-bonus-card', 'qr_bonus_admin_page', 'dashicons-tickets-alt', 44  );
}

function qr_bonus_admin_page(){

    $checksum = get_option('qr_bonus_checksum');
    global $wpdb;
    $table_name = $wpdb->prefix . "qr_bonuss";
    $checksum_results = $wpdb->get_results("SELECT * FROM $table_name WHERE checksum = '{$checksum}' LIMIT 1");
    if($checksum_results){
        $checksum = uniqid();
        update_option( 'qr_bonus_checksum', $checksum );
    }
    ?>
    <div class="wrap">
        <div style="text-align: center"><img width="320" src="<?php echo site_url('/qr-bonus-generate?string=' . $checksum) ?>"></div>
        <button type="button" class="control-btn plus">+</button>
        <div class="input-number"><input type="number"></div>
        <button type="button" class="control-btn minus">-</button>
    </div>
    <?php

}