<?php
/**
 * Plugin Name: AdCore
 * Plugin URI: https://example.com/adcore
 * Description: A modern advertising management engine for WordPress.
 * Version: 0.1.0
 * Author: AdCore
 * Text Domain: adcore
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

define('ADCORE_VERSION', '0.1.0');
define('ADCORE_PLUGIN_FILE', __FILE__);
define('ADCORE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ADCORE_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once ADCORE_PLUGIN_DIR . 'includes/class-adcore.php';

function adcore(): AdCore {
    return AdCore::instance();
}

register_activation_hook(__FILE__, ['AdCore', 'activate']);
register_deactivation_hook(__FILE__, ['AdCore', 'deactivate']);

adcore();