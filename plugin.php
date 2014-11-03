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
     * @since   0.1
     * @version 1.0.0
     * @var     string  $version    current plugin version
     */
    public static $version = '1.0.0';

    /**
     * Initialize the class and set its properties.
     *
     * @since   1.0.0
     */
    public function __construct() {
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
        require_once( plugin_dir_path( __FILE__ ) . '/includes/class-arconix-plugins-admin.php' );
        require_once( plugin_dir_path( __FILE__ ) . '/includes/class-arconix-plugin.php' );
    }


    private function load_admin() {
        new Arconix_Plugins_Admin( $this->get_version() );
    }


    public function get_version() {
        return self::version;
    }
}

/** Vroom vroom */
add_action( 'plugins_loaded', 'arconix_plugins_run' );
function arconix_plugins_run() {
    new Arconix_Plugins();
}