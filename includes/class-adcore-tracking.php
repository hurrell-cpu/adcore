<?php

if (!defined('ABSPATH')) {
    exit;
}

final class AdCore_Tracking
{
    public static function init(): void
    {
        add_action('template_redirect', [self::class, 'handle_click_redirect']);

        add_action('wp_ajax_adcore_record_impression', [self::class, 'ajax_record_impression']);
        add_action('wp_ajax_nopriv_adcore_record_impression', [self::class, 'ajax_record_impression']);

        add_action('wp_enqueue_scripts', [self::class, 'enqueue_scripts']);

        add_action('admin_post_adcore_reset_stats', [self::class, 'handle_reset_stats']);
    }

   public static function enqueue_scripts(): void
{
    wp_enqueue_style(
        'adcore-frontend',
        ADCORE_PLUGIN_URL . 'assets/css/adcore-frontend.css',
        [],
        ADCORE_VERSION
    );

    wp_enqueue_script(
        'adcore-frontend',
        ADCORE_PLUGIN_URL . 'assets/js/adcore-frontend.js',
        [],
        ADCORE_VERSION,
        true
    );

    wp_localize_script('adcore-frontend', 'adcoreFrontend', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('adcore_impression_nonce'),
    ]);
}

    public static function ajax_record_impression(): void
    {
        check_ajax_referer('adcore_impression_nonce', 'nonce');

        $ad_id = isset($_POST['ad_id']) ? absint($_POST['ad_id']) : 0;

        if (!$ad_id || get_post_type($ad_id) !== 'adcore_ad') {
            wp_send_json_error([
                'message' => 'Invalid ad ID.',
            ]);
        }

        self::record_impression($ad_id);

        wp_send_json_success([
            'message' => 'Impression recorded.',
        ]);
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
   public static function handle_reset_stats(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to reset stats.', 'adcore'));
        }

        $ad_id = isset($_GET['ad_id']) ? absint($_GET['ad_id']) : 0;

        if (!$ad_id || get_post_type($ad_id) !== 'adcore_ad') {
            wp_die(__('Invalid ad.', 'adcore'));
        }

        check_admin_referer('adcore_reset_stats_' . $ad_id);

        delete_post_meta($ad_id, '_adcore_impressions');
        delete_post_meta($ad_id, '_adcore_clicks');

        wp_safe_redirect(get_edit_post_link($ad_id, 'raw'));
        exit;
    }
}