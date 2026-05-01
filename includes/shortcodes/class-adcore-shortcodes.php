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

        if (!self::placement_matches_targeting($placement_id)) {
            return '';
        }

        $ad_ids = self::get_placement_ad_ids($placement_id);

        if (empty($ad_ids)) {
            return '';
        }

        shuffle($ad_ids);

        foreach ($ad_ids as $ad_id) {
            $output = AdCore_Renderer::render((int) $ad_id);

            if ($output) {
                return $output;
            }
        }

        return '';
    }

    private static function get_placement_ad_ids(int $placement_id): array
    {
        $ad_ids = get_post_meta($placement_id, '_adcore_placement_ad_ids', true);

        if (is_array($ad_ids)) {
            return array_values(array_filter(array_map('absint', $ad_ids)));
        }

        $legacy_ad_id = (int) get_post_meta($placement_id, '_adcore_placement_ad_id', true);

        return $legacy_ad_id ? [$legacy_ad_id] : [];
    }

    private static function placement_matches_targeting(int $placement_id): bool
    {
        $device_target    = get_post_meta($placement_id, '_adcore_device_target', true) ?: 'all';
        $post_type_target = get_post_meta($placement_id, '_adcore_post_type_target', true) ?: 'all';

        if ($device_target === 'mobile' && !wp_is_mobile()) {
            return false;
        }

        if ($device_target === 'desktop' && wp_is_mobile()) {
            return false;
        }

        if ($post_type_target !== 'all' && get_post_type() !== $post_type_target) {
            return false;
        }

        return true;
    }
}