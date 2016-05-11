<?php
/**
 * Plugin Name: Arconix Plugins
 * Plugin URI: http://arconixpc.com/
 * Description: Plugin for displaying WP.org-hosted plugins on your website
 *
 * Version: 1.0.1
 *
 * Author: John Gardner
 * Author URI: http://arconixpc.com
 *
 * License: GNU General Public License v2.0
 * License URI: http://www.opensource.org/licenses/gpl-license.php
 */


require_once( plugin_dir_path(__FILE__) . 'includes/class-arconix-plugins-admin.php' );
require_once( plugin_dir_path(__FILE__) . 'includes/class-arconix-plugins-content-type.php' );
require_once( plugin_dir_path(__FILE__) . 'includes/class-arconix-plugins-metaboxes.php' );
require_once( plugin_dir_path(__FILE__) . 'includes/class-arconix-plugins-public.php' );
require_once( plugin_dir_path(__FILE__) . 'includes/cmb2/init.php' );
if( ! class_exists('Gamajo_Dashboard_Glancer') )
    require_once( plugin_dir_path(__FILE__) . 'includes/class-gamajo-dashboard-glancer.php' );



class Arconix_Plugins {

    /**
     * Stores the current version of the plugin.
     *
     * @since   0.5
     * @access  private
     * @var     string  $version    Current plugin version
     */
    const VERSION = '1.0.1';

    /**
     * Initialize the class and set its properties.
     *
     * @since   1.0.0
     */
    public function __construct() {
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'decativate' ) );
        $this->load_classes();
    }

    /**
     * Load the Administrative Backend
     *
     * @since   1.0.0
     */
    public function load_classes() {
        new Arconix_Plugins_Admin();
        new Arconix_Plugins_Content_Type();
        new Arconix_Plugins_Metaboxes();
    }

    /**
     * Activate the plugin
     *
     * @since   1.0.1
     * return   void
     */
    public function activate() {
        flush_rewrite_rules();
    }

    /**
     * Deactivate the plugin
     *
     * @since   1.0.1
     * @return  void
     */
    public function deactivate() {
        flush_rewrite_rules();
    }

}

//
// Vroom vroom
//
add_action( 'plugins_loaded', 'arconix_plugins_run' );
function arconix_plugins_run() {
    new Arconix_Plugins();
}