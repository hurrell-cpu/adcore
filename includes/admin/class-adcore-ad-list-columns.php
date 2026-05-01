<?php

if (!defined('ABSPATH')) {
    exit;
}

final class AdCore_Ad_List_Columns
{
    public static function init(): void
    {
        add_filter('manage_adcore_ad_posts_columns', [self::class, 'add_columns']);
        add_action('manage_adcore_ad_posts_custom_column', [self::class, 'render_columns'], 10, 2);
    }

    public static function add_columns(array $columns): array
    {
        $new_columns = [];

        foreach ($columns as $key => $label) {
            $new_columns[$key] = $label;

            if ($key === 'title') {
                $new_columns['adcore_status']      = __('Status', 'adcore');
                $new_columns['adcore_impressions'] = __('Impressions', 'adcore');
                $new_columns['adcore_clicks']      = __('Clicks', 'adcore');
                $new_columns['adcore_ctr']         = __('CTR', 'adcore');
            }
        }

        return $new_columns;
    }

    public static function render_columns(string $column, int $post_id): void
    {
        if ($column === 'adcore_status') {
            $status = get_post_meta($post_id, '_adcore_status', true) ?: 'active';

            echo esc_html(ucfirst($status));
        }

        if ($column === 'adcore_impressions') {
            echo esc_html(number_format_i18n((int) get_post_meta($post_id, '_adcore_impressions', true)));
        }

        if ($column === 'adcore_clicks') {
            echo esc_html(number_format_i18n((int) get_post_meta($post_id, '_adcore_clicks', true)));
        }

        if ($column === 'adcore_ctr') {
            $impressions = (int) get_post_meta($post_id, '_adcore_impressions', true);
            $clicks      = (int) get_post_meta($post_id, '_adcore_clicks', true);

            $ctr = $impressions > 0 ? ($clicks / $impressions) * 100 : 0;

            echo esc_html(number_format($ctr, 2) . '%');
        }
    }
}