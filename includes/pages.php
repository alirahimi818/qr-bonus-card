<?php

function make_bonus_pages()
{
    $pages = [
        'qr-bonus-show' => [
            'shortcode' => '[QR_BONUS_SHOW]',
            'title' => __('Bonus Page', 'qrbc'),
            'status' => 'private'
        ],
        'qr-bonus-generate' => [
            'shortcode' => '[QR_BONUS_GENERATE]',
            'title' => __('Bonus QR Generator', 'qrbc'),
            'status' => 'private'
        ],
        'qr-bonus-profile' => [
            'shortcode' => '[QR_BONUS_PROFILE]',
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
                'post_content' => $option['shortcode'],
                'post_status' => $option['status'],
                'post_author' => 1,
                'post_type' => 'page',
            );
            wp_insert_post($new_post);
        }
    }
}

register_activation_hook(PLUGIN_FILE_URL, 'make_bonus_pages');

add_action('wp', 'redirect_private_page_to_login');
function redirect_private_page_to_login()
{
    $queried_object = get_queried_object();
    if (isset($queried_object->post_status) && 'private' === $queried_object->post_status && !current_user_can('manage_options')) {
        wp_redirect(wp_login_url(get_permalink($queried_object->ID)));
    }
}

add_filter('page_template', 'qrbc_qr_code_generate_page_template');
function qrbc_qr_code_generate_page_template($page_template)
{
    if (is_page('qr-bonus-generate')) {
        $page_template = PLUGIN_BASE_URL . 'includes/generate-qr-code-page.php';
    }
    return $page_template;
}

add_filter('page_template', 'qrbc_bonus_profile_page_template');
function qrbc_bonus_profile_page_template($page_template)
{
    if (is_page('qr-bonus-profile')) {
        $page_template = PLUGIN_BASE_URL . 'includes/bonus-profile-page.php';
    }
    return $page_template;
}

add_filter('page_template', 'qrbc_bonus_generate_page_template');
function qrbc_bonus_generate_page_template($page_template)
{
    if (is_page('qr-bonus-show')) {
        $page_template = PLUGIN_BASE_URL . 'includes/bonus-generate-page.php';
    }
    return $page_template;
}