<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

    function remove_yoast_settings() {

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Sorry, you do not have sufficient permissions to access this page.', 'remove-yoast-stuff' ) );
        }

        $shown_menu_items = get_option( 'enable_yoast_menu', [] );

        $menu_options = [
            1  => 'Settings',
            2  => 'Integrations',
            3  => 'Tools',
            4  => 'Academy',
            5  => 'Plans',
            6  => 'Workouts (premium)',
            7  => 'Redirects (premium)',
            8  => 'Bulk Editor',
            9  => 'Support',
            10 => 'Upgrade',
            11 => 'AI Insights (premium)',
        ];
        ?>

        <div class="wrap">

            <h1>
                <?php echo get_admin_page_title(); ?>
            </h1>

            <?php if ( function_exists( 'bp_show_error_messages' ) ) { ?>
                <?php bp_show_error_messages(); ?>
            <?php } ?>

            <p>
                Select which pages you (temporarily) want to show.
                <br>
                Click save.
                <br>
                Click reload (because option gets stored after menu is generated).
            </p>
            <form method="POST">
                <input type="hidden" name="enable_yoast_nonce" value="<?php echo wp_create_nonce( 'enable-yoast-nonce' ); ?>">
                <?php foreach( $menu_options as $key => $label ) { ?>
                    <?php $checked = in_array( $key, $shown_menu_items ) ? ' checked' : ''; ?>
                    <label>
                        <input type="checkbox" name="enable_yoast_options[]" value="<?php echo (int) $key; ?>"<?php echo $checked; ?>><?php echo $label; ?>
                    </label>
                    <br>
                <?php } ?>
                <br>
                <input type="submit" class="button button-primary" value="<?php esc_html_e( 'Save', 'remove-yoast-stuff' ); ?>" />
            </form>
        </div>
    <?php }
