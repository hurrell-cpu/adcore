<?php

if (!defined('ABSPATH')) {
    exit;
}

final class AdCore_Dashboard
{
    public static function init(): void
    {
        add_action('admin_menu', [self::class, 'register_menu']);
        add_action('admin_post_adcore_export_stats_csv', [self::class, 'export_csv']);
    }

    public static function register_menu(): void
    {
        add_submenu_page(
            'edit.php?post_type=adcore_ad',
            __('Dashboard', 'adcore'),
            __('Dashboard', 'adcore'),
            'manage_options',
            'adcore-dashboard',
            [self::class, 'render']
        );
    }

    private static function get_stats(): array
    {
        $ads = get_posts([
            'post_type'      => 'adcore_ad',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
        ]);

        $total_impressions = 0;
        $total_clicks      = 0;
        $rows              = [];

        foreach ($ads as $ad) {
            $impressions = (int) get_post_meta($ad->ID, '_adcore_impressions', true);
            $clicks      = (int) get_post_meta($ad->ID, '_adcore_clicks', true);
            $ctr         = $impressions > 0 ? ($clicks / $impressions) * 100 : 0;

            $total_impressions += $impressions;
            $total_clicks      += $clicks;

            $rows[] = [
                'id'          => $ad->ID,
                'title'       => $ad->post_title,
                'impressions' => $impressions,
                'clicks'      => $clicks,
                'ctr'         => $ctr,
            ];
        }

        usort($rows, function ($a, $b) {
            return $b['clicks'] <=> $a['clicks'];
        });

        $average_ctr = $total_impressions > 0
            ? ($total_clicks / $total_impressions) * 100
            : 0;

        return [
            'total_impressions' => $total_impressions,
            'total_clicks'      => $total_clicks,
            'average_ctr'       => $average_ctr,
            'rows'              => $rows,
        ];
    }

    public static function export_csv(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('You do not have permission to export AdCore stats.', 'adcore'));
        }

        check_admin_referer('adcore_export_stats_csv');

        $stats    = self::get_stats();
        $filename = 'adcore-stats-' . gmdate('Y-m-d') . '.csv';

        nocache_headers();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        if ($output === false) {
            wp_die(esc_html__('Unable to create CSV export.', 'adcore'));
        }

        fputcsv($output, ['AdCore Stats Export']);
        fputcsv($output, ['Generated At', gmdate('Y-m-d H:i:s') . ' UTC']);
        fputcsv($output, []);
        fputcsv($output, ['Total Impressions', $stats['total_impressions']]);
        fputcsv($output, ['Total Clicks', $stats['total_clicks']]);
        fputcsv($output, ['Average CTR', number_format((float) $stats['average_ctr'], 2) . '%']);
        fputcsv($output, []);
        fputcsv($output, ['Ad ID', 'Ad Title', 'Impressions', 'Clicks', 'CTR']);

        foreach ($stats['rows'] as $row) {
            fputcsv($output, [
                $row['id'],
                $row['title'],
                $row['impressions'],
                $row['clicks'],
                number_format((float) $row['ctr'], 2) . '%',
            ]);
        }

        fclose($output);
        exit;
    }

    public static function render(): void
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        $stats             = self::get_stats();
        $total_impressions = $stats['total_impressions'];
        $total_clicks      = $stats['total_clicks'];
        $average_ctr       = $stats['average_ctr'];
        $rows              = $stats['rows'];
        ?>

        <div class="wrap">
            <h1><?php esc_html_e('AdCore Dashboard', 'adcore'); ?></h1>

            <p>
                <a
                    class="button button-secondary"
                    href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=adcore_export_stats_csv'), 'adcore_export_stats_csv')); ?>"
                >
                    <?php esc_html_e('Export CSV', 'adcore'); ?>
                </a>
            </p>

            <style>
                .adcore-dashboard-cards {
                    display: grid;
                    grid-template-columns: repeat(3, minmax(180px, 1fr));
                    gap: 16px;
                    margin: 24px 0;
                    max-width: 900px;
                }

                .adcore-card {
                    background: #fff;
                    border: 1px solid #dcdcde;
                    border-radius: 10px;
                    padding: 18px;
                }

                .adcore-card-label {
                    color: #646970;
                    font-size: 13px;
                    margin-bottom: 8px;
                }

                .adcore-card-value {
                    font-size: 28px;
                    font-weight: 700;
                }

                .adcore-table-wrap {
                    max-width: 1000px;
                    margin-top: 24px;
                }

                @media (max-width: 782px) {
                    .adcore-dashboard-cards {
                        grid-template-columns: 1fr;
                    }
                }
            </style>

            <div class="adcore-dashboard-cards">
                <div class="adcore-card">
                    <div class="adcore-card-label"><?php esc_html_e('Total Impressions', 'adcore'); ?></div>
                    <div class="adcore-card-value"><?php echo esc_html(number_format_i18n($total_impressions)); ?></div>
                </div>

                <div class="adcore-card">
                    <div class="adcore-card-label"><?php esc_html_e('Total Clicks', 'adcore'); ?></div>
                    <div class="adcore-card-value"><?php echo esc_html(number_format_i18n($total_clicks)); ?></div>
                </div>

                <div class="adcore-card">
                    <div class="adcore-card-label"><?php esc_html_e('Average CTR', 'adcore'); ?></div>
                    <div class="adcore-card-value"><?php echo esc_html(number_format($average_ctr, 2)); ?>%</div>
                </div>
            </div>

            <div class="adcore-table-wrap">
                <h2><?php esc_html_e('Top Performing Ads', 'adcore'); ?></h2>

                <table class="widefat striped">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Ad', 'adcore'); ?></th>
                            <th><?php esc_html_e('Impressions', 'adcore'); ?></th>
                            <th><?php esc_html_e('Clicks', 'adcore'); ?></th>
                            <th><?php esc_html_e('CTR', 'adcore'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($rows)): ?>
                            <tr>
                                <td colspan="4"><?php esc_html_e('No ad data yet.', 'adcore'); ?></td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($rows as $row): ?>
                                <tr>
                                    <td>
                                        <a href="<?php echo esc_url(get_edit_post_link($row['id'])); ?>">
                                            <?php echo esc_html($row['title']); ?>
                                        </a>
                                    </td>
                                    <td><?php echo esc_html(number_format_i18n($row['impressions'])); ?></td>
                                    <td><?php echo esc_html(number_format_i18n($row['clicks'])); ?></td>
                                    <td><?php echo esc_html(number_format($row['ctr'], 2)); ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php
    }
}
