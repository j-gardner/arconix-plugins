<?php
/**
 * Provides Helper functions for displaying plugin information
 *
 * @since 1.0.0
 */
class Arconix_Plugin {

    /**
     * Retrieve the plugin data for wp.org hosted plugins.
     *
     * Accepts the plugin slug in question and checks for a stored transient.
     * If none exists, then it retrieves the plugin data from wp.org,
     * stores it as a transient and then returns it as an object.
     * The transient data expires daily (in seconds) by default or can be
     * overridden by filter.
     *
     * @since   0.5
     * @version 1.0.0
     * @param   string      $slug          Plugin slug
     * @return  stdObj                     Details of the Plugin being asked about
     */
    public function get_wporg_custom_plugin_data( $slug ) {
        $transient_slug = 'acpl-' . $slug;

        // Check for stored transient. Create one if none exists
        if ( false === get_transient( $transient_slug ) ) {

            $request = array(
                'action'    => 'plugin_information',
                'request'   => serialize(
                    (object) array(
                        'slug'      => $slug,
                        'fields'    => array(
                            'description'       =>  true,
                            'active_installs'   =>  true
                        )
                    )
                )
            );

            // Pass the request to the API and then unserialize the response
            $api_request = wp_remote_post( 'http://api.wordpress.org/plugins/info/1.0/', array( 'body' => $request ) );
            $response = unserialize( $api_request['body'] );

            $expiration = apply_filters( 'arconix_plugins_transient_expiration', 60*60*24 );

            // Save transient to the database
            set_transient( $transient_slug, $response, $expiration );
        }

        // Check for cached result
        $response = get_transient( $transient_slug );

        if( ! $response )
            return false;

        return $response;
    }

    /**
     * Return the plugin version.
     *
     * @since   1.0.0
     * @param   stdObj      $data           Plugin information
     * @return  string                      Version string. Return early if $data isn't an object
     */
    public function get_version( $data ) {
        if ( ! is_object( $data ) )
            return false;

        return $data->version;
    }

    /**
     * Return when the plugin was last updated formatted to the WordPress-set date format
     *
     * @since   1.0.0
     * @param   stdObj      $data           Plugin information
     * @param   bool        $formatted      Return raw or formatted data
     * @return  mixed                       Plugin last updated date.
     */
    public function get_last_updated( $data, $formatted = true ) {
        if ( ! is_object( $data ) )
            return false;

        if ( false === $formatted )
            return $data->last_updated;

        return date( get_option( 'date_format' ) , strtotime( $data->last_updated ) );
    }

    /**
     * Return a formatted number of plugin downloads
     *
     * @since   1.0.0
     * @param   stdObj      $data           Plugin information
     * @param   bool        $formatted      Return raw or formatted data
     * @return  mixed                       Formatted or unformatted number of downloads
     */
    public function get_downloads( $data, $formatted = true ) {
        if ( ! is_object( $data ) )
            return false;

        if ( false === $formatted )
            return $data->downloaded;

        return number_format( $data->downloaded );
    }

    /**
     * Return the plugin rating on a 5-star scale
     *
     * @since   1.0.0
     * @param   stdObj      $data           Plugin information
     * @param   bool        $five_scale     Format number to a X/5 rating like WP.org
     * @return  float
     */
    public function get_rating( $data, $five_scale = true ) {
        if ( ! is_object( $data ) )
            return false;

        if ( false === $five_scale )
            return $data->rating;

        return round( $data->rating / 20, 2 ) . '/5';
    }

    /**
     * Return the number of active installs
     *
     * @since   1.0.0
     * @param   stdObj      $data           Plugin information
     * @param   bool        $formatted      Return raw or formatted data
     * @return  string                      Number of active installs formatted
     */
    public function get_active_installs( $data, $formatted = true ) {
        if ( ! is_object( $data ) )
            return false;

        if ( false === $formatted )
            return $data->active_installs;

        return number_format( $data->active_installs );
    }

    /**
     * Pass in a time and receive the text for amount of time passed, e.g. '2 months ago'
     *
     * Time periods and tense are filterable
     *
     * @link http://www.php.net/manual/en/function.time.php#91864
     *
     * @since   1.0.0
     * @param   string      $tm             Time to check against
     * @param   int         $rcs            Number of levels deep (e.g. 2 minutes 20 seconds ago)
     * @return  string                      Difference between param time and now
     */
    public function ago( $tm, $rcs = 0 ) {
        $defaults = apply_filters( 'arconix_plugins_ago_defaults', array(
          'periods' => array( 'second', 'minute', 'hour', 'day', 'week', 'month', 'year', 'decade' ),
          'tense' => 'ago'
        ) );

        $cur_tm = time();
        $dif = $cur_tm - $tm;
        $pds = $defaults['periods'];
        $lngh = array( 1, 60, 3600, 86400, 604800, 2630880, 31570560, 315705600 );

        for( $v = sizeof( $lngh ) - 1; ($v >= 0) && (($no = $dif / $lngh[$v]) <= 1); $v-- )
            ; if( $v < 0 )
            $v = 0; $_tm = $cur_tm - ($dif % $lngh[$v]);

        $no = floor( $no );
        if( $no <> 1 )
            $pds[$v] .= 's'; $x = sprintf( "%d %s ", $no, $pds[$v] );
        if( ( $rcs == 1 ) && ( $v >= 1 ) && ( ( $cur_tm - $_tm ) > 0 ) )
            $x .= ago( $_tm );

        return $x . $defaults['tense'];
    }

    /**
     * Return the plugin slug.
     *
     * @since   1.0.0
     * @param   int         $id             Post ID. If not supplied it will be defaulted
     * @return  string                      Slug of plugin meta information
     */
    public function get_slug( $id = 0 ) {
        if ( $id === 0 )
            $id = get_the_id();

        $slug = get_post_meta( $id, '_acpl_slug', true );

        return $slug;
    }

}