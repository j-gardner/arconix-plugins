<?php
/**
 * Plugin Name: Arconix Plugins
 * Plugin URI: http://arconixpc.com/
 * Description: Plugin for displaying WP.org-hosted plugins on your website
 *
 * Version: 1.0.0
 *
 * Author: John Gardner
 * Author URI: http://arconixpc.com
 *
 * License: GNU General Public License v2.0
 * License URI: http://www.opensource.org/licenses/gpl-license.php
 */


class Arconix_Plugins {

    /**
     * Stores the current version of the plugin.
     *
     * @since   0.5
     * @var     string  $version    Current plugin version
     */
    private $version = '1.0.0';

    /**
     * The directory path to this plugin's 'includes' folder.
     *
     * @since   1.0.0
     * @access  private
     * @var     string      $inc    The directory path to this plugin's 'includes' folder
     */
    private $inc;

    /**
     * Initialize the class and set its properties.
     *
     * @since   1.0.0
     */
    public function __construct() {
        $this->inc = trailingslashit( plugin_dir_path( __FILE__ ) . '/includes' );
        $this->load_dependencies();
        $this->load_admin();
    }

    /**
     * Load the required dependencies for the plugin.
     *
     * - Admin loads the backend functionality
     * - Public provides front-end functionality
     * - Widgets registers the site widgets
     */
    private function load_dependencies() {
        require_once( $this->inc . 'class-arconix-plugins-admin.php' );
        require_once( $this->inc . 'class-arconix-plugins-public.php' );
        require_once( $this->inc . 'class-arconix-plugins-widgets.php' );

        if ( ! class_exists( 'cmb_Meta_Box' ) )
            require_once( $this->inc . 'metabox/init.php');

        if ( ! class_exists( 'Gamajo_Dashboard_Glancer' ) )
            require_once( $this->inc . 'class-gamajo-dashboard-glancer.php' );
    }

    /**
     * Load the Administrative Backend
     *
     * @since   1.0.0
     */
    private function load_admin() {
        new Arconix_Plugins_Admin( $this->get_version() );
    }

    /**
     * Get the current version of the plugi9n
     *
     * @since   1.0.0
     * @return  string  current plugin version
     */
    public function get_version() {
        return $this->version;
    }
}

/** Vroom vroom */
add_action( 'plugins_loaded', 'arconix_plugins_run' );
function arconix_plugins_run() {
    new Arconix_Plugins();
}