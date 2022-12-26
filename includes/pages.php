<?php

function make_bonus_pages() {
    $pages = [
      'qr-bonus-show' => [
        'shortcode' => '[QR_BONUS_SHOW]',
        'title' => __('bonus Page', 'qrbc'),
        'status' => 'private'
      ],
      'qr-bonus-generate' => [
        'shortcode' => '[QR_BONUS_GENERATE]',
        'title' => __('bonus QR Generator', 'qrbc'),
        'status' => 'private'
      ],
      'qr-bonus-profile' => [
        'shortcode' => '[QR_BONUS_PROFILE]',
        'title' => __('bonus Profile', 'qrbc'),
        'status' => 'publish'
      ]
    ];
    foreach($pages as $slug => $option){
      $args = array(
        'name'   => $slug,
        'post_type'   => 'page',
        'post_status' => $option['status'],
        'posts_per_page' => 1
      );
      if( !get_posts($args) ){
            $new_post = array(
            'post_title'    => wp_strip_all_tags( $option['title'] ),
            'post_name'  => $slug,
            'post_content'  => $option['shortcode'],
            'post_status'   => $option['status'],
            'post_author'   => 1,
            'post_type'     => 'page',
            );
            wp_insert_post( $new_post );
      }
    }
}

register_activation_hook(PLUGIN_FILE_URL, 'make_bonus_pages');