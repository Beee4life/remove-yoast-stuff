<?php
    /*
    Plugin Name: Remove Yoast Stuff
    Version: 1.0
    Description: This plugin removes all Yoast' upsell stuff
    Author: Beee
    Author URI: https://berryplasman.com
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

                include 'actions.php';
                include 'filters.php';
            }

            public function enqueue_admin_css() {
                wp_enqueue_style( 'remove-yoast', plugins_url( 'admin.css', __FILE__ ), array(), '1.0' );
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
