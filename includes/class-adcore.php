<?php

if (!defined('ABSPATH')) {
    exit;
}

final class AdCore
{
    private static ?AdCore $instance = null;

    public static function instance(): AdCore
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        $this->includes();
        $this->init_hooks();
    }

 private function includes(): void
{
    require_once ADCORE_PLUGIN_DIR . 'includes/class-adcore-tracking.php';
    require_once ADCORE_PLUGIN_DIR . 'includes/class-adcore-renderer.php';
    require_once ADCORE_PLUGIN_DIR . 'includes/class-adcore-content-inserter.php';

    require_once ADCORE_PLUGIN_DIR . 'includes/shortcodes/class-adcore-shortcodes.php';

    require_once ADCORE_PLUGIN_DIR . 'includes/post-types/class-adcore-ad-post-type.php';
    require_once ADCORE_PLUGIN_DIR . 'includes/post-types/class-adcore-placement-post-type.php';

    require_once ADCORE_PLUGIN_DIR . 'includes/meta-boxes/class-adcore-ad-meta-box.php';
    require_once ADCORE_PLUGIN_DIR . 'includes/meta-boxes/class-adcore-placement-meta-box.php';

    require_once ADCORE_PLUGIN_DIR . 'includes/admin/class-adcore-dashboard.php';

}

   private function init_hooks(): void
{
    add_action('init', [$this, 'register_post_types']);
    add_action('init', ['AdCore_Shortcodes', 'register']);
    add_action('init', ['AdCore_Tracking', 'init']);

    add_action('admin_menu', ['AdCore_Dashboard', 'register_menu']);

    add_action('add_meta_boxes_adcore_ad', ['AdCore_Ad_Meta_Box', 'add']);
    add_action('save_post_adcore_ad', ['AdCore_Ad_Meta_Box', 'save']);

    add_action('add_meta_boxes_adcore_placement', ['AdCore_Placement_Meta_Box', 'add']);
    add_action('save_post_adcore_placement', ['AdCore_Placement_Meta_Box', 'save']);

    add_action('wp', ['AdCore_Content_Inserter', 'init']);
}

    public function register_post_types(): void
    {
    AdCore_Ad_Post_Type::register();
    AdCore_Placement_Post_Type::register();
    }

    public static function activate(): void
    {
    require_once ADCORE_PLUGIN_DIR . 'includes/post-types/class-adcore-ad-post-type.php';
    require_once ADCORE_PLUGIN_DIR . 'includes/post-types/class-adcore-placement-post-type.php';

    AdCore_Ad_Post_Type::register();
    AdCore_Placement_Post_Type::register();

    flush_rewrite_rules();
    }

    public static function deactivate(): void
    {
        flush_rewrite_rules();
    }
}