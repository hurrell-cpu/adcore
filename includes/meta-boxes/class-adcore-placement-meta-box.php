<?php

if (!defined('ABSPATH')) {
    exit;
}

final class AdCore_Placement_Meta_Box
{
    public static function add(): void
    {
        add_meta_box(
            'adcore_placement_settings',
            __('Placement Settings', 'adcore'),
            [self::class, 'render'],
            'adcore_placement',
            'normal',
            'high'
        );
    }

    public static function render(WP_Post $post): void
    {
        wp_nonce_field('adcore_save_placement_settings', 'adcore_placement_settings_nonce');

        $selected_ad_id   = (int) get_post_meta($post->ID, '_adcore_placement_ad_id', true);
        $auto_insert      = get_post_meta($post->ID, '_adcore_auto_insert', true) ?: 'no';
        $insert_position  = get_post_meta($post->ID, '_adcore_insert_position', true) ?: 'after_paragraph';
        $paragraph_number = get_post_meta($post->ID, '_adcore_paragraph_number', true) ?: 2;
        $device_target    = get_post_meta($post->ID, '_adcore_device_target', true) ?: 'all';
        $post_type_target = get_post_meta($post->ID, '_adcore_post_type_target', true) ?: 'all';

        $ads = get_posts([
            'post_type'      => 'adcore_ad',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'title',
            'order'          => 'ASC',
        ]);
        ?>

        <p>
            <label for="adcore_placement_ad_id"><strong><?php esc_html_e('Ad to Display', 'adcore'); ?></strong></label>
            <br><br>

            <select id="adcore_placement_ad_id" name="adcore_placement_ad_id" style="width:100%;max-width:720px;">
                <option value="0"><?php esc_html_e('Select an ad', 'adcore'); ?></option>

                <?php foreach ($ads as $ad): ?>
                    <option value="<?php echo esc_attr($ad->ID); ?>" <?php selected($selected_ad_id, $ad->ID); ?>>
                        <?php echo esc_html($ad->post_title); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>

        <hr>

        <p>
            <label>
                <input
                    type="checkbox"
                    name="adcore_auto_insert"
                    value="yes"
                    <?php checked($auto_insert, 'yes'); ?>
                >
                <strong><?php esc_html_e('Auto-insert this placement into content', 'adcore'); ?></strong>
            </label>
        </p>

        <p>
            <label for="adcore_insert_position"><strong><?php esc_html_e('Insert Position', 'adcore'); ?></strong></label>
            <br><br>

            <select id="adcore_insert_position" name="adcore_insert_position" style="width:100%;max-width:720px;">
                <option value="before_content" <?php selected($insert_position, 'before_content'); ?>>Before content</option>
                <option value="after_paragraph" <?php selected($insert_position, 'after_paragraph'); ?>>After paragraph</option>
                <option value="after_content" <?php selected($insert_position, 'after_content'); ?>>After content</option>
            </select>
        </p>

        <p>
            <label for="adcore_paragraph_number"><strong><?php esc_html_e('Paragraph Number', 'adcore'); ?></strong></label>
            <br><br>

            <input
                type="number"
                id="adcore_paragraph_number"
                name="adcore_paragraph_number"
                value="<?php echo esc_attr($paragraph_number); ?>"
                min="1"
                style="width:120px;"
            >
        </p>

        <hr>

        <p>
            <label for="adcore_device_target"><strong><?php esc_html_e('Device Targeting', 'adcore'); ?></strong></label>
            <br><br>

            <select id="adcore_device_target" name="adcore_device_target" style="width:100%;max-width:720px;">
                <option value="all" <?php selected($device_target, 'all'); ?>>All devices</option>
                <option value="desktop" <?php selected($device_target, 'desktop'); ?>>Desktop only</option>
                <option value="mobile" <?php selected($device_target, 'mobile'); ?>>Mobile only</option>
            </select>
        </p>

        <p>
            <label for="adcore_post_type_target"><strong><?php esc_html_e('Post Type Targeting', 'adcore'); ?></strong></label>
            <br><br>

            <select id="adcore_post_type_target" name="adcore_post_type_target" style="width:100%;max-width:720px;">
                <option value="all" <?php selected($post_type_target, 'all'); ?>>Posts and pages</option>
                <option value="post" <?php selected($post_type_target, 'post'); ?>>Posts only</option>
                <option value="page" <?php selected($post_type_target, 'page'); ?>>Pages only</option>
            </select>
        </p>

        <?php
    }

    public static function save(int $post_id): void
    {
        if (!isset($_POST['adcore_placement_settings_nonce'])) {
            return;
        }

        if (!wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST['adcore_placement_settings_nonce'])),
            'adcore_save_placement_settings'
        )) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $ad_id = isset($_POST['adcore_placement_ad_id'])
            ? absint($_POST['adcore_placement_ad_id'])
            : 0;

        $auto_insert = isset($_POST['adcore_auto_insert']) ? 'yes' : 'no';

        $insert_position = isset($_POST['adcore_insert_position'])
            ? sanitize_key(wp_unslash($_POST['adcore_insert_position']))
            : 'after_paragraph';

        if (!in_array($insert_position, ['before_content', 'after_paragraph', 'after_content'], true)) {
            $insert_position = 'after_paragraph';
        }

        $paragraph_number = isset($_POST['adcore_paragraph_number'])
            ? max(1, absint($_POST['adcore_paragraph_number']))
            : 2;

        $device_target = isset($_POST['adcore_device_target'])
            ? sanitize_key(wp_unslash($_POST['adcore_device_target']))
            : 'all';

        if (!in_array($device_target, ['all', 'desktop', 'mobile'], true)) {
            $device_target = 'all';
        }

        $post_type_target = isset($_POST['adcore_post_type_target'])
            ? sanitize_key(wp_unslash($_POST['adcore_post_type_target']))
            : 'all';

        if (!in_array($post_type_target, ['all', 'post', 'page'], true)) {
            $post_type_target = 'all';
        }

        update_post_meta($post_id, '_adcore_placement_ad_id', $ad_id);
        update_post_meta($post_id, '_adcore_auto_insert', $auto_insert);
        update_post_meta($post_id, '_adcore_insert_position', $insert_position);
        update_post_meta($post_id, '_adcore_paragraph_number', $paragraph_number);
        update_post_meta($post_id, '_adcore_device_target', $device_target);
        update_post_meta($post_id, '_adcore_post_type_target', $post_type_target);
    }
}