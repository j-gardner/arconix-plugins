<?php
// Define the post type text here
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