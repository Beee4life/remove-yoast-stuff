<?php
    /*
    Plugin Name:    Remove Yoast Stuff
    Version:        1.1.0
    Description:    This plugin removes all Yoast' upsell stuff
    Author:         Beee
    Author URI:     https://berryplasman.com
    Plugin URI:     https://github.com/Beee4life/remove-yoast-stuff
    License:        GPLv2 or later
    License URI:    https://www.gnu.org/licenses/gpl.html
       ___  ____ ____ ____
      / _ )/ __/  __/  __/
     / _  / _/   _/   _/
    /____/___/____/____/

    */

    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

    if ( ! class_exists( 'RemoveYoastStuff' ) ) :

        class RemoveYoastStuff {
            public function __construct() {
                add_action( 'admin_enqueue_scripts',  [ $this, 'enqueue_admin_css' ] );
                add_action( 'admin_menu',               [ $this, 'add_admin_pages' ] );
                add_action( 'admin_init',               [ $this, 'handle_settings_form' ] );

                add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), [ $this, 'b3_settings_link' ] );

                include 'actions.php';
                include 'filters.php';
            }

            public function enqueue_admin_css() {
                wp_enqueue_style( 'remove-yoast', plugins_url( 'admin/admin.css', __FILE__ ), [], '1.1.0' );
            }

            public function add_admin_pages() {
                include 'admin/admin-page.php';
                add_submenu_page( 'admin.php', 'Remove Yoast Settings', 'Remove Yoast Settings', 'manage_options', 'remove-yoast', 'remove_yoast_settings', 95 );
            }

            public function b3_settings_link( $links ) {
            }

            public function handle_settings_form() {
                $option_key = 'enable_yoast_menu';

                if ( isset( $_POST[ 'enable_yoast_nonce' ] ) ) {
                    if ( ! wp_verify_nonce( $_POST[ 'enable_yoast_nonce' ], 'enable-yoast-nonce' ) ) {
                        // error
                    } else {
                        // ok
                        if ( isset( $_POST[ 'enable_yoast_options' ] ) && ! empty( $_POST[ 'enable_yoast_options' ] ) ) {
                            $keys   = array_map( 'intval', $_POST[ 'enable_yoast_options' ] );
                            $keys[] = 0;
                            // echo '<pre>'; var_dump($keys); echo '</pre>'; exit;
                            // delete_option( $option_key );
                            update_option( $option_key, $keys );
                        } else {
                            $keys = [0];
                            // delete_option( $option_key );
                            update_option( $option_key, $keys );
                        }
                    }
                }
            }

            public static function get_instance() {
                static $instance;

                if ( null === $instance ) {
                    $instance = new self();
                }

                return $instance;
            }
        }

        RemoveYoastStuff::get_instance();

    endif;
