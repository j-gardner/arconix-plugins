<?php
/**
 * Defines and handles the Staff Custom Post Type
 *
 * @since   0.5.0
 */
class Arconix_Plugins_Content_Type {

    /**
     * Initialize the class and set its properties.
     *
     * @since   0.5.0
     * @access  public
     */
    public function __construct() {
        add_action( 'init',                         array( $this, 'content_types' ) );
        add_filter( 'post_updated_messages',        array( $this, 'updated_messages' ) );
    }

    /**
     * Register the post_type
     *
     * @since   0.5.0
     * @return  void
     */
    public function content_types() {
        $defaults = $this->defaults();
        register_post_type( $defaults['post_type']['slug'], $defaults['post_type']['args'] );
    }

    /**
     * Define the defaults used in the registration of the post type
     *
     * @since   0.1.0
     * @return  array   $defaults
     */
    public function defaults() {
        $defaults = array(
            'post_type' => array(
                'slug' => 'plugins',
                'args' => array(
                    'labels' => array(
                        'name'                  => __( 'WP Plugins',                    'arconix-plugins' ),
                        'singular_name'         => __( 'Plugin',                        'arconix-plugins' ),
                        'add_new_item'          => __( 'Add New Plugin',                'arconix-plugins' ),
                        'edit_item'             => __( 'Edit Plugin',                   'arconix-plugins' ),
                        'new_item'              => __( 'New Plugin',                    'arconix-plugins' ),
                        'view_item'             => __( 'View Plugin',                   'arconix-plugins' ),
                        'search_items'          => __( 'Search Plugins',                'arconix-plugins' ),
                        'not_found'             => __( 'No plugins found',              'arconix-plugins' ),
                        'not_found_in_trash'    => __( 'No plugins found in the trash', 'arconix-plugins' )
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
     * Correct messages when post type is saved
     *
     * @since   0.1.0
     * @global  stdObject    $post              WP Post object
     * @global  int          $post_ID           Post ID
     * @param   array        $messages          Existing array of messages
     * @return  array                           updated messages
     */
    public function updated_messages( $messages ) {
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