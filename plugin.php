<?php
/**
 * Plugin Name: Arconix Plugins
 * Plugin URI: http://arconixpc.com/
 * Description: Plugin for displaying WP.org-hosted plugins on your website
 *
 * Version: 1.0
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
     * @version 0.5
     */
    function __construct() {
        $this->constants();

        register_activation_hook( __FILE__,                 array( $this, 'activation' ) );
        register_deactivation_hook( __FILE__,               array( $this, 'deactivation' ) );

        add_action( 'init',                                 'arconix_plugins_init_meta_boxes', 9999 );
        add_action( 'init',                                 array( $this, 'content_types' ) );
        add_action( 'widgets_init',                         array( $this, 'plugin_widgets' ) );
        add_action( 'wp_enqueue_scripts',                   array( $this, 'scripts' ) );
        add_action( 'manage_plugins_posts_custom_column',   array( $this, 'custom_columns_action' ) );
        add_action( 'right_now_content_table_end',          array( $this, 'right_now' ) );

        add_filter( 'manage_plugins_posts_columns',         array( $this,'custom_columns_filter' ) );
        add_filter( 'cmb_meta_boxes',                       array( $this, 'metaboxes' ) );
        add_filter( 'post_updated_messages',                array( $this, 'messages' ) );
        add_filter( 'the_content',                          array( $this, 'content_filter' ) );        
    }

    /**
     * Define Plugin Constants
     *
     * @since 0.3
     */
    function constants() {
        define( 'ACPL_VERSION',             '1.0' );
        define( 'ACPL_URL',                 trailingslashit( plugin_dir_url( __FILE__ ) ) );
        define( 'ACPL_INCLUDES_URL',        trailingslashit( ACPL_URL . 'includes' ) );
        define( 'ACPL_IMAGES_URL',          trailingslashit( ACPL_INCLUDES_URL . 'images' ) );
        define( 'ACPL_DIR',                 trailingslashit( plugin_dir_path( __FILE__ ) ) );
        define( 'ACPL_INCLUDES_DIR',        trailingslashit( ACPL_DIR . 'includes' ) );
        define( 'ACPL_VIEWS_DIR',           trailingslashit( ACPL_INCLUDES_DIR . 'views' ) );
    }

    /**
     * Runs on plugin activation
     *
     * @since 0.1
     * @version 0.5
     */
    function activation() {
        $this->content_types();
        flush_rewrite_rules();
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

    /**
     * Set our plugin defaults for post type and metabox registration
     *
     * @since 0.5
     * @return array $defaults
     */
    function defaults() {
        $defaults = array(
            'post_type' => array(
                'slug' => 'plugins',
                'args' => array(
                    'labels' => array(
                        'name'                  => __( 'Plugins',                       'acpl' ),
                        'singular_name'         => __( 'Plugin',                        'acpl' ),
                        'add_new_item'          => __( 'Add New Plugin',                'acpl' ),
                        'edit_item'             => __( 'Edit Plugin',                   'acpl' ),
                        'new_item'              => __( 'New Plugin',                    'acpl' ),
                        'view_item'             => __( 'View Plugin',                   'acpl' ),
                        'search_items'          => __( 'Search Plugins',                'acpl' ),
                        'not_found'             => __( 'No plugins found',              'acpl' ),
                        'not_found_in_trash'    => __( 'No plugins found in the trash', 'acpl' )
                    ),
                    'public'            => true,
                    'query_var'         => true,
                    'menu_position'     => 100,
                    'has_archive'       => true,
                    'supports'          => array( 'title', 'thumbnail', 'excerpt' ),
                    'rewrite'           => array( 'with_front' => false )
                )
            )
        return apply_filters( 'arconix_plugins_defaults', $defaults );
    }

    /**
     * Register the post type
     *
     * @since 0.1
     * @version  0.5
     */
    function content_types() {
        $defaults = $this->defaults();
        register_post_type( $defaults['post_type']['slug'], $defaults['post_type']['args'] );
    }

    /**
     * Register the plugin widget(s)
     *
     * @since 0.5
     */
    function plugin_widgets() {
        $widgets = array( 'Arconix_Widget_Plugin_Details', 'Arconix_Widget_Plugin_Resources', 'Arconix_Widget_Plugin_Related' );

        foreach ($widgets as $widget ) {
            register_widget( $widget );
        }
        
    }

    /**
     * Modifies the post save notifications to properly reflect the post-type
     *
     * @global object $post
     * @global int $post_ID
     * @param array $messages
     * @return array $messages
     * @since 0.1
     */
    function messages( $messages ){
        global $post, $post_ID;

        $messages['plugins'] = array(
            0 => '', // Unused. Messages start at index 1.
            1 => sprintf( __( 'Plugin updated. <a href="%s">View plugin</a>' ), esc_url( get_permalink( $post_ID ) ) ),
            2 => __( 'Custom field updated.' ),
            3 => __( 'Custom field deleted.' ),
            4 => __( 'Plugin updated.' ),
            // translators: %s: date and time of the revision
            5 => isset( $_GET['revision'] ) ? sprintf( __( 'Plugin restored to revision from %s' ), wp_post_revision_title( ( int ) $_GET['revision'], false ) ) : false,
            6 => sprintf( __( 'Plugin published. <a href="%s">View plugin</a>' ), esc_url( get_permalink( $post_ID ) ) ),
            7 => __( 'Plugin saved.' ),
            8 => sprintf( __( 'Plugin submitted. <a target="_blank" href="%s">Preview plugin</a>' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
            9 => sprintf( __( 'Plugin scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview plugin</a>' ),
                    // translators: Publish box date format, see http://php.net/date
                    date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) ),
            10 => sprintf( __( 'Plugin draft updated. <a target="_blank" href="%s">Preview plugin</a>' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
        );

        return $messages;
    }

    /**
     * Add the Post type to the "Right Now" Dashboard Widget
     *
     * @link http://bajada.net/2010/06/08/how-to-add-custom-post-types-and-taxonomies-to-the-wordpress-right-now-dashboard-widget
     * @since 0.1
     */
    function right_now() {
        include_once( ACPL_VIEWS_DIR . 'right-now.php' );
    }

    /**
     * Modify the list of columns available for our custom post type
     *
     * @since 0.1
     * @version 0.5
     * @param array $columns
     * @return array $columns
     */
    function custom_columns_filter( $columns ) {

        $columns = array(
            'cb' => '<input type="checkbox" />',
            'title' => __( 'Title', 'acpl' ),
            'plugins_details' => __( 'Details', 'acpl' )
        );

        return $columns;
    }

    /**
     * Define the data that shows up in the columns we set above
     *
     * @since 0.1
     * @version 0.5
     * @global object $post
     * @param array $column
     */
    function custom_columns_action( $column ) {
        global $post;

        switch( $column ) {
            case 'plugins_details':
                // Get the slug set by the custom post type entry
                $slug = get_post_meta( $post->ID, '_acpl_slug', true );

                // Bail out if slug wasn't set
                if( ! $slug ) break;

                // Pass the slug into the api to get the data we need, bailing out if we don't get anything back
                $details = unserialize( $this->get_wporg_custom_plugin_data( $slug ) );

                if( ! $details ) {
                    __e( 'No Plugin data returned', 'acpl' );
                    break;
                }

                echo 'Latest Version: ' . $details->version . '<br />';
                echo 'Last Updated: ' . date( get_option( 'date_format' ) , strtotime( $details->last_updated ) ) . ' <em style="color: #aaa;">(' . $this->ago( strtotime( $details->last_updated ) ) . ')</em><br />';
                echo 'Downloads: ' . number_format( $details->downloaded );

                break;
        }
    }

    /**
     * Create our custom meta box for the plugin post type
     *
     * @since 0.1
     * @version 0.5
     * @param array $meta_boxes
     * @return array $meta_boxes
     */
    function metaboxes( $meta_boxes ) {
        $prefix = "_acpl_";
        //$defaults = $this->defaults(); <--- This unfortunately is not working at the moment
        //$meta_boxes[] = $defaults['meta_box'];
        
        $metabox = apply_filters( 'arconix_plugins_metabox_defaults', array(
            'id'            => 'plugins_box',
            'title'         => 'Plugin Details',
            'pages'         => array( 'plugins' ), // post type
            'context'       => 'normal',
            'priority'      => 'high',
            'show_names'    => true, // Show field names left of input
            'fields'        => array(
                array(
                    'name'  => 'Slug',
                    'desc'  => 'Enter the plugin slug',
                    'id'    => $prefix . 'slug',
                    'type'  => 'text_medium',
                ),
                array(
                    'name'  => 'Demo',
                    'desc'  => 'Enter the demo URL',
                    'id'    => $prefix . 'demo',
                    'type'  => 'text_medium',
                ),
                array(
                    'name'  => 'Donation',
                    'desc'  => 'Enter the donation URL',
                    'id'    => $prefix . 'donation',
                    'type'  => 'text_medium',
                ),
                array(
                    'name'  => 'Download',
                    'desc'  => 'Enter the download URL',
                    'id'    => $prefix . 'download',
                    'type'  => 'text_medium',
                ),
                array(
                    'name'  => 'Documentation',
                    'desc'  => 'Enter the documentation URL',
                    'id'    => $prefix . 'docs',
                    'type'  => 'text_medium',
                ),
                array(
                    'name'  => 'Support',
                    'desc'  => 'Enter the support URL.',
                    'id'    => $prefix . 'help',
                    'type'  => 'text_medium',
                ),
                array(
                    'name'  => 'Development',
                    'desc'  => 'Enter the development board URL',
                    'id'    => $prefix . 'dev',
                    'type'  => 'text_medium',
                ),
                array(
                    'name'  => 'Source Code',
                    'desc'  => 'Enter the source code URL',
                    'id'    => $prefix . 'source',
                    'type'  => 'text_medium',
                )
            )
        ) );

        $meta_boxes[] = $metabox;        

        return $meta_boxes;
    }

    /**
     * Load the necessary css, which can be overriden by creating your own file and placing it in
     * the root of your theme's folder
     *
     * @since 0.1
     * @version 0.3
     */
    function scripts() {
        if( apply_filters( 'pre_register_arconix_plugins_css', true ) ) {
            if( file_exists( get_stylesheet_directory() . '/arconix-plugins.css' ) )
                wp_enqueue_style( 'arconix-plugins', get_stylesheet_directory_uri() . '/arconix-plugins.css', false, ACPL_VERSION );
            elseif( file_exists( get_template_directory() . '/arconix-plugins.css' ) )
                wp_enqueue_style( 'arconix-plugins', get_template_directory_uri() . '/arconix-plugins.css', false, ACPL_VERSION );
            else
                wp_enqueue_style( 'arconix-plugins', ACPL_INCLUDES_URL . 'arconix-plugins.css', false, ACPL_VERSION );
        }
    }

    /**
     * Add our plugin data to the content
     *
     * @since 0.3
     * @version 0.5
     * @param string $content
     * @return string $content Modified post content
     */
    function content_filter( $content ) {
        global $post;

        if( ! 'plugins' == get_post_type() ) return $content;

        $custom = get_post_custom();
        isset( $custom["_acpl_slug"][0] )? $slug = $custom["_acpl_slug"][0] : $slug = '';

        // Return the content if $slug has no value (useful if plugin is not being hosted on wp.org or isn't live yet)
        if( ! $slug ) return $content;

        // Pass the slug into the API to get the data we need
        $details = unserialize( $this->get_wporg_custom_plugin_data( $slug ) );

        // Return the content if the plugin exists but has no data (pre-release, for example)
        if( ! $details ) return $content;

        $sections = $details->sections;

        // If this is a post excerpt (such as from an archive), then get out right here and return the excerpt or plugin description (trimmed to 25 words)
        if( in_array( 'get_the_excerpt', $GLOBALS['wp_current_filter'] ) ) {
            if( $post->post_excerpt )
                return $post->post_excerpt;
            else
                return wp_trim_words( $sections['description'], 25 );
        }

        // Create our links
        $output = '<div class="arconix-plugin-top">';
        if( isset( $custom["_acpl_download"][0] ) ) {
            $url = esc_url( $custom["_acpl_download"][0] );
            $output .= "<a href='{$url}' class='arconix-plugin-download'>Download</a>";
        }
        if( isset( $custom["_acpl_demo"][0] ) ) {
            $url = esc_url( $custom["_acpl_demo"][0] );
            $output .= '<div><span>or</span></div>';
            $output .= "<a href='{$url}' class='arconix-plugin-demo'>Demo</a>";
        }
        if( isset( $custom["_acpl_donation"][0] ) ) {
            $url = esc_url( $custom["_acpl_donation"][0] );
            $output .= "<a href='{$url}' class='arconix-plugin-donation'>Donation</a>";
        }
        $output .= '</div>';

        $output .= $sections['description'];
        $output .= $sections['screenshots'];

        $output .= '<h3 class="arconix-plugin-links-title">Links</h3>';
        $output .= '<ul class="arconix-plugin-links arconix-plugin-links-left">';

        /* Add the Documentation Link */
        if( isset( $custom["_acpl_docs"][0] ) ) {
            $url = esc_url( $custom["_acpl_docs"][0] );
            $output .= "<li class='arconix-plugin-docs'><a href='{$url}'>Documentation</a></li>";
        }

        /* Add the Support Link */
        if( isset( $custom["_acpl_help"][0] ) ) {
            $url = esc_url( $custom["_acpl_help"][0] );
            $output .= "<li class='arconix-plugin-help'><a href='{$url}'>Support</a></li>";
        }

        $output .= '</ul>';
        $output .= '<ul class="arconix-plugin-links arconix-plugin-links-right">';

        /* Add the Dev Link */
        if( isset( $custom["_acpl_dev"][0] ) ) {
            $url = esc_url( $custom["_acpl_dev"][0] );
            $output .= "<li class='arconix-plugin-dev'><a href='{$url}'>Dev Board</a></li>";
        }

        /* Add the Source Code Link */
        if( isset( $custom["_acpl_source"][0] ) ) {
            $url = esc_url( $custom["_acpl_source"][0] );
            $output .= "<li class='arconix-plugin-source'><a href='{$url}'>Source Code</a></li>";
        }
        $output .= '</ul>';       

        $output = apply_filters( 'arconix_plugins_content_filter', $output );

        return $output;
    }

    /**
     * Retrieve the plugin data for wp.org hosted plugins.
     *
     * Accepts the plugin slug in question and checks for a stored transient.
     * If none exists, then it retrieves the plugin data from wp.org and
     * stores it as a transient. The transient data expires daily (in seconds)
     * by default or can be overridden by adding a filter
     *
     * @since  0.5
     * @param string $slug Plugin slug
     */
    function get_wporg_custom_plugin_data( $slug ) {
        $trans_slug = 'acpl-' . $slug;

        // Check for stored transient. Create one if none exists
        if( false === get_transient( $trans_slug ) ) {
            $request = array(
                'action' => 'plugin_information',
                'request' => serialize(
                    (object) array(
                        'slug' => $slug,
                        'fields' => array( 'description' => true )
                    )
                )
            );

            $repo = wp_remote_post( 'http://api.wordpress.org/plugins/info/1.0/', array( 'body' => $request ) );
            $response = $repo['body'];

            $expiration = apply_filters( 'arconix_plugins_transient_expiration', 86400 );

            // Save transient to the database
            set_transient( $trans_slug, $response, $expiration );
        }

        // Check for cached result
        $response = get_transient( $trans_slug );

        if( ! is_wp_error( $response ) )
            $plugin = $response;
        else
            echo "A transient error occured with {$slug}!";


        return $plugin;
    }

    /**
     * Pass in a time and receive the text for amount of time passed, e.g. '2 months ago'
     *
     * Time periods and tense are filterable
     *
     * @link http://www.php.net/manual/en/function.time.php#91864
     *
     * @param string $tm Time to check against
     * @param int $rcs Number of levels deep (e.g. 2 minutes 20 seconds ago)
     * @return string Difference between param time and now
     */
    function ago( $tm, $rcs = 0 ) {
        $defaults = apply_filters( 'arconix_plugins_ago_defaults', array(
          'periods' => array( 'second', 'minute', 'hour', 'day', 'week', 'month', 'year', 'decade' ),
          'tense' => 'ago'
        ) );

        $cur_tm = time();
        $dif = $cur_tm - $tm;
        $pds = $defaults['periods'];
        $lngh = array( 1, 60, 3600, 86400, 604800, 2630880, 31570560, 315705600 );

        for( $v = sizeof( $lngh ) - 1; ($v >= 0) && (($no = $dif / $lngh[$v]) <= 1); $v-- )
            ; if( $v < 0 )
            $v = 0; $_tm = $cur_tm - ($dif % $lngh[$v]);

        $no = floor( $no );
        if( $no <> 1 )
            $pds[$v] .= 's'; $x = sprintf( "%d %s ", $no, $pds[$v] );
        if( ( $rcs == 1 ) && ( $v >= 1 ) && ( ( $cur_tm - $_tm ) > 0 ) )
            $x .= ago( $_tm );

        return $x . $defaults['tense'];
    }

}


/**
 * Load the plugin custom metabox
 *
 * @since 0.1
 */
function arconix_plugins_init_meta_boxes() {
    if( ! class_exists( 'cmb_Meta_Box' ) )
        require_once( plugin_dir_path( __FILE__ ) . '/includes/metabox/init.php' );
}

require_once( plugin_dir_path( __FILE__ ) . '/includes/class-widgets.php' );

new Arconix_Plugins;