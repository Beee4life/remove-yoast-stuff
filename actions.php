<?php
    if ( ! defined( 'ABSPATH' ) ) exit;

    /*
     * Unbind Yoast's awful constant upsell notifications whenever you trash/delete anything
     *
     * @src: https://gist.github.com/wpchannel/7cdd6eed0927ea5732d7?permalink_comment_id=3709137#gistcomment-3709137
     * @ref: https://github.com/Yoast/wordpress-seo/blob/0742e9b6ba4c0d6ae9d65223267a106b92a6a4a1/admin/watchers/class-slug-change-watcher.php#L18
     * @another ref: https://gist.github.com/wpchannel/7cdd6eed0927ea5732d7
     * @see: https://wordpress.stackexchange.com/a/352509
     */
    function b3_unbind_yoast_slug_change_watchers() {
        $priority = 10;
        $actions_methods = [
            'wp_trash_post'        => 'detect_post_trash',
            'before_delete_post'   => 'detect_post_delete',
            'delete_term_taxonomy' => 'detect_term_delete',
        ];

        global $wp_filter;
        foreach( $actions_methods as $action => $method ) {
            if ( isset( $wp_filter[ $action ]->callbacks[ $priority ] ) && ( ! empty( $wp_filter[ $action ]->callbacks[ $priority ] ) ) ) {
                $wp_filter[ $action ]->callbacks[ $priority ] = array_filter( $wp_filter[ $action ]->callbacks[ $priority ], function( $v, $k ) use ( $method ) {
                    return ( stripos( $k, $method ) === false );
                }, ARRAY_FILTER_USE_BOTH );
            }
        }
    }
    add_action( 'plugins_loaded', 'b3_unbind_yoast_slug_change_watchers', 5 );

    function b3_remove_admin_notices() {
        if ( is_plugin_active( 'wordpress-seo/wp-seo.php' ) ) {
            if ( ! class_exists( 'Yoast_Notification_Center' ) ) {
                require_once( WP_PLUGIN_DIR . '/wordpress-seo/admin/class-yoast-notification-center.php' );
            }
            remove_action( 'all_admin_notices', [ Yoast_Notification_Center::get(), 'display_notifications' ] );
            remove_action( 'admin_notices', [ Yoast_Notification_Center::get(), 'display_notifications' ] );
        }
    }
    add_action( 'admin_init', 'b3_remove_admin_notices' );

    function b3_remove_yoast_zapier_text() {
        if ( class_exists( 'Yoast\WP\SEO\Integrations\Third_Party\Zapier_Free' ) ) {
            $zapier_free = YoastSEO()->classes->get( Yoast\WP\SEO\Integrations\Third_Party\Zapier_Free::class );
            remove_action( 'wpseo_publishbox_misc_actions', [ $zapier_free, 'add_publishbox_text' ] );
        }
    }
    add_action( 'init', 'b3_remove_yoast_zapier_text' );

    function b3_remove_yoast_ads() {
        // check if class Yoast_Notification_Center exists
        if ( ! class_exists( 'Yoast_Notification_Center' ) ) {
            return;
        }
        $notification_center = Yoast_Notification_Center::get();
        // get all notifications
        $notifications = $notification_center->get_sorted_notifications();
        // loop through all YOAST notifications
        foreach( $notifications as $notification ) {
            // transform the notification to an array, so that we can access the message
            $notification_array = $notification->to_array();
            // get message from array
            $notification_message = $notification_array[ 'message' ] ?? null;
            // continue to next notification if no message in array
            if ( ! $notification_message ) {
                continue;
            }

            // Remove the notification if it contains a string.
            // You could also check for $notification_array['options']['yoast_branding'] === true
            if ( stripos( $notification_message, 'Get Yoast SEO Premium' ) !== false ) {
                $notification_center->remove_notification( $notification );
            } elseif ( stripos( $notification_message, 'Koop Yoast SEO Premium' ) !== false ) {
                $notification_center->remove_notification( $notification );
            }
        }
    }
    add_action( 'admin_notices', 'b3_remove_yoast_ads', 5 );

    /*
     * Remove any filter.
     * Source: https://gist.github.com/tripflex/c6518efc1753cf2392559866b4bd1a53?permalink_comment_id=3528826#gistcomment-3528826
     */
    if ( ! function_exists( 'remove_class_hook' ) ) {
        function remove_class_hook( $tag, $class_name = '', $method_name = '', $priority = 10 ) {
            global $wp_filter;
            $is_hook_removed = false;
            if ( ! empty( $wp_filter[ $tag ]->callbacks[ $priority ] ) ) {
                $methods = array_filter(wp_list_pluck(
                    $wp_filter[ $tag ]->callbacks[ $priority ],
                    'function'
                ), function ($method) {
                    /*
                     * Allow only array & string notation for hooks, since we're
                     * looking to remove an exact method of a class anyway. And the
                     * method of the class is passed in as a string anyway.
                     */
                    return is_string($method) || is_array($method);
                });
                $found_hooks = ! empty( $methods ) ? wp_list_filter( $methods, array( 1 => $method_name ) ) : array();
                foreach( $found_hooks as $hook_key => $hook ) {
                    if ( ! empty( $hook[0] ) && is_object( $hook[0] ) && get_class( $hook[0] ) === $class_name ) {
                        $wp_filter[ $tag ]->remove_filter( $tag, $hook, $priority );
                        $is_hook_removed = true;
                    }
                }
            }
            return $is_hook_removed;
        }
    }
    add_action( 'admin_init', function() {
        remove_class_hook( 'admin_notices', 'WPSEO_Admin_Init', 'permalink_settings_notice' );
    });

    function b3_unset_yoast_menu_items() {
        global $submenu;
        // unset( $submenu[ 'wpseo_dashboard' ][ 2 ] ); // integrations
        unset( $submenu[ 'wpseo_dashboard' ][ 3 ] ); // academy
        unset( $submenu[ 'wpseo_dashboard' ][ 4 ] ); // support
        unset( $submenu[ 'wpseo_dashboard' ][ 5 ] ); // upgrade button
        unset( $submenu[ 'wpseo_dashboard' ][ 6 ] ); // AI insights button
    }
    add_action( 'admin_menu', 'b3_unset_yoast_menu_items' );

    /**
     * Remove the permalink notice action.
     * Uses @remove_class_hook.
     */
    add_action( 'admin_init', function() {
        remove_class_hook( 'admin_notices', 'WPSEO_Admin_Init', 'permalink_settings_notice' );
    });

    /*
     * Disable Yoast dropdowns
     * @src: https://wordpress.stackexchange.com/a/300965/103402
     */
    function b3_remove_yoast_seo_admin_filters() {
        global $wpseo_meta_columns ;
        if ( $wpseo_meta_columns ) {
            remove_action( 'restrict_manage_posts', [ $wpseo_meta_columns, 'posts_filter_dropdown' ] );
            remove_action( 'restrict_manage_posts', [ $wpseo_meta_columns, 'posts_filter_dropdown_readability' ] );
        }
    }
    add_action( 'admin_init', 'b3_remove_yoast_seo_admin_filters', 20 );
