<?php

if (!defined('ABSPATH')) {
    exit;
}

final class AdCore_Tracking
{
    public static function record_impression(int $ad_id): void
    {
        if (!$ad_id || get_post_type($ad_id) !== 'adcore_ad') {
            return;
        }

        $impressions = (int) get_post_meta($ad_id, '_adcore_impressions', true);
        $impressions++;

        update_post_meta($ad_id, '_adcore_impressions', $impressions);
    }

    public static function get_impressions(int $ad_id): int
    {
        return (int) get_post_meta($ad_id, '_adcore_impressions', true);
    }
}