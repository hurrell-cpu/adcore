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

        $selected_ad_id = (int) get_post_meta($post->ID, '_adcore_placement_ad_id', true);

        $ads = get_posts([
            'post_type'      => 'adcore_ad',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'title',
            'order'          => 'ASC',
        ]);
        ?>

        <div class="adcore-field">
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

            <p style="color:#646970;">
                <?php esc_html_e('This placement will render the selected ad using the [adcore_placement] shortcode.', 'adcore'); ?>
            </p>
        </div>

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

        update_post_meta($post_id, '_adcore_placement_ad_id', $ad_id);
    }
}