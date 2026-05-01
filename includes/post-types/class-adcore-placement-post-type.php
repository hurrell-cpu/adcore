<?php

if (!defined('ABSPATH')) {
    exit;
}

final class AdCore_Placement_Post_Type
{
    public const POST_TYPE = 'adcore_placement';

    public static function register(): void
    {
        $labels = [
            'name'               => __('Placements', 'adcore'),
            'singular_name'      => __('Placement', 'adcore'),
            'menu_name'          => __('Placements', 'adcore'),
            'add_new_item'       => __('Add New Placement', 'adcore'),
            'edit_item'          => __('Edit Placement', 'adcore'),
            'all_items'          => __('Placements', 'adcore'),
            'search_items'       => __('Search Placements', 'adcore'),
            'not_found'          => __('No placements found.', 'adcore'),
        ];

        $args = [
            'labels'             => $labels,
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => 'edit.php?post_type=adcore_ad',
            'query_var'          => false,
            'rewrite'            => false,
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'supports'           => ['title'],
            'show_in_rest'       => true,
        ];

        register_post_type(self::POST_TYPE, $args);
    }
}