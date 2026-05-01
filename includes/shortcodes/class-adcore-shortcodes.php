<?php

if (!defined('ABSPATH')) {
    exit;
}

final class AdCore_Shortcodes
{
    public static function register(): void
    {
        add_shortcode('adcore', [self::class, 'render_ad']);
        add_shortcode('adcore_placement', [self::class, 'render_placement']);
    }

    public static function render_ad(array $atts): string
    {
        $atts = shortcode_atts([
            'id' => 0,
        ], $atts, 'adcore');

        return AdCore_Renderer::render((int) $atts['id']);
    }

    public static function render_placement(array $atts): string
    {
        $atts = shortcode_atts([
            'id' => 0,
        ], $atts, 'adcore_placement');

        $placement_id = (int) $atts['id'];

        if (!$placement_id || get_post_type($placement_id) !== 'adcore_placement') {
            return '';
        }

        $ad_id = (int) get_post_meta($placement_id, '_adcore_placement_ad_id', true);

        if (!$ad_id) {
            return '';
        }

        return AdCore_Renderer::render($ad_id);
    }
}