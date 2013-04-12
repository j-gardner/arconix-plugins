<?php
$prefix = '_acpl_'; // Set the prefix for the metabox registration (WHICH IS NOT WORKING CURRENTLY)

$defaults = array(
	'post_type' => array(
		'slug' => 'plugins',
		'args' => array(
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
            'supports'          => array( 'title', 'thumbnail', 'excerpt' ),
            'rewrite'           => array( 'slug' => 'plugins', 'with_front' => false )
        )
	),
	'meta_box' => array(
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
    )
);