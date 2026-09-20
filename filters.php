<?php
    if ( ! defined( 'ABSPATH' ) ) exit;

    // remove Yoast's marketing comments in source
    add_filter( 'wpseo_debug_markers', '__return_false' );

    /*
     * Remove Yoast update notice (not sure if still works)
     * @src: https://gist.github.com/wpchannel/7cdd6eed0927ea5732d7
     */
    function b3_filter_yst_wpseo_option( $option ) {
        if ( is_array( $option ) ) {
            $option[ 'seen_about' ] = true;
        }

        return $option;
    }
    add_filter('option_wpseo', 'b3_filter_yst_wpseo_option');
