<?php

/**
 * Register the post-type
 * 
 * @since 0.1
 */
function create_post_type() {

    $args = apply_filters( 'arconix_plugins_post_type_args', 
        array(
            'labels' => array(
                'name'                  => __( 'Plugins',                       'acpl' ),
                'singular_name'         => __( 'Plugins',                       'acpl' ),
                'add_new'               => __( 'Add New',                       'acpl' ),
                'add_new_item'          => __( 'Add New Plugin',                'acpl' ),
                'edit'                  => __( 'Edit',                          'acpl' ),
                'edit_item'             => __( 'Edit Plugin',                   'acpl' ),
                'new_item'              => __( 'New Plugin',                    'acpl' ),
                'view'                  => __( 'View Plugin',                   'acpl' ),
                'view_item'             => __( 'View Plugin',                   'acpl' ),
                'search_items'          => __( 'Search Plugins',                'acpl' ),
                'not_found'             => __( 'No plugins found',              'acpl' ),
                'not_found_in_trash'    => __( 'No plugins found in the trash', 'acpl' )
            ),
            'public'            => true,
            'query_var'         => true,
            'menu_position'     => 100,
            'has_archive'       => true,
            'supports'          => array( 'title', 'editor', 'excerpt', 'revisions' ),
            'rewrite'           => array( 'slug' => 'plugins', 'with_front' => false )
        )
    );
    
    register_post_type( 'plugins', $args );
    flush_rewrite_rules( false );
}
