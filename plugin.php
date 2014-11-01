<?php
/**
 * Plugin Name: Arconix Plugins
 * Plugin URI: http://arconixpc.com/
 * Description: Plugin for displaying WP.org-hosted plugins on your website
 *
 * Version: 1.0
 *
 * Author: John Gardner
 * Author URI: http://arconixpc.com
 *
 * License: GNU General Public License v2.0
 * License URI: http://www.opensource.org/licenses/gpl-license.php
 */


class Arconix_Plugins_Loader {

    protected $version;

    public function __construct() {
        $this->version = '1.0.0';

        $this->load_dependencies();
        $this->load_admin();
        $this->load_public();
        //$this->load_widgets();
    }

    private function load_dependencies() {
        require_once( plugin_dir_path( __FILE__ ) . 'includes/class-arconix-plugins-admin.php' );
        require_once( plugin_dir_path( __FILE__ ) . 'includes/class-arconix-plugins-public.php' );
        require_once( plugin_dir_path( __FILE__ ) . '/includes/class-arconix-plugins-widgets.php' );
    }

    private function load_admin() {
        new Arconix_Plugins_Admin( $this->get_version() );
    }

    private function load_public() {
        new Arconix_Plugins_Public( $this->get_version() );
    }

    public function get_version() {
        return $this->version;
    }
}






/**
 * Load the plugin custom metabox
 *
 * @since 0.1
 */
function arconix_plugins_init() {
    if( ! class_exists( 'cmb_Meta_Box' ) )
        require_once( plugin_dir_path( __FILE__ ) . '/includes/metabox/init.php' );

    if ( ! class_exists( 'Gamajo_Dashboard_Glancer' ) )
        require_once( plugin_dir_path( __FILE__ ) . '/includes/class-gamajo-dashboard-glancer.php');
}



new Arconix_Plugins;