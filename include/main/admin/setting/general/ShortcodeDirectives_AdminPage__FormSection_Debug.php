<?php
/**
 * Shortcode Directives
 *
 * http://en.michaeluno.jp/shortcode-directivies
 * Copyright (c) 2020 Michael Uno; Licensed under <LICENSE_TYPE>
 */

/**
 * Adds the 'Debug' form section to the 'General' tab.
 *
 * @since    0.0.3
 */
class ShortcodeDirectives_AdminPage__FormSection_Debug extends ShortcodeDirectives_AdminPage__FormSection_Base {

    /**
     *
     * @since   0.0.3
     */
    protected function _getArguments( $oFactory ) {
        return array(
            'section_id'    => 'debug',
            'tab_slug'      => $this->_sTabSlug,
            'title'         => __( 'Debug', 'shortcode-directives' ),
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
                'field_id'          => 'debug_mode',
                'title'             => __( 'Debug Mode', 'shortcode-directives' ),
                'type'              => 'checkbox',
                'label'             => __( 'Enable', 'shortcode-directives' ),
            )
        );

    }

}