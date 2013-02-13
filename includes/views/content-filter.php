<?php
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
        //return $details->short_description;
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