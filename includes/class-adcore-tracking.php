<?php

if (!defined('ABSPATH')) {
    exit;
}

final class AdCore_Tracking
{
    public static function init(): void
    {
        add_action('template_redirect', [self::class, 'handle_click_redirect']);
    }

    public static function record_impression(int $ad_id): void
    {
        if (!$ad_id || get_post_type($ad_id) !== 'adcore_ad') {
            return;
        }

        $impressions = (int) get_post_meta($ad_id, '_adcore_impressions', true);
        update_post_meta($ad_id, '_adcore_impressions', $impressions + 1);
    }

    public static function record_click(int $ad_id): void
    {
        if (!$ad_id || get_post_type($ad_id) !== 'adcore_ad') {
            return;
        }

        $clicks = (int) get_post_meta($ad_id, '_adcore_clicks', true);
        update_post_meta($ad_id, '_adcore_clicks', $clicks + 1);
    }

    public static function get_impressions(int $ad_id): int
    {
        return (int) get_post_meta($ad_id, '_adcore_impressions', true);
    }

    public static function get_clicks(int $ad_id): int
    {
        return (int) get_post_meta($ad_id, '_adcore_clicks', true);
    }

    public static function get_tracking_url(int $ad_id): string
    {
        return add_query_arg(
            'adcore_click',
            absint($ad_id),
            home_url('/')
        );
    }

    public static function handle_click_redirect(): void
    {
        if (!isset($_GET['adcore_click'])) {
            return;
        }

        $ad_id = absint($_GET['adcore_click']);

        if (!$ad_id || get_post_type($ad_id) !== 'adcore_ad') {
            wp_safe_redirect(home_url('/'));
            exit;
        }

        $destination_url = get_post_meta($ad_id, '_adcore_destination_url', true);

        if (!$destination_url) {
            wp_safe_redirect(home_url('/'));
            exit;
        }

        self::record_click($ad_id);

        wp_redirect(esc_url_raw($destination_url));
        exit;
    }
}