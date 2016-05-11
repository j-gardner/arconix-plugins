<?php
/**
 * Defines and handles all backend plugin operation
 *
 * @since   1.0.0
 */
class Arconix_Plugins_Admin {

    /**
     * The directory path to this plugin.
     *
     * @since   1.0.0
     * @access  private
     * @var     string      $dir    The directory path to this plugin
     */
    private $dir;

    /**
     * The url path to this plugin.
     *
     * @since   1.0.0
     * @access  private
     * @var     string      $url    The url path to this plugin
     */
    private $url;

    /**
     * Initialize the class and set its properties.
     *
     * @since   0.1
     * @version 1.0.0
     * @access  private
     * @param   string      $version    The version of this plugin.
     */
    public function __construct() {
        $this->dir = trailingslashit( plugin_dir_path( __FILE__ ) );
        $this->url = trailingslashit( plugin_dir_url( __FILE__ ) );
        $this->init();
    }


    /**
     * Init the Admin side
     *
     * Loads all actions and filters to be used.
     *
     * @since   1.0.1
     */
    public function init() {
        add_action( 'manage_plugins_posts_custom_column',   array( $this, 'custom_columns_action' ) );
        add_action( 'admin_enqueue_scripts',                array( $this, 'admin_css' ) );
        add_action( 'wp_enqueue_scripts',                   array( $this, 'scripts' ) );
        //add_action( 'widgets_init',                         array( $this, 'plugin_widgets' ) );

        add_filter( 'the_content',                          array( $this, 'content_filter' ) );
        add_filter( 'manage_plugins_posts_columns',         array( $this, 'custom_columns_filter' ) );
    }

    /**
     * Register the plugin widget(s)
     *
     * @since 0.5
     */
    public function plugin_widgets() {
        $widgets = array( 'Arconix_Widget_Plugin_Details', 'Arconix_Widget_Plugin_Resources', 'Arconix_Widget_Plugin_Related' );

        foreach ( $widgets as $widget ) {
            register_widget( $widget );
        }

    }

    /**
     * Modify the list of columns available for our custom post type
     *
     * @since   0.1
     * @version 0.5
     * @param   array $columns  Existing post_type columns
     * @return  array $columns  New post_type columns
     */
    public function custom_columns_filter( $columns ) {
        $columns = array(
            'cb'                => '<input type="checkbox" />',
            'title'             => __( 'Title', 'acpl' ),
            'plugins_details'   => __( 'Details', 'acpl' )
        );

        return $columns;
    }

    /**
     * Define the data that shows up in the columns we set.
     *
     * @since   0.1
     * @version 0.5
     * @param   array   $column Column whose output is being defined
     */
    public function custom_columns_action( $column ) {

        $p = new Arconix_Plugin();

        switch( $column ) {
            case 'plugins_details':
                // Get the slug set by the custom post type entry
                $slug = $p->get_slug();

                // Bail out if slug wasn't set
                if ( ! $slug ) break;

                // Pass the slug into the api to get the data we need, bailing out if we don't get anything back
                $details = $p->get_wporg_custom_plugin_data( $slug );

                if ( ! $details ) {
                    _e( 'No Plugin data returned', 'acpl' );
                    break;
                }

                echo 'Latest Version: ' . $p->get_version( $details ) . '<br />';
                echo 'Last Updated: ' . $p->get_last_updated( $details ) . ' <em style="color: #aaa;">(' . $p->ago( strtotime( $details->last_updated ) ) . ')</em><br />';
                echo 'Downloads: ' . $p->get_downloads( $details );

                break;
        }
    }

    /**
     * Add our plugin data to the content
     *
     * @since   0.3
     * @version 0.5
     * @param   string  $content
     * @return  string  $content    Modified post content
     */
    public function content_filter( $content ) {
        global $post;

        if( ! 'plugins' == get_post_type() ) return $content;

        $p = new Arconix_Plugin();

        $slug = $p->get_slug( $post->ID );

        // Return the content if $slug has no value (useful if plugin is not being hosted on wp.org or isn't live yet)
        if( ! $slug ) return $content;

        // Pass the slug into the API to get the data we need
        $details = $this->get_wporg_custom_plugin_data( $slug );

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

        $custom = get_post_custom();

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
     * Load the necessary css, which can be overriden by creating your own file and placing it in
     * the root of your theme's folder
     *
     * @since   0.1
     * @version 1.0.0
     */
    public function scripts() {

        if ( ! current_theme_supports( 'arconix_plugins' ) && apply_filters( 'pre_register_arconix_plugins_css', true ) ) {
            if( file_exists( get_stylesheet_directory() . '/arconix-plugins.css' ) )
                wp_enqueue_style( 'arconix-plugins', get_stylesheet_directory_uri() . '/arconix-plugins.css', false, Arconix_Plugins::VERSION );
            elseif( file_exists( get_template_directory() . '/arconix-plugins.css' ) )
                wp_enqueue_style( 'arconix-plugins', get_template_directory_uri() . '/arconix-plugins.css', false, Arconix_Plugins::VERSION );
            else
                wp_enqueue_style( 'arconix-plugins', $this->url . 'css/arconix-plugins.css', false, Arconix_Plugins::VERSION );
        }
    }

    /**
     * Includes admin css
     *
     * @since   0.1.0
     * @version 1.0.0
     */
    public function admin_css() {
        wp_enqueue_style( 'arconix-plugins-admin', $this->url . 'css/admin.css', false, Arconix_Plugins::VERSION );
    }

}