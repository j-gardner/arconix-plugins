<?php
/**
 * Defines and handles all backend plugin operation
 *
 * @since   1.0.0
 */
class Arconix_Plugins_Admin {

    /**
     * The version of this plugin.
     *
     * @since   1.0.0
     * @access  private
     * @var     string      $version    The vurrent version of this plugin.
     */
    private $version;

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
    public function __construct( $version ) {
        $this->version = $version;
        $this->dir = trailingslashit( plugin_dir_path( __FILE__ ) );
        $this->url = trailingslashit( plugin_dir_url( __FILE__ ) );

        register_activation_hook( __FILE__,                 array( $this, 'activation' ) );
        register_deactivation_hook( __FILE__,               array( $this, 'deactivation' ) );

        add_action( 'init',                                 array( $this, 'content_types' ) );
        add_action( 'manage_plugins_posts_custom_column',   array( $this, 'custom_columns_action' ) );
        add_action( 'dashboard_glance_items',               array( $this, 'at_a_glance' ) );
        add_action( 'admin_enqueue_scripts',                array( $this, 'admin_css' ) );
        add_action( 'wp_enqueue_scripts',                   array( $this, 'scripts' ) );
        add_action( 'widgets_init',                         array( $this, 'plugin_widgets' ) );

        add_filter( 'the_content',                          array( $this, 'content_filter' ) );
        add_filter( 'manage_plugins_posts_columns',         array( $this, 'custom_columns_filter' ) );
        add_filter( 'cmb_meta_boxes',                       array( $this, 'metaboxes' ) );
        add_filter( 'post_updated_messages',                array( $this, 'messages' ) );
    }

    /**
     * Runs on plugin activation.
     *
     * @since   0.1
     * @version 0.5
     */
    public function activation() {
        flush_rewrite_rules();
    }

    /**
     * Runs on plugin deactivation.
     *
     * @since   0.1
     * @version 0.2
     */
    public function deactivation() {
        flush_rewrite_rules();
    }

    /**
     * Set our plugin defaults for post type and metabox registration
     *
     * @since   0.5
     * @return  array $defaults
     */
    public function defaults() {
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
                    'menu_icon'         => 'dashicons-admin-plugins',
                    'has_archive'       => true,
                    'supports'          => array( 'title', 'thumbnail', 'excerpt' ),
                    'rewrite'           => array( 'with_front' => false )
                )
            )
        );

        return apply_filters( 'arconix_plugins_defaults', $defaults );
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
     * Register the 'Plugin' post type.
     *
     * @since   0.1
     * @version 0.5
     */
    public function content_types() {
        $defaults = $this->defaults();
        register_post_type( $defaults['post_type']['slug'], $defaults['post_type']['args'] );
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
                    __e( 'No Plugin data returned', 'acpl' );
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
     * Create our custom meta box for the plugin post type
     *
     * @since   0.1
     * @version 0.5
     * @param   array $meta_boxes   Existing meta box array
     * @return  array $meta_boxes   Meta box array including our meta box
     */
    public function metaboxes( $meta_boxes ) {
        $prefix = "_acpl_";

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
     * @since   0.1
     * @version 1.0.0
     */
    public function scripts() {
        if( apply_filters( 'pre_register_arconix_plugins_css', true ) ) {
            if( file_exists( get_stylesheet_directory() . '/arconix-plugins.css' ) )
                wp_enqueue_style( 'arconix-plugins', get_stylesheet_directory_uri() . '/arconix-plugins.css', false, $this->version );
            elseif( file_exists( get_template_directory() . '/arconix-plugins.css' ) )
                wp_enqueue_style( 'arconix-plugins', get_template_directory_uri() . '/arconix-plugins.css', false, $this->version );
            else
                wp_enqueue_style( 'arconix-plugins', $this->url . 'css/arconix-plugins.css', false, $this->version );
        }
    }

    /**
     * Includes admin css
     *
     * @since   0.1.0
     * @version 1.0.0
     */
    public function admin_css() {
        wp_enqueue_style( 'arconix-plugins-admin', $this->url . 'css/admin.css', false, $this->version );
    }

    /**
     * Add the Post type to the "At a Glance" Dashboard Widget.
     *
     * @since   0.1
     * @version 1.0.0
     */
    public function at_a_glance() {
        require_once( $this->dir . 'class-gamajo-dashboard-glancer.php' );
        $glancer = new Gamajo_Dashboard_Glancer;
        $glancer->add( 'plugins' );
    }

    /**
     * Updated Messages to display on Post Type Edit screen
     *
     * @since   0.1
     * @version 1.0.0
     * @global  object  $post
     * @global  int     $post_ID    ID of current post
     * @param   array   $messages   existing messages to update
     * @return  array               updated messages
     */
    public function messages( $messages ) {
        global $post, $post_ID;
        $post_type = get_post_type( $post_ID );

        $obj = get_post_type_object( $post_type );
        $singular = $obj->labels->singular_name;

        $messages[$post_type] = array(
            0  => '', // Unused. Messages start at index 1.
            1  => sprintf( __( $singular . ' updated. <a href="%s">View ' . strtolower( $singular ) . '</a>' ), esc_url( get_permalink( $post_ID ) ) ),
            2  => __( 'Custom field updated.' ),
            3  => __( 'Custom field deleted.' ),
            4  => __( $singular . ' updated.' ),
            5  => isset( $_GET['revision'] ) ? sprintf( __( $singular . ' restored to revision from %s' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
            6  => sprintf( __( $singular . ' published. <a href="%s">View ' . strtolower( $singular ) . '</a>' ), esc_url( get_permalink( $post_ID ) ) ),
            7  => __( 'Page saved.' ),
            8  => sprintf( __( $singular . ' submitted. <a target="_blank" href="%s">Preview ' . strtolower( $singular ) . '</a>' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
            9  => sprintf( __( $singular . ' scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview ' . strtolower( $singular ) . '</a>' ), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) ),
            10 => sprintf( __( $singular . ' draft updated. <a target="_blank" href="%s">Preview ' . strtolower( $singular ) . '</a>' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
        );

        return $messages;
    }
}