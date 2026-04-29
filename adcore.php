<?php
/**
 * Plugin Name: AdCore
 * Plugin URI: https://example.com/adcore
 * Description: A modern advertising management engine for WordPress.
 * Version: 0.1.0
 * Author: AdCore
 * Text Domain: adcore
 */

if (!defined('ABSPATH')) {
    exit;
}

define('ADCORE_VERSION', '0.1.0');
define('ADCORE_PLUGIN_FILE', __FILE__);
define('ADCORE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ADCORE_PLUGIN_URL', plugin_dir_url(__FILE__));

function adcore_register_ad_post_type() {
    register_post_type('adcore_ad', [
        'labels' => [
            'name' => 'Ads',
            'singular_name' => 'Ad',
            'add_new_item' => 'Add New Ad',
            'edit_item' => 'Edit Ad',
        ],
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-megaphone',
        'supports' => ['title'],
        'capability_type' => 'post',
    ]);
}
add_action('init', 'adcore_register_ad_post_type');