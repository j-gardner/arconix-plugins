<?php
/**
 * Arconix Staff Metabox Class
 *
 * Registers the plugin's settings metabox
 *
 * @since   1.0.1
 */
class Arconix_Plugins_Metaboxes {

    /**
     * Initialize the class
     *
     * @since   1.0.1
     * @access  public
     */
    public function __construct() {
        add_action( 'cmb2_init',    array( $this, 'cmb2') );
    }

    /**
     * Define the Metabox and its fields
     *
     * @since   1.0.1
     * @access  public
     * @return  void
     */
    public function cmb2() {
        // Set the prefix
        $prefix = '_acpl_';

        // Initiate the metabox
        $cmb = new_cmb2_box( array(
            'id'            => 'arconix_plugins_details',
            'title'         => __( 'Plugin Details', 'arconix-plugins' ),
            'object_types'  => array( 'plugins' ),
            'context'       => 'normal',
            'priority'      => 'high',
            'show_names'    => true
        ) );

        // Add the Link Type field
        $cmb->add_field( array(
            'id'    => $prefix . 'slug',
            'name'  => __( 'Slug', 'arconix-plugins' ),
            'desc'  => __( 'Enter the plugin slug', 'arconix-plugins' ),
            'type'  => 'text_medium',
        ) );

        $cmb->add_field( array (
            'id'    => $prefix . 'download',
            'name'  => __( 'Download', 'arconix-plugins' ),
            'desc'  => __( 'Enter the download URL (optional)' ),
            'type'  => 'text_medium',
        ) );

        $cmb->add_field( array (
            'id'    => $prefix . 'demo',
            'name'  => __( 'Demo', 'arconix-plugins' ),
            'desc'  => __( 'Enter the URL to the demo (optional)' ),
            'type'  => 'text_medium',
        ) );

        $cmb->add_field( array (
            'id'    => $prefix . 'docs',
            'name'  => __( 'Documentation', 'arconix-plugins' ),
            'desc'  => __( 'Enter the URL to the documentation (optional)' ),
            'type'  => 'text_medium',
        ) );

        $cmb->add_field( array (
            'id'    => $prefix . 'help',
            'name'  => __( 'Support', 'arconix-plugins' ),
            'desc'  => __( 'Enter the support URL (optional)' ),
            'type'  => 'text_medium',
        ) );

        $cmb->add_field( array (
            'id'    => $prefix . 'source',
            'name'  => __( 'Source Code', 'arconix-plugins' ),
            'desc'  => __( 'Enter the URL to the source (optional)' ),
            'type'  => 'text_medium',
        ) );

        $cmb->add_field( array (
            'id'    => $prefix . 'dev',
            'name'  => __( 'Development', 'arconix-plugins' ),
            'desc'  => __( 'Enter the URL to the development roadmap (optional)' ),
            'type'  => 'text_medium',
        ) );

        $cmb->add_field( array (
            'id'    => $prefix . 'donation',
            'name'  => __( 'Donation', 'arconix-plugins' ),
            'desc'  => __( 'Enter a URL to the donation page (optional)' ),
            'type'  => 'text_medium',
        ) );

    }

}