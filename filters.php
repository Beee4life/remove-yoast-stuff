<?php
    // remove Yoast's marketing comments in source
    add_filter( 'wpseo_debug_markers', '__return_false' );

    function b3_filter_admin_pages1( $value ) {
        if ( is_array( $value ) ) {
            foreach( $value as $main_key => $values ) {
                if ( in_array( 'wpseo_licenses', $values ) ) {
                    unset( $value[ $main_key ] );
                } elseif ( in_array( 'wpseo_workouts', $values ) ) {
                    unset( $value[ $main_key ] );
                } elseif ( in_array( 'wpseo_redirects', $values ) ) {
                    unset( $value[ $main_key ] );
                }
            }
        }

        return $value;
    }
    add_filter( 'wpseo_submenu_pages', 'b3_filter_admin_pages1' );

    function b3_filter_admin_pages2( $value ) {
        if ( is_array( $value ) ) {
            foreach ( $value as $main_key => $values ) {
                if ( in_array( 'wpseo_redirects', $values )
                     || in_array( 'wpseo_workouts', $values )
                     || in_array( 'wpseo_brand_insights', $values )
                ) {
                    $key = array_search( 'edit_others_posts', $values );
                    if ( $key ) {
                        $value[ $main_key ][ $key ] = 'this';
                    }
                } elseif ( in_array( 'wpseo_licenses', $values )
                     || in_array( 'wpseo_tools', $values )
                     || in_array( 'wpseo_upgrade_sidebar', $values )
                ) {
                    $key = array_search( 'wpseo_manage_options', $values );
                    if ( $key ) {
                        $value[ $main_key ][ $key ] = 'this';
                    }
                }
            }
        }

        return $value;
    }
    add_filter( 'wpseo_submenu_pages', 'b3_filter_admin_pages2' );

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
