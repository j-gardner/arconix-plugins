<?php
/**
 * Modifies the post save notifications to properly reflect the post-type
 *
 * @global type $post
 * @global type $post_ID
 * @param type $messages
 * @return type array $messages *
 * @since 0.1
 */
function acpl_updated_messages( $messages ) {
    global $post, $post_ID;

    $messages['plugins'] = array(
        0 => '', // Unused. Messages start at index 1.
        1 => sprintf( __( 'Plugin updated. <a href="%s">View plugin</a>' ), esc_url( get_permalink( $post_ID ) ) ),
        2 => __( 'Custom field updated.' ),
        3 => __( 'Custom field deleted.' ),
        4 => __( 'Plugin updated.' ),
        /* translators: %s: date and time of the revision */
        5 => isset( $_GET['revision'] ) ? sprintf( __( 'Plugin restored to revision from %s' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
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
 * Filter the columns on the admin screen and define our own
 *
 * @param type $columns
 * @return string $columns
 * @since 0.1
 */
function acpl_columns_filter( $columns ) {
    $columns = array(
        "cb" => "<input type=\"checkbox\" />",
        "title" => "Plugin Name",
        "plugin_desc" => "Description",
        "date" => "Date"
    );

    return $columns;
}

/**
 * Filter the data that shows up in the columns we defined above
 *
 * @global type $post
 * @param type $column
 * @since 0.1
 */
function acpl_columns_data( $column ) {
    global $post;

    switch( $column ) {
        case "plugin_desc":
            the_excerpt();
            break;

        default:
            break;
    }
}

/**
 * Add the Post type to the "Right Now" Dashboard Widget
 *
 * @link http://bajada.net/2010/06/08/how-to-add-custom-post-types-and-taxonomies-to-the-wordpress-right-now-dashboard-widget
 * @since 0.1
 */
function acpl_right_now() {
    /* Define the post type text here, allowing us to quickly re-use this code in other projects */
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
        echo '<td class="t '. $ac_pt . '">' . $text . '</td>';
        echo '</tr>';
    }
}

/**
 * Customize the "Enter title here" text
 *
 * @param string $title
 * @return $title
 * @since 0.3
 */
function acpl_custom_title_text( $title ) {
    $screen = get_current_screen();

    if( 'plugins' == $screen->post_type ) {
        $title = __( 'Enter the plugin name', 'acpl' );
    }
    return $title;
}