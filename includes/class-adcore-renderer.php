<?php

if (!defined('ABSPATH')) {
    exit;
}

final class AdCore_Renderer
{
    public static function render(int $ad_id): string
    {
        if (!$ad_id || get_post_type($ad_id) !== 'adcore_ad') {
            return '';
        }

        if (get_post_status($ad_id) !== 'publish') {
            return '';
        }

        if (!self::is_active($ad_id)) {
            return '';
        }

        $type            = get_post_meta($ad_id, '_adcore_ad_type', true) ?: 'image';
        $ad_size         = get_post_meta($ad_id, '_adcore_ad_size', true) ?: 'fluid';
        $image_url       = get_post_meta($ad_id, '_adcore_image_url', true);
        $html_code       = get_post_meta($ad_id, '_adcore_html_code', true);
        $destination_url = get_post_meta($ad_id, '_adcore_destination_url', true);

        $output = '';

        if ($type === 'image') {
            $output = self::render_image_ad($image_url, $destination_url, $ad_id);
        }

        if ($type === 'html' || $type === 'script') {
            $output = self::render_html_ad($html_code, $ad_id);
        }

        if (!$output) {
    return '';
        }

        $allowed_sizes = ['fluid', 'leaderboard', 'medium-rectangle', 'large-mobile'];

if (!in_array($ad_size, $allowed_sizes, true)) {
    $ad_size = 'fluid';
}

return sprintf(
    '<div class="adcore-ad adcore-ad-%d adcore-ad-size-%s" data-adcore-ad-id="%d"><div class="adcore-ad-inner">%s</div></div>',
    esc_attr($ad_id),
    esc_attr($ad_size),
    esc_attr($ad_id),
    $output
);
    }

    private static function render_image_ad(string $image_url, string $destination_url, int $ad_id): string
    {
        if (!$image_url) {
            return '';
        }

        $image = sprintf(
            '<img src="%s" alt="%s" loading="lazy" style="max-width:100%%;height:auto;" />',
            esc_url($image_url),
            esc_attr(get_the_title($ad_id))
        );

        if (!$destination_url) {
            return $image;
        }

        $tracking_url = AdCore_Tracking::get_tracking_url($ad_id);

        return sprintf(
            '<a href="%s" target="_blank" rel="nofollow sponsored noopener">%s</a>',
            esc_url($tracking_url),
            $image
        );
    }

    private static function render_html_ad(string $html_code, int $ad_id): string
    {
        if (!$html_code) {
            return '';
        }

        return wp_kses_post($html_code);
    }

    private static function is_active(int $ad_id): bool
    {
        $status     = get_post_meta($ad_id, '_adcore_status', true) ?: 'active';
        $start_date = get_post_meta($ad_id, '_adcore_start_date', true);
        $end_date   = get_post_meta($ad_id, '_adcore_end_date', true);

        if ($status !== 'active') {
            return false;
        }

        $today = current_time('Y-m-d');

        if ($start_date && $today < $start_date) {
            return false;
        }

        if ($end_date && $today > $end_date) {
            return false;
        }

        return true;
    }
}