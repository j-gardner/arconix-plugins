<?php
/**
 * Individual Plugin Details widget
 *
 * Displays plugin details for the plugin on the current page (Single Plugin view)
 *
 * @since 0.5
 */
class Arconix_Widget_Plugin_Details extends WP_Widget {
    function __construct() {
        $widget_ops = array(
            'classname'     => 'widget_plugin_details',
            'description'   => __( 'Display additional details about the WP.org hosted plugin', 'acpl' ),
        );
        parent::__construct( 'arconix-plugins-details', __( 'Arconix Plugin Details', 'acpl' ), $widget_ops );
    }

    /**
     * Widget Output
     *
     * @param type $args Display arguments including before_title, after_title, before_widget, and after_widget.
     * @param type $instance The settings for the particular instance of the widget
     * @since 0.5
     */
    function widget( $args, $instance ) {
        extract( $args );

        // Before widget (defined by themes)
        echo $before_widget;

        // Output our plugin details
        if( ! is_singular( 'plugins' ) ) return;

        global $post;

        $custom = get_post_custom();
        isset( $custom["_acpl_slug"][0] )? $slug = $custom["_acpl_slug"][0] : $slug = '';

        // Bail if $slug has no value (useful if plugin is not being hosted on WP.org or isn't live yet)
        if( ! $slug ) return;

        // Grab the plugin details from the WP.org servers
        $details = unserialize( ARCONIX_PLUGINS::get_wporg_custom_plugin_data( $slug ) );

        // Bail out here if there's a problem with the WP server, etc...
        if( ! $details ) return;

        // Set our variables
        $plugname   = $details->name;
        $plugtitle  = $plugname . ' Details';
        $version    = $details->version;
        $requires   = $details->requires;
        $compatible = $details->tested;
        $updated    = date( get_option( 'date_format' ), strtotime( $details->last_updated ) );
        $ago        = ARCONIX_PLUGINS::ago( strtotime( $details->last_updated ) );
        $downloads  = number_format( $details->downloaded );
        $downlink   = $details->download_link;
        $demolink   = esc_url( $custom["_acpl_demo"][0] );
        $donatlink  = esc_url( $custom["_acpl_donate"][0] );


        echo $before_title . $plugtitle . $after_title;
        
        echo "<table class='arconix-plugins-table arconix-plugins-table-details'><tbody>";
        echo "<tr><td>Version</td><td>{$version}</td></tr>";
        echo "<tr><td>Requires</td><td>{$requires}</td></tr>";
        echo "<tr><td>Compatible</td><td>{$compatible}</td></tr>";
        //echo "<tr><td>Last Updated</td><td>{$updated} <span class='arconix-plugins-ago'>{$ago}</span></td></tr>";
        echo "<tr><td>Last Updated</td><td><span class='arconix-plugins-ago'>{$ago}</span></td></tr>";
        echo "<tr><td>Downloads</td><td>{$downloads}</td></tr>";
        echo "</tbody></table>";
        echo "<p class='arconix-plugins-button-area'>";
        echo "<a class='arconix-button arconix-button-large arconix-button-green arconix-button-download' href='{$downlink}'>Download</a>";
        if( $demolink )
            echo "<a class='arconix-button arconix-button-large arconix-button-silver arconix-button-demo' href='{$demolink}'>Demo</a>";
        if( $donatlink )
            echo "Enjoy this plugin? Please consider <a class='arconix-button-donate' href='{$donatlink}'>buying me a coffee</a>";
        echo "</p>";

        // After widget (defined by themes)
        echo $after_widget;

    }

    /**
     * Update a particular instance.
     *
     * @param array $new_instance New settings for this instance as input by the user via form()
     * @param array $old_instance Old settings for this instance
     * @return array Settings to save or bool false to cancel saving
     * @since 0.5
     */
    function update( $new_instance, $old_instance ) {
        return;
    }

    /**
     * Widget form
     *
     * @param array $instance Current settings
     * @since 0.5
     */
    function form( $instance ) {
        echo '<p>This widget will only work when displayed on an individual plugin page.</p>';
    }
}

/**
 * Show resources for the plugin using the links supplied on the individual plugin entry page
 *
 * @since 0.5
 */
class Arconix_Widget_Plugin_Resources extends WP_Widget {
    function __construct() {
        $widget_ops = array(
            'classname'     => 'widget_plugin_resources',
            'description'   => __( 'Resources for the plugin', 'acpl' ),
        );
        parent::__construct( 'arconix-plugins-resource', __( 'Arconix Plugin Resources', 'acpl' ), $widget_ops );
    }

    /**
     * Widget Output
     *
     * @param type $args Display arguments including before_title, after_title, before_widget, and after_widget.
     * @param type $instance The settings for the particular instance of the widget
     * @since 0.5
     */
    function widget( $args, $instance ) {
        // Bail if not a single-plugin
        if( ! is_singular( 'plugins' ) ) return;

        global $post;

        $custom = get_post_custom();
        isset( $custom["_acpl_slug"][0] )? $slug = $custom["_acpl_slug"][0] : $slug = '';

        // Bail if $slug has no value (useful if plugin is not being hosted on WP.org or isn't live yet)
        if( ! $slug ) return;

        // Grab the plugin details from the WP.org servers
        $details = unserialize( ARCONIX_PLUGINS::get_wporg_custom_plugin_data( $slug ) );

        // Bail out here if there's a problem with the WP server, etc...
        if( ! $details ) return;

        $plugname   = $details->name;
        $plugtitle  = $plugname . ' Resources';
        $help       = esc_url( $custom["_acpl_help"][0] );
        $docs       = esc_url( $custom["_acpl_docs"][0] );
        $dev        = esc_url( $custom["_acpl_dev"][0] );
        $source     = esc_url( $custom["_acpl_source"][0] );

        // Extract our theme-specific arguments
        extract( $args );

        // Before widget (defined by themes)
        echo $before_widget;

        echo $before_title . $plugtitle . $after_title;

        // Now return the rest of the plugin resources (docs, help, etc...)
        echo '<ul class="arconix-plugin-resources">';
        echo "<li><a class='arconix-plugin-docs' href='{$docs}'>Documentation</a></li>";
        echo "<li><a class='arconix-plugin-help' href='{$help}'>Support</a></li>";
        echo "<li><a class='arconix-plugin-dev' href='{$dev}'>Dev Board</a></li>";
        echo "<li><a class='arconix-plugin-source' href='{$source}'>Source Code</a></li>";
        echo '</ul>';

        // After widget (defined by themes)
        echo $after_widget;
    }

    /**
     * Update a particular instance.
     *
     * @param array $new_instance New settings for this instance as input by the user via form()
     * @param array $old_instance Old settings for this instance
     * @return array Settings to save or bool false to cancel saving
     * @since 0.5
     */
    function update( $new_instance, $old_instance ) {
        return $instance;
    }

    /**
     * Widget form
     *
     * @param array $instance Current settings
     * @since 0.5
     */
    function form( $instance ) {
        echo '<p>This widget will only work when displayed on an individual plugin page.</p>';
    }

}

/**
 * Show Blog posts tagged with the same slug as the plugin. Will only work on the single plugin page
 *
 * @since 0.5
 */
class Arconix_Widget_Plugin_Related extends WP_Widget {
    function __construct() {
        $widget_ops = array(
            'classname'     => 'widget_plugin_related',
            'description'   => __( 'Recent Posts tagged with the plugin slug', 'acpl' ),
        );
        parent::__construct( 'arconix-plugins-related', __( 'Arconix Plugin Related Posts', 'acpl' ), $widget_ops );
    }

    /**
     * Widget Output
     *
     * @param type $args Display arguments including before_title, after_title, before_widget, and after_widget.
     * @param type $instance The settings for the particular instance of the widget
     * @since 0.5
     */
    function widget( $args, $instance ) {
        if( ! is_singular( 'plugins' ) ) return;

        global $post;

        $custom = get_post_custom();
        isset( $custom["_acpl_slug"][0] )? $slug = $custom["_acpl_slug"][0] : $slug = '';

        // Bail if $slug has no value (useful if plugin is not being hosted on WP.org or isn't live yet)
        if( ! $slug ) return;

        // Grab the plugin details from the WP.org servers
        $details = unserialize( ARCONIX_PLUGINS::get_wporg_custom_plugin_data( $slug ) );

        // Bail out here if there's a problem with the WP server, etc...
        if( ! $details ) return;

        // Set our variables
        $plugname   = $details->name;
        $plugtitle  = $plugname . ' Posts';

        if ( empty( $instance['number'] ) || ! $number = absint( $instance['number'] ) )
            $number = 5;
        $show_date = isset( $instance['show_date'] ) ? $instance['show_date'] : false;

        $qargs = apply_filters( 'arconix_plugins_related_query_args', array(
            'posts_per_page' => $number,
            'no_found_rows' => true,
            'tag' => $slug
        ) );

        $q = new WP_Query( $qargs );

        if ( $q->have_posts() ) {

            extract( $args );
            
            // Before widget (defined by themes)
            echo $before_widget;

            echo $before_title . $plugtitle . $after_title;

            echo'<ul>';

            while ( $q->have_posts() ) : $q->the_post(); 
                echo '<li>';
                echo '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
                if( $show_date )
                    echo ' <span class="post-date">' . get_the_date() . '</span>';
                echo '</li>';
            endwhile;

            echo '</ul>';
        }

        // After widget (defined by themes)
        echo $after_widget;

        // Reset the global $the_post as this query will have stomped on it
        wp_reset_postdata();
    }

    /**
     * Update a particular instance.
     *
     * @param array $new_instance New settings for this instance as input by the user via form()
     * @param array $old_instance Old settings for this instance
     * @return array Settings to save or bool false to cancel saving
     * @since 0.5
     */
    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['number'] = (int) $new_instance['number'];
        $instance['show_date'] = (bool) $new_instance['show_date'];

        return $instance;
    }

    /**
     * Widget form
     *
     * @param array $instance Current settings
     * @since 0.5
     */
    function form( $instance ) {
        echo '<p>This widget will only work when displayed on an individual plugin page.</p>';

        $number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
        $show_date = isset( $instance['show_date'] ) ? (bool) $instance['show_date'] : false;
        ?>
        <p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts to show:' ); ?></label>
        <input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>

        <p><input class="checkbox" type="checkbox" <?php checked( $show_date ); ?> id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" />
        <label for="<?php echo $this->get_field_id( 'show_date' ); ?>"><?php _e( 'Display post date?' ); ?></label></p>
        <?php
    }

}