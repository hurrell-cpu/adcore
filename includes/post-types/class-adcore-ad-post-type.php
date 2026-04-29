<?php

if (!defined('ABSPATH')) {
    exit;
}

final class AdCore_Ad_Post_Type
{
    public const POST_TYPE = 'adcore_ad';

    public static function register(): void
    {
        $labels = [
            'name'               => __('Ads', 'adcore'),
            'singular_name'      => __('Ad', 'adcore'),
            'menu_name'          => __('AdCore', 'adcore'),
            'name_admin_bar'     => __('Ad', 'adcore'),
            'add_new'            => __('Add New', 'adcore'),
            'add_new_item'       => __('Add New Ad', 'adcore'),
            'new_item'           => __('New Ad', 'adcore'),
            'edit_item'          => __('Edit Ad', 'adcore'),
            'view_item'          => __('View Ad', 'adcore'),
            'all_items'          => __('Ads', 'adcore'),
            'search_items'       => __('Search Ads', 'adcore'),
            'not_found'          => __('No ads found.', 'adcore'),
            'not_found_in_trash' => __('No ads found in Trash.', 'adcore'),
        ];

        $args = [
            'labels'              => $labels,
            'public'              => false,
            'publicly_queryable'  => false,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'query_var'           => false,
            'rewrite'             => false,
            'capability_type'     => 'post',
            'has_archive'         => false,
            'hierarchical'        => false,
            'menu_position'       => 25,
            'menu_icon'           => 'dashicons-megaphone',
            'supports'            => ['title'],
            'show_in_rest'        => true,
        ];

        register_post_type(self::POST_TYPE, $args);
    }
}