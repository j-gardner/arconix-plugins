<?php
/**
 * Plugin Name: Arconix Plugins
 * Plugin URI: http://arconixpc.com/
 * Description: Custom Plugin for consistently creating and displaying user-developed plugins on a website
 *
 * Version: 0.3
 *
 * Author: John Gardner
 * Author URI: http://arconixpc.com
 *
 * License: GNU General Public License v2.0
 * License URI: http://www.opensource.org/licenses/gpl-license.php
 */

class Arconix_Plugins {

    /**
     * Constructor
     *
     * @since 0.1
     * @version 0.3
     */
    function __construct() {
        $this->constants();
        $this->hooks();
        register_activation_hook( __FILE__, array( $this, 'activation' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivation' ) );
    }

    /**
     * Define Plugin Constants
     *
     * @since 0.3
     */
    function constants() {
        define( 'ACPL_VERSION', '0.3' );
        define( 'ACPL_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );
        define( 'ACPL_INCLUDES_URL', trailingslashit( ACPL_URL . 'includes' ) );
        define( 'ACPL_IMAGES_URL', trailingslashit( ACPL_URL . 'images' ) );
        define( 'ACPL_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
        define( 'ACPL_INCLUDES_DIR', trailingslashit( ACPL_DIR . 'includes' ) );
    }

    /**
     * Run the necessary functions and add them to their respective hooks
     *
     * @since 0.2
     * @version 0.3
     */
    function hooks() {
        add_action( 'init', 'acpl_create_post_type' );
        add_action( 'after_setup_theme', 'acpl_post_thumbnail_support' , 9999 );
        add_action( 'wp_enqueue_scripts', 'acpl_enqueue_script' );
        add_action( 'manage_posts_custom_column', 'acpl_columns_data' );
        add_action( 'right_now_content_table_end', 'acpl_right_now' );

        add_filter( 'manage_edit-plugins_columns', 'acpl_columns_filter' );
        add_filter( 'cmb_meta_boxes', 'acpl_create_meta_box' );
        add_filter( 'post_updated_messages', 'acpl_updated_messages' );
        add_filter( 'the_content', 'acpl_content_filter' );


        require_once( ACPL_INCLUDES_DIR . 'functions.php' );
        require_once( ACPL_INCLUDES_DIR . 'post-type.php' );

        if( is_admin() )
            require_once( ACPL_INCLUDES_DIR . 'admin.php' );

        if( !class_exists( 'cmb_Meta_Box' ) )
            require_once( ACPL_INCLUDES_DIR . 'metabox/init.php' );
    }

    /**
     * Runs on plugin activation
     *
     * @since 0.1
     * @version 0.3
     */
    function activation() {
        // flush_rewrite_rules();
    }

    /**
     * Runs on plugin deactivation
     *
     * @since 0.1
     * @version 0.2
     */
    function deactivation() {
        flush_rewrite_rules();
    }

}

new Arconix_Plugins;
?>