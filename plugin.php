<?php
/**
 * Plugin Name: Arconix Plugins
 * Plugin URI: http://arconixpc.com/
 * Description: Custom Plugin for consistently displaying the developed plugins
 *
 * Version: 0.2
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
     * @version 0.2
     */
    function __construct() {
        
        /* Set the necessary constants */
        add_action( 'plugins_loaded', array( $this, 'constants' ), 1 );

        /* Run the necessary functions and add them to their respective hooks */
        $this->hooks();

        /* Register activation hook */
        register_activation_hook( __FILE__, array( $this, 'activation' ) );
        
        /* Register deactivation hook */
        register_deactivation_hook( __FILE__, array( $this, 'deactivation' ) );
        
    }
    
    /**
     * Defines constants used by the plugin.
     *
     * @since 0.1
     * @version 0.2
     */
    function constants() {
        
        /* Set the constant for the plugin version */
        define( 'ACPL_VERSION', '0.1' );

        /* Set constant path to the plugin directory */
        define( 'ACPL_DIR', plugin_dir_url( __FILE__ ) );

    }
        
    /**
     * Run the necessary functions and add them to their respective hooks
     * 
     * @since 0.2
     */
    function hooks() {
        
        /* Create the post type */
        add_action( 'init', 'create_post_type', 11 );

        /* Post Thumbnail Support */
        add_action( 'after_setup_theme', 'add_post_thumbnail_support' , 9999 );

        /* Enqueue CSS */
        add_action( 'wp_enqueue_scripts', 'enqueue_css' );
        
        /* Edit the columns on the all posts screen */
        add_action( 'manage_posts_custom_column', 'columns_data' );
        add_filter( 'manage_edit-plugins_columns', 'columns_filter' );
        
        /* Append the Right Now widget */
        add_action( 'right_now_content_table_end', 'right_now' );
        
        /* Add the post type meta box */
        add_filter( 'cmb_meta_boxes', 'create_meta_box' );
        
        /* Post type updated messages */
        add_filter( 'post_updated_messages', 'updated_messages' );
        
        /* Add a filter for the_content to display the post type data */
        add_filter( 'the_content', 'content_filter' );
        
        
        require_once( dirname( __FILE__ ) . '/includes/functions.php' );
        require_once( dirname( __FILE__ ) . '/includes/post-type.php' );
        
        if( is_admin() )
            require_once( dirname( __FILE__ ) . '/includes/admin.php' );
        
        if( !class_exists( 'cmb_Meta_Box' ) )
            require_once( dirname( __FILE__ ) . '/includes/metabox/init.php' );
        
    }
    
    /**
     * Runs on plugin activation
     * 
     * @since 0.1
     * @version 0.2
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

new Arconix_Plugins();
?>