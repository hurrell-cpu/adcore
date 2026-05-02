<?php

if (!defined('ABSPATH')) {
    exit;
}

final class AdCore_Ad_Meta_Box
{
    public static function add(): void
    {
        add_meta_box(
            'adcore_ad_settings',
            __('Ad Settings', 'adcore'),
            [self::class, 'render'],
            'adcore_ad',
            'normal',
            'high'
        );
    }

    public static function render(WP_Post $post): void
    {
        wp_nonce_field('adcore_save_ad_settings', 'adcore_ad_settings_nonce');

        $ad_type         = get_post_meta($post->ID, '_adcore_ad_type', true) ?: 'image';
        $image_url       = get_post_meta($post->ID, '_adcore_image_url', true);
        $html_code       = get_post_meta($post->ID, '_adcore_html_code', true);
        $destination_url = get_post_meta($post->ID, '_adcore_destination_url', true);
        $status          = get_post_meta($post->ID, '_adcore_status', true) ?: 'active';
        $start_date      = get_post_meta($post->ID, '_adcore_start_date', true);
        $end_date        = get_post_meta($post->ID, '_adcore_end_date', true);
        $ad_weight       = get_post_meta($post->ID, '_adcore_ad_weight', true) ?: 1;
        $frequency_cap   = get_post_meta($post->ID, '_adcore_frequency_cap', true) ?: 0;
        ?>

        <style>
            .adcore-field {
                margin-bottom: 18px;
            }

            .adcore-field label {
                display: block;
                font-weight: 600;
                margin-bottom: 6px;
            }

            .adcore-field input,
            .adcore-field select,
            .adcore-field textarea {
                width: 100%;
                max-width: 720px;
            }

            .adcore-help {
                color: #646970;
                font-size: 13px;
                margin-top: 4px;
            }
        </style>

        <div class="adcore-field">
            <label for="adcore_ad_type"><?php esc_html_e('Ad Type', 'adcore'); ?></label>
            <select id="adcore_ad_type" name="adcore_ad_type">
                <option value="image" <?php selected($ad_type, 'image'); ?>>Image</option>
                <option value="html" <?php selected($ad_type, 'html'); ?>>HTML</option>
                <option value="script" <?php selected($ad_type, 'script'); ?>>Script</option>
            </select>
        </div>

        <div class="adcore-field">
            <label for="adcore_ad_size"><?php esc_html_e('Ad Size', 'adcore'); ?></label>
            <select id="adcore_ad_size" name="adcore_ad_size">
                <option value="fluid" <?php selected($ad_size, 'fluid'); ?>>Fluid / Responsive</option>
                <option value="leaderboard" <?php selected($ad_size, 'leaderboard'); ?>>Leaderboard — 728x90</option>
                <option value="medium-rectangle" <?php selected($ad_size, 'medium-rectangle'); ?>>Medium Rectangle — 300x250</option>
                <option value="large-mobile" <?php selected($ad_size, 'large-mobile'); ?>>Large Mobile — 320x100</option>
            </select>
            <p class="adcore-help">Used to reserve space and reduce layout shift.</p>
        </div>

        <div class="adcore-field">
            <label for="adcore_image_url"><?php esc_html_e('Image URL', 'adcore'); ?></label>
            <input
                type="url"
                id="adcore_image_url"
                name="adcore_image_url"
                value="<?php echo esc_attr($image_url); ?>"
                placeholder="https://example.com/banner.jpg"
            />
        </div>

        <div class="adcore-field">
            <label for="adcore_html_code"><?php esc_html_e('HTML / Script Code', 'adcore'); ?></label>
            <textarea
                id="adcore_html_code"
                name="adcore_html_code"
                rows="8"
                placeholder="<div>Your ad code here</div>"
            ><?php echo esc_textarea($html_code); ?></textarea>
            <p class="adcore-help">Use this for HTML ads, AdSense snippets, or third-party ad scripts.</p>
        </div>

        <div class="adcore-field">
            <label for="adcore_destination_url"><?php esc_html_e('Destination URL', 'adcore'); ?></label>
            <input
                type="url"
                id="adcore_destination_url"
                name="adcore_destination_url"
                value="<?php echo esc_attr($destination_url); ?>"
                placeholder="https://example.com/landing-page"
            />
        </div>

        <div class="adcore-field">
            <label for="adcore_status"><?php esc_html_e('Status', 'adcore'); ?></label>
            <select id="adcore_status" name="adcore_status">
                <option value="active" <?php selected($status, 'active'); ?>>Active</option>
                <option value="paused" <?php selected($status, 'paused'); ?>>Paused</option>
            </select>
        </div>

        <div class="adcore-field">
    <label for="adcore_ad_weight"><?php esc_html_e('Rotation Weight', 'adcore'); ?></label>
    <input
        type="number"
        id="adcore_ad_weight"
        name="adcore_ad_weight"
        value="<?php echo esc_attr($ad_weight); ?>"
        min="1"
        max="100"
        step="1"
    />
    <p class="adcore-help">Higher numbers make this ad appear more often in rotating placements.</p>
</div>

<div class="adcore-field">
    <label for="adcore_frequency_cap"><?php esc_html_e('Frequency Cap', 'adcore'); ?></label>
    <input
        type="number"
        id="adcore_frequency_cap"
        name="adcore_frequency_cap"
        value="<?php echo esc_attr($frequency_cap); ?>"
        min="0"
        max="100"
        step="1"
    />
    <p class="adcore-help">Maximum times this ad can appear per visitor session. Use 0 for unlimited.</p>
</div>

        <div class="adcore-field">
            <label for="adcore_start_date"><?php esc_html_e('Start Date', 'adcore'); ?></label>
            <input
                type="date"
                id="adcore_start_date"
                name="adcore_start_date"
                value="<?php echo esc_attr($start_date); ?>"
            />
        </div>

        <div class="adcore-field">
            <label for="adcore_end_date"><?php esc_html_e('End Date', 'adcore'); ?></label>
            <input
                type="date"
                id="adcore_end_date"
                name="adcore_end_date"
                value="<?php echo esc_attr($end_date); ?>"
            />
        </div>

        <hr>

        <p>
            <strong>Impressions:</strong>
            <?php echo esc_html((int) get_post_meta($post->ID, '_adcore_impressions', true)); ?>
        </p>

        <p>
            <strong>Clicks:</strong>
            <?php echo esc_html((int) get_post_meta($post->ID, '_adcore_clicks', true)); ?>
        </p>

        <p>
            <strong>CTR:</strong>
            <?php
            $impressions = (int) get_post_meta($post->ID, '_adcore_impressions', true);
            $clicks = (int) get_post_meta($post->ID, '_adcore_clicks', true);

            $ctr = $impressions > 0 ? ($clicks / $impressions) * 100 : 0;

            echo esc_html(number_format($ctr, 2) . '%');
            ?>
        </p>

        <?php
        }

    public static function save(int $post_id): void
    {
        if (!isset($_POST['adcore_ad_settings_nonce'])) {
            return;
        }

        if (!wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST['adcore_ad_settings_nonce'])),
            'adcore_save_ad_settings'
        )) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $ad_type = isset($_POST['adcore_ad_type'])
            ? sanitize_key(wp_unslash($_POST['adcore_ad_type']))
            : 'image';

        if (!in_array($ad_type, ['image', 'html', 'script'], true)) {
            $ad_type = 'image';
        }

        $status = isset($_POST['adcore_status'])
            ? sanitize_key(wp_unslash($_POST['adcore_status']))
            : 'active';

        if (!in_array($status, ['active', 'paused'], true)) {
            $status = 'active';
        }

        update_post_meta($post_id, '_adcore_ad_type', $ad_type);

        $ad_size = isset($_POST['adcore_ad_size'])
            ? sanitize_key(wp_unslash($_POST['adcore_ad_size']))
            : 'fluid';

        if (!in_array($ad_size, ['fluid', 'leaderboard', 'medium-rectangle', 'large-mobile'], true)) {
            $ad_size = 'fluid';
        }

        update_post_meta($post_id, '_adcore_ad_size', $ad_size);

            $ad_weight = isset($_POST['adcore_ad_weight'])
            ? max(1, min(100, absint($_POST['adcore_ad_weight'])))
            : 1;

        update_post_meta($post_id, '_adcore_ad_weight', $ad_weight);

        $frequency_cap = isset($_POST['adcore_frequency_cap'])
            ? max(0, min(100, absint($_POST['adcore_frequency_cap'])))
            : 0;

        update_post_meta($post_id, '_adcore_frequency_cap', $frequency_cap);

        update_post_meta(
            $post_id,
            '_adcore_image_url',
            isset($_POST['adcore_image_url'])
                ? esc_url_raw(wp_unslash($_POST['adcore_image_url']))
                : ''
        );

        update_post_meta(
            $post_id,
            '_adcore_html_code',
            isset($_POST['adcore_html_code'])
                ? wp_kses_post(wp_unslash($_POST['adcore_html_code']))
                : ''
        );

        update_post_meta(
            $post_id,
            '_adcore_destination_url',
            isset($_POST['adcore_destination_url'])
                ? esc_url_raw(wp_unslash($_POST['adcore_destination_url']))
                : ''
        );

        update_post_meta($post_id, '_adcore_status', $status);

        update_post_meta(
            $post_id,
            '_adcore_start_date',
            isset($_POST['adcore_start_date'])
                ? sanitize_text_field(wp_unslash($_POST['adcore_start_date']))
                : ''
        );

        update_post_meta(
            $post_id,
            '_adcore_end_date',
            isset($_POST['adcore_end_date'])
                ? sanitize_text_field(wp_unslash($_POST['adcore_end_date']))
                : ''
        );
    }
}