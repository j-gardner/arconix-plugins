<?php
/**
 * Check for post-thumbnails and support for it to the post type
 *
 * @global type $_wp_theme_features
 * @since 0.1
 * @version 0.3
 */
function acpl_post_thumbnail_support() {
    global $_wp_theme_features;

    if( !isset( $_wp_theme_features['post-thumbnails'] ) )
        $_wp_theme_features['post-thumbnails'] = array( array( 'plugins' ) );
    elseif ( is_array( $_wp_theme_features['post-thumbnails'] ) )
        $_wp_theme_features['post-thumbnails'][0][] = 'plugins';
}

/**
 * Load the necessary css, which can be overriden by creating your own file and placing it in
 * the root of your theme's folder
 *
 * @since 0.1
 * @version 0.3
 */
function acpl_enqueue_script() {
    if( file_exists( get_stylesheet_directory() . '/arconix-plugins.css' ) )
        wp_enqueue_style( 'arconix-plugins', get_stylesheet_directory_uri() . '/arconix-plugins.css', false, ACPL_VERSION );
    elseif( file_exists( get_template_directory() . '/arconix-plugins.css' ) )
        wp_enqueue_style( 'arconix-plugins', get_template_directory_uri() . '/arconix-plugins.css', false, ACPL_VERSION );
    else
        wp_enqueue_style( 'arconix-plugins', ACPL_INCLUDES_DIR . 'plugins.css', false, ACPL_VERSION );
}

/**
 * Filter the_content to add post type related information
 *
 * @param type $content
 * @return type $content
 * @since 0.1
 * @version 0.3
 */
function acpl_content_filter( $content ) {
    /* Exit if the theme is displaying the excerpt */
    if( in_array( 'get_the_excerpt', $GLOBALS['wp_current_filter'] ) )
        return $content;

    $output = '';

    if( 'plugins' == get_post_type() ) {
        $custom = get_post_custom();

        /* Extract the values of the various meta boxes into variables */
        isset( $custom["_acpl_example"][0] ) ? $demo = $custom["_acpl_example"][0] : $demo = "";
        isset( $custom["_acpl_download"][0] ) ? $download = $custom["_acpl_download"][0] : $download = "";
        isset( $custom["_acpl_docs"][0] ) ? $docs = $custom["_acpl_docs"][0] : $docs = "";
        isset( $custom["_acpl_help"][0] ) ? $help = $custom["_acpl_help"][0] : $help = "";
        isset( $custom["_acpl_dev"][0] ) ? $dev = $custom["_acpl_dev"][0] : $dev = "";
        isset( $custom["_acpl_source"][0] ) ? $source = $custom["_acpl_source"][0] : $source = "";

        $output .= '<h3 class="arconix-plugin-title">Links</h3>';
        $output .= '<ul class="arconix-plugin-links">';
        $output .= '<li class="arconix-plugin-demo"><a href="' . $demo . '">Demo</a></li>';
        $output .= '<li class="arconix-plugin-download"><a href="' . $download . '">Download</a></li>';
        $output .= '<li class="arconix-plugin-docs"><a href="' . $docs . '">Documentation</a></li>';
        $output .= '<li class="arconix-plugin-help"><a href="' . $help . '">Support</a></li>';
        $output .= '<li class="arconix-plugin-dev"><a href="' . $dev . '">Dev Board</a></li>';
        $output .= '<li class="arconix-plugin-source"><a href="' . $source . '">Source Code</a></li>';
        $output .= '</ul>';
    }

    $return = $content . $output;
    $return = apply_filters( 'arconix_plugins_content_filter', $return );

    return $return;
}

/**
 * Register the Post Type metabox
 *
 * @return type array $meta_boxes
 * @since 0.1
 */
function acpl_create_meta_box( array $meta_boxes ) {
    $prefix = '_acpl_';

    $meta_boxes[] = array(
        'id' => 'plugins',
        'title' => 'Plugin Details',
        'pages' => array( 'plugins' ), // post type
        'context' => 'normal',
        'priority' => 'high',
        'show_names' => true, // Show field names left of input
        'fields' => array(
            array(
                'name' => 'Demo',
                'desc' => 'Enter the demo URL',
                'id' => $prefix . 'demo',
                'type' => 'text_medium',
            ),
            array(
                'name' => 'Download',
                'desc' => 'Enter the download URL.',
                'id' => $prefix . 'download',
                'type' => 'text_medium'
            ),
            array(
                'name' => 'Documentation',
                'desc' => 'Enter the documentation URL.',
                'id' => $prefix . 'docs',
                'type' => 'text_medium'
            ),
            array(
                'name' => 'Support',
                'desc' => 'Enter the support URL.',
                'id' => $prefix . 'help',
                'type' => 'text_medium'
            ),
            array(
                'name' => 'Development',
                'desc' => 'Enter the development board URL.',
                'id' => $prefix . 'dev',
                'type' => 'text_medium'
            ),
            array(
                'name' => 'Source Code',
                'desc' => 'Enter the source code URL.',
                'id' => $prefix . 'source',
                'type' => 'text_medium'
            )
        )
    );

    return $meta_boxes;
}