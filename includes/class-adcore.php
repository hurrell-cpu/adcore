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
        require_once ADCORE_PLUGIN_DIR . 'includes/post-types/class-adcore-ad-post-type.php';
    }

    private function init_hooks(): void
    {
        add_action('init', [$this, 'register_post_types']);
    }

    public function register_post_types(): void
    {
        AdCore_Ad_Post_Type::register();
    }

    public static function activate(): void
    {
        require_once ADCORE_PLUGIN_DIR . 'includes/post-types/class-adcore-ad-post-type.php';

        AdCore_Ad_Post_Type::register();

        flush_rewrite_rules();
    }

    public static function deactivate(): void
    {
        flush_rewrite_rules();
    }
}