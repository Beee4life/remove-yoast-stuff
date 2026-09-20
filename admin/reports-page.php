<?php

    use function Termwind\render;

    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

    function b3_scan_reports_page() {

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Sorry, you do not have sufficient permissions to access this page.', 'b3-scan-site' ) );
        }
        $report_args = [
            'post_type'      => 'scanreport',
            'posts_per_page' => 10,
        ];
        $reports = get_posts( $report_args );
        ?>

        <div class="wrap">
            <h1>
                <?php echo get_admin_page_title(); ?>
            </h1>

            <?php if ( function_exists( 'bp_show_error_messages' ) ) { ?>
                <?php bp_show_error_messages(); ?>
            <?php } ?>

            <?php do_action( 'b3_scan_menu' ); ?>

            <?php
                if ( isset( $_GET[ 'report_id' ] ) ) {
                    $report_id = (int) $_GET[ 'report_id' ];
                    $post      = get_post( $report_id );

                    if ( $post instanceof WP_Post ) {
                        $views_folder = sprintf( '%s/views', dirname( dirname( __FILE__ ) ) );
                        $file_path    = sprintf( '%s/single-report.twig', $views_folder );
                        $body         = get_post_meta( $post->ID, 'body', true );

                        $skip = [
                            'namespaces',
                            'routes',
                            'authentication',
                            'page_for_posts',
                            'page_on_front',
                            'show_on_front',
                            'site_logo',
                            'site_icon',
                            'site_icon_url',
                            '_links',
                        ];

                        $active_plugins = b3_check_for_plugins( $body[ 'namespaces' ] );

                        if ( file_exists( $file_path ) ) {
                            $render_this = [
                                'api_root'       => get_post_meta( $post->ID, 'api_root', true ),
                                'active_plugins' => $active_plugins,
                                'body'           => $body,
                                'gutenberg'      => isset( $body[ 'routes' ][ '/wp/v2/block-types' ] ),
                                'date_format'    => get_option( 'date_format' ),
                                'post'           => $post,
                                'response_code'  => get_post_meta( $post->ID, 'response_code', true ),
                                'skip'           => $skip,
                                'time_format'    => get_option( 'time_format' ),
                                'user'           => get_userdata( $post->post_author ),
                            ];
                            Timber::render( 'single-report.twig', $render_this );
                        }
                    }

                } elseif ( ! empty( $reports ) ) {
                    $items = [];
                    $table_headers = b3_get_table_headers();

                    foreach( $reports as $report ) {
                        $items_info = [
                            'ID'            => $report->ID,
                            'title'         => $report->post_title,
                            'created'       => wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $report->post_date ) ),
                            'modified'      => wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $report->post_modified ) ),
                            'response_code' => get_post_meta( $report->ID, 'response_code', true ),
                            'user'          => get_userdata( $report->post_author ),
                            'url'           => get_post_meta( $report->ID, 'scan_url', true ),
                            'view_report'   => admin_url( 'admin.php?page=b3-scan-reports&report_id=' . $report->ID ),
                        ];
                        $items[] = $items_info;
                    }

                    if ( ! empty( $items ) ) {
                        $views_folder = sprintf( '%s/views', dirname( dirname( __FILE__ ) ) );
                        $file_path    = sprintf( '%s/page--reports.twig', $views_folder );

                        if ( file_exists( $file_path ) ) {
                            $render_this = [
                                'headers' => $table_headers,
                                'items'   => $items,
                            ];
                            Timber::render( 'page--reports.twig', $render_this );
                        }
                    }
                }
            ?>

        </div>
    <?php }
