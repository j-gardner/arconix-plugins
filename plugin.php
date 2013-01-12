<?php
/**
 * Plugin Name: Arconix Plugins
 * Plugin URI: http://arconixpc.com/
 * Description: Custom Plugin for consistently creating and displaying user-developed plugins on a website
 *
 * Version: 0.5
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

        add_action( 'init', array( $this, 'register_content_types' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'manage_plugins_posts_custom_column', array( $this, 'custom_columns_action' ) );
        add_action( 'right_now_content_table_end', array( $this, 'custom_right_now' ) );

        add_filter( 'manage_plugins_posts_columns', array( $this,'custom_columns_filter' ) );
        add_filter( 'cmb_meta_boxes', array( $this, 'custom_meta_box' ) );
        add_filter( 'post_updated_messages', array( $this, 'updated_post_type_messages' ) );
        add_filter( 'the_content', array( $this, 'content_filter' ) );

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

    /**
     * Register the post type
     *
     * @since 0.1
     */
    function register_content_types() {
        $args = array(
            'labels' => array(
                'name'                  => __( 'Plugins',                       'acpl' ),
                'singular_name'         => __( 'Plugin',                        'acpl' ),
                'add_new'               => __( 'Add New',                       'acpl' ),
                'add_new_item'          => __( 'Add New Plugin',                'acpl' ),
                'edit'                  => __( 'Edit',                          'acpl' ),
                'edit_item'             => __( 'Edit Plugin',                   'acpl' ),
                'new_item'              => __( 'New Plugin',                    'acpl' ),
                'view'                  => __( 'View',                          'acpl' ),
                'view_item'             => __( 'View Plugin',                   'acpl' ),
                'search_items'          => __( 'Search Plugins',                'acpl' ),
                'not_found'             => __( 'No plugins found',              'acpl' ),
                'not_found_in_trash'    => __( 'No plugins found in the trash', 'acpl' )
            ),
            'public'            => true,
            'query_var'         => true,
            'menu_position'     => 100,
            'has_archive'       => true,
            'supports'          => array( 'title', 'editor', 'excerpt', 'custom-fields' ),
            'rewrite'           => array( 'slug' => 'plugins', 'with_front' => false )
        );
        $args = apply_filters( 'arconix_plugins_post_type_args', $args );
        register_post_type( 'plugins', $args );
        flush_rewrite_rules( false );
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
    function updated_post_type_messages( $messages ){
        global $post, $post_ID;

        $messages['plugins'] = array(
            0 => '', // Unused. Messages start at index 1.
            1 => sprintf( __( 'Plugin updated. <a href="%s">View plugin</a>' ), esc_url( get_permalink( $post_ID ) ) ),
            2 => __( 'Custom field updated.' ),
            3 => __( 'Custom field deleted.' ),
            4 => __( 'Plugin updated.' ),
            /* translators: %s: date and time of the revision */
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
    function custom_right_now() {
        /* Define the post type text here */
        $ac_pt = 'plugins'; // must be the registered post type
        $ac_pt_s = 'Plugin';

        /* No need to modify these */
        $ac_pt_p = ucfirst( $ac_pt );
        $ac_pt_pp = $ac_pt_p . ' Pending';
        $ac_pt_sp = $ac_pt_s . ' Pending';

        $args = array(
            'public' => true,
            '_builtin' => false
        );
        $output = 'object';
        $operator = 'and';

        $num_posts = wp_count_posts( $ac_pt );
        $num = number_format_i18n( $num_posts->publish );
        $text = _n( $ac_pt_s, $ac_pt_p, intval( $num_posts->publish ) );

        if( current_user_can( 'edit_posts' ) ) {
            $num = "<a href='edit.php?post_type=$ac_pt'>$num</a>";
            $text = "<a href='edit.php?post_type=$ac_pt'>$text</a>";
        }

        echo '<td class="first b b-' . $ac_pt . '">' . $num . '</td>';
        echo '<td class="t ' . $ac_pt . '">' . $text . '</td>';
        echo '</tr>';

        if( $num_posts->pending > 0 ) {
            $num = number_format_i18n( $num_posts->pending );
            $text = _n( $ac_pt_sp, $ac_pt_pp, intval( $num_posts->pending ) );

            if( current_user_can( 'edit_posts' ) ) {
                $num = "<a href='edit.php?post_status=pending&post_type='$ac_pt'>$num</a>";
                $text = "<a href='edit.php?post_status=pending&post_type=$ac_pt'>$text</a>";
            }

            echo '<td class="first b b-' . $ac_pt . '">' . $num . '</td>';
            echo '<td class="t ' . $ac_pt . '">' . $text . '</td>';
            echo '</tr>';
        }
    }

    /**
     * Filter the data that shows up in the columns we defined above
     *
     * @uses wpapi  http://wpapi.org/
     * @since 0.1
     * @global object $post
     * @param array $column
     */
    function custom_columns_action( $column ) {
        /**
         * @todo Use the wpapi to get stats for items
         */
        global $post;

        switch( $column ) {
            case 'plugins_details':

                break;
            case 'plugins_stats':

                break;
        }
    }

    /**
     * Add our own Columns to our custom post type browse screen
     *
     * @since 0.1
     * @version 0.5
     * @param array $columns
     * @return array $columns
     */
    function custom_columns_filter( $columns ) {
        /* Define the columns we want to remove, then loop through and remove them */
        $removals = array( 'author', 'categories', 'tags', 'comments' );
        foreach( $removals as $removal ) {
            if( isset( $removal ) )
                unset( $removal );
        }

        /* Define the columns we want to add */
        $additions = array(
            'plugins_details' => 'Details',
            'plugins_stats' => 'Stats'
        );

        /* Return our additional columns */
        return array_merge( $columns, $additions );
    }


    function custom_meta_box( array $meta_boxes ) {
        $prefix = '_acpl_';

        $meta_boxes[] = array(
            'id' => 'plugins_box',
            'title' => 'Plugin Details',
            'pages' => array( 'plugins' ), // post type
            'context' => 'normal',
            'priority' => 'high',
            'show_names' => true, // Show field names left of input
            'fields' => array(
                array(
                    'name'      => 'Slug',
                    'desc'      => 'Enter the plugin\'s slug',
                    'id'        => $prefix . 'slug',
                    'type'      => 'text_medium',
                ),
                array(
                    'name'      => 'Demo',
                    'desc'      => 'Enter the demo URL',
                    'id'        => $prefix . 'demo',
                    'type'      => 'text_medium',
                ),
                array(
                    'name'      => 'Download',
                    'desc'      => 'Enter the download URL.',
                    'id'        => $prefix . 'download',
                    'type'      => 'text_medium'
                ),
                array(
                    'name'      => 'Documentation',
                    'desc'      => 'Enter the documentation URL.',
                    'id'        => $prefix . 'docs',
                    'type'      => 'text_medium'
                ),
                array(
                    'name'      => 'Support',
                    'desc'      => 'Enter the support URL.',
                    'id'        => $prefix . 'help',
                    'type'      => 'text_medium'
                ),
                array(
                    'name'      => 'Development',
                    'desc'      => 'Enter the development board URL.',
                    'id'        => $prefix . 'dev',
                    'type'      => 'text_medium'
                ),
                array(
                    'name'      => 'Source Code',
                    'desc'      => 'Enter the source code URL.',
                    'id'        => $prefix . 'source',
                    'type'      => 'text_medium'
                )
            )
        );

        return $meta_boxes;
    }




    /**
     * Load the necessary css, which can be overriden by creating your own file and placing it in
     * the root of your theme's folder
     *
     * @since 0.1
     * @version 0.3
     */
    function enqueue_scripts() {
        if( file_exists( get_stylesheet_directory() . '/arconix-plugins.css' ) )
            wp_enqueue_style( 'arconix-plugins', get_stylesheet_directory_uri() . '/arconix-plugins.css', false, ACPL_VERSION );
        elseif( file_exists( get_template_directory() . '/arconix-plugins.css' ) )
            wp_enqueue_style( 'arconix-plugins', get_template_directory_uri() . '/arconix-plugins.css', false, ACPL_VERSION );
        else
            wp_enqueue_style( 'arconix-plugins', ACPL_INCLUDES_DIR . 'plugins.css', false, ACPL_VERSION );
    }


    function content_filter( $content ){
        /* Exit if the theme is displaying the excerpt */
        if( in_array( 'get_the_excerpt', $GLOBALS['wp_current_filter'] ) )
            return $content;

        $output = '';

        if( 'plugins' == get_post_type() ) {
            $custom = get_post_custom();

            /* Extract the values of the various meta boxes into variables */
            isset( $custom["_acpl_demo"][0] ) ? $demo = $custom["_acpl_demo"][0] : $demo = "";
            isset( $custom["_acpl_download"][0] ) ? $download = $custom["_acpl_download"][0] : $download = "";
            isset( $custom["_acpl_docs"][0] ) ? $docs = $custom["_acpl_docs"][0] : $docs = "";
            isset( $custom["_acpl_help"][0] ) ? $help = $custom["_acpl_help"][0] : $help = "";
            isset( $custom["_acpl_dev"][0] ) ? $dev = $custom["_acpl_dev"][0] : $dev = "";
            isset( $custom["_acpl_source"][0] ) ? $source = $custom["_acpl_source"][0] : $source = "";

            $top = '<div class="arconix-plugin-top">';
            $top .= "<a href='{$download}' class='arconix-plugin-download'>Download</a>";
            if( $demo ) {
                $top .= '<div><span>or</span></div>';
                $top .= "<a href='{$demo}' class='arconix-plugin-demo'>Demo</a>";
            }
            $top .= '</div>';

            $output .= '<h3 class="arconix-plugin-links-title">Links</h3>';
            $output .= '<ul class="arconix-plugin-links arconix-plugin-links-left">';
            $output .= "<li class='arconix-plugin-docs'><a href='{$docs}'>Documentation</a></li>";
            $output .= "<li class='arconix-plugin-help'><a href='{$help}'>Support</a></li>";
            $output .= '</ul>';
            $output .= '<ul class="arconix-plugin-links arconix-plugin-links-right">';
            $output .= "<li class='arconix-plugin-dev'><a href='{$dev}'>Dev Board</a></li>";
            $output .= "<li class='arconix-plugin-source'><a href='{$source}'>Source Code</a></li>";
            $output .= '</ul>';
        }

        $return = $top . $content . $output;
        $return = apply_filters( 'arconix_plugins_content_filter', $return );

        return $return;
    }

}

/**
 * Conditionally load the Custom Meta Box class
 */
if( !class_exists( 'cmb_Meta_Box' ) ) {
    add_action( 'init', 'arconix_plugins_init_meta_boxes', 9999 );
    require_once( plugin_dir_path( __FILE__ ) . '/includes/metabox/init.php' );
}

new Arconix_Plugins;
?>