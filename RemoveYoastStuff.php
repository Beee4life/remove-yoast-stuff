<?php
    /*
    Plugin Name: Remove Yoast Stuff
    Version: 1.0
    Description: This plugin removes all Yoast' upsell stuff
    Author: Aseda
    Author URI: https://aseda.nl

    http://www.berryplasman.com
       ___  ____ ____ ____
      / _ )/ __/  __/  __/
     / _  / _/   _/   _/
    /____/___/____/____/

    */

    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }

    if ( ! class_exists( 'RemoveYoastStuff' ) ) :

        /**
         * Main class
         */
        class RemoveYoastStuff {

            /**
             *  A dummy constructor to ensure plugin is only initialized once
             */
            public function __construct() {
                add_action( 'admin_enqueue_scripts',  [ $this, 'pb_enqueue_admin_css' ] );

                include 'actions.php';
                include 'filters.php';
            }

            public function pb_enqueue_admin_css() {
                wp_enqueue_style( 'remove-yoast', plugins_url( 'admin.css', __FILE__ ), array(), false );
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

    endif; // class_exists check
