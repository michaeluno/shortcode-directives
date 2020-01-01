<?php
/**
 * Shortcode Directives
 *
 * [PROGRAM_URI]
 * Copyright (c) 2020 Michael Uno; Licensed under <LICENSE_TYPE>
 */

/**
 * Adds the 'Export' form section to the 'Manage Options' tab.
 *
 * @since    0.0.3
 */
class ShortcodeDirectives_AdminPage__FormSection_Export extends ShortcodeDirectives_AdminPage__FormSection_Base {

    /**
     *
     * @since   0.0.3
     */
    protected function _getArguments( $oFactory ) {
        return array(
            'section_id'    => 'export',
            'tab_slug'      => $this->_sTabSlug,
            'title'         => __( 'Export', 'shortcode-directives' ),
        );
    }

    /**
     * Get adding form fields.
     * @since    0.0.3
     * @return   array
     */
    protected function _getFields( $oFactory ) {
        return array(
            array(
                'field_id'          => 'export_options',
                'title'             => __( 'Export Options', 'shortcode-directives' ),
                'type'              => 'export',
                'value'             => __( 'Download', 'shortcode-directives' ),
                'save'              => false,
            )
        );

    }

}