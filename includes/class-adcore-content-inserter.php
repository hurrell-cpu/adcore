<?php

if (!defined('ABSPATH')) {
    exit;
}

final class AdCore_Content_Inserter
{
    public static function init(): void
    {
        add_filter('the_content', [self::class, 'insert_ads']);
    }

    public static function insert_ads(string $content): string
    {
        if (is_admin() || !is_singular() || !in_the_loop() || !is_main_query()) {
            return $content;
        }

        $placements = get_posts([
            'post_type'      => 'adcore_placement',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'meta_key'       => '_adcore_auto_insert',
            'meta_value'     => 'yes',
        ]);

        if (empty($placements)) {
            return $content;
        }

        foreach ($placements as $placement) {
            $position = get_post_meta($placement->ID, '_adcore_insert_position', true) ?: 'after_paragraph';
            $paragraph_number = (int) get_post_meta($placement->ID, '_adcore_paragraph_number', true) ?: 2;

            $ad_html = AdCore_Shortcodes::render_placement([
                'id' => $placement->ID,
            ]);

            if (!$ad_html) {
                continue;
            }

            if ($position === 'before_content') {
                $content = $ad_html . $content;
            }

            if ($position === 'after_content') {
                $content = $content . $ad_html;
            }

            if ($position === 'after_paragraph') {
                $content = self::insert_after_paragraph($content, $ad_html, $paragraph_number);
            }
        }

        return $content;
    }

    private static function insert_after_paragraph(string $content, string $ad_html, int $paragraph_number): string
    {
        $paragraphs = explode('</p>', $content);

        if (count($paragraphs) < $paragraph_number) {
            return $content . $ad_html;
        }

        foreach ($paragraphs as $index => $paragraph) {
            if (trim($paragraph)) {
                $paragraphs[$index] .= '</p>';
            }

            if (($index + 1) === $paragraph_number) {
                $paragraphs[$index] .= $ad_html;
            }
        }

        return implode('', $paragraphs);
    }
}