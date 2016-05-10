<?php
/**
 * Arconix Staff Metabox Class
 *
 * Registers the plugin's settings metabox
 *
 * @since   0.1.0
 */
class Arconix_Staff_Metaboxes {

    /**
     * Initialize the class
     *
     * @since   0.1.0
     * @access  public
     */
    public function __construct() {
        add_action( 'cmb2_init',    array( $this, 'cmb2') );
    }

    /**
     * Define the Metabox and its fields
     *
     * @since   0.1.0
     * @access  public
     * @return  void
     */
    public function cmb2() {
        // Initiate the metabox
        $cmb = new_cmb2_box( array(
            'id'            => 'arconix_staff_settings',
            'title'         => __( 'Staff Member Setting', 'arconix-staff' ),
            'object_types'  => array( 'staff' ),
            'context'       => 'normal',
            'priority'      => 'high',
            'show_names'    => true
        ) );

        // Add the Link Type field
        $cmb->add_field( array(
            'id'        => '_staff_email',
            'name'      => __( 'Staff Member E-mail', 'arconix-staff' ),
            'desc'      => __( 'Enter the Staff Member\'s e-mail address', 'arconix-staff' ),
            'type'      => 'text_email'
        ) );

    }

}