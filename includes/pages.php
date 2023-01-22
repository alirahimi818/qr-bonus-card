<?php

function qrbc_make_bonus_pages()
{
    $pages = [
        'qr-bonus-show' => [
            'title' => __('Bonus Page', 'qrbc'),
            'status' => 'private'
        ],
        'qr-bonus-generate' => [
            'title' => __('Bonus QR Generator', 'qrbc'),
            'status' => 'private'
        ],
        'qr-bonus-profile' => [
            'title' => __('Bonus Profile', 'qrbc'),
            'status' => 'publish'
        ]
    ];
    foreach ($pages as $slug => $option) {
        $args = array(
            'name' => $slug,
            'post_type' => 'page',
            'post_status' => $option['status'],
            'posts_per_page' => 1
        );
        if (!get_posts($args)) {
            $new_post = array(
                'post_title' => wp_strip_all_tags($option['title']),
                'post_name' => $slug,
                'post_content' => '',
                'post_status' => $option['status'],
                'post_author' => 1,
                'post_type' => 'page',
            );
            wp_insert_post($new_post);
        }
    }
}

register_activation_hook(QRBC_PLUGIN_FILE_URL, 'qrbc_make_bonus_pages');

add_action('wp', 'qrbc_redirect_private_page_to_login');
function qrbc_redirect_private_page_to_login()
{
    $queried_object = get_queried_object();
    if (isset($queried_object->post_status) && 'private' === $queried_object->post_status && !current_user_can('manage_options')) {
        wp_redirect(wp_login_url(get_permalink($queried_object->ID)));
    }
}

add_filter('page_template', 'qrbc_bonus_profile_page_template', 1);
function qrbc_bonus_profile_page_template($page_template)
{
    if (is_page('qr-bonus-profile')) {
        $page_template = QRBC_PLUGIN_BASE_URL . 'includes/bonus-profile-page.php';
    }
    return $page_template;
}

add_filter('page_template', 'qrbc_bonus_generate_page_template', 1);
function qrbc_bonus_generate_page_template($page_template)
{
    if (is_page('qr-bonus-show')) {
        $page_template = QRBC_PLUGIN_BASE_URL . 'includes/bonus-generate-page.php';
    }
    return $page_template;
}