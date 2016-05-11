<?php
/*
if ( ! function_exists( 'plugins_api' ) )
    require_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );*/
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
     * stores it as a transient and then returns it as an unserialized array.
     * The transient data expires daily (in seconds) by default or can be
     * overridden by adding a filter.
     *
     * @since   0.5
     * @version 1.0.0
     * @param   string   $slug  Plugin slug
     * @return  array           Unserialized array of plugin details
     */
    public function get_wporg_custom_plugin_data( $slug ) {
        $trans_slug = 'acpl-' . $slug;

        // Check for stored transient. Create one if none exists
        if( WP_DEBUG || false === get_transient( $trans_slug ) ) {

            $request = array(
                'action' => 'plugin_information',
                'request' => serialize(
                    (object) array(
                        'slug' => $slug,
                        'fields' => array( 'description' => true )
                    )
                )
            );


            $wp_repo = wp_remote_post( 'http://api.wordpress.org/plugins/info/1.0/', array( 'body' => $request ) );
            $response = unserialize( $response['body'] );

            $expiration = apply_filters( 'arconix_plugins_transient_expiration', 60*60*24 );

            // Save transient to the database
            set_transient( $trans_slug, $response, $expiration );
        }

        // Check for cached result
        $response = get_transient( $trans_slug );

        if( ! is_wp_error( $response ) )
            return false;

        return unserialize( $plugin );

        /*$api_call = plugins_api( 'plugin_information', array( 'slug' => $slug ) );

        if( is_wp_error( $api_call ) )
            return print_r( $api_call->get_error_message(), true );
        else
            return $api_call;*/
    }

    /**
     * Pass in a time and receive the text for amount of time passed, e.g. '2 months ago'
     *
     * Time periods and tense are filterable
     *
     * @link http://www.php.net/manual/en/function.time.php#91864
     *
     * @since   1.0.0
     * @param   string  $tm     Time to check against
     * @param   int     $rcs    Number of levels deep (e.g. 2 minutes 20 seconds ago)
     * @return  string          Difference between param time and now
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
     * Return the plugin version.
     *
     * @since   1.0.0
     * @param   array   $data           Unserialized array of plugin information
     * @return  mixed   false|string    Version string. Return early if $data isn't an array
     */
    public function get_version( $data ) {
        if ( ! is_array( $data ) )
            return false;

        return $data->version;
    }

    /**
     * Return when the plugin was last updated
     *
     * @since   1.0.0
     * @param   array       $data           Unserialized array of plugin information
     * @param   boolean     $raw            Return raw or formatted data
     * @return  mixed       false|string    Plugin last updated date. Return early if $data not an array
     */
    public function get_last_updated( $data, $raw = false ) {
        if ( ! is_array( $data ) )
            return false;

        if ( false === $raw )
            return $data->last_updated;
        else
            return date( get_option( 'date_format' ) , strtotime( $data->last_updated ) );
    }

    /**
     * Return number of plugin downloads
     *
     * @since   1.0.0
     * @param   array       $data           Unserialized array of plugin information
     * @param   boolean     $raw            Return raw or formatted data
     * @return  mixed       false|string    Plugin last updated date. Return early if $data not an array
     */
    public function get_downloads( $data, $raw = false ) {
        if ( ! is_array( $data ) )
            return false;

        if ( false === $raw )
            return $data->downloaded;
        else
            return number_format( $array->downloaded );
    }

    /**
     * Return the plugin slug.
     *
     * @since   1.0.0
     * @param   int     $id
     * @return  string          Slug of plugin meta information
     */
    public function get_slug( $id = 0 ) {
        if ( $id === 0 )
            $id = get_the_id();

        $slug = get_post_meta( $id, '_acpl_slug', true );

        return $slug;
    }

}