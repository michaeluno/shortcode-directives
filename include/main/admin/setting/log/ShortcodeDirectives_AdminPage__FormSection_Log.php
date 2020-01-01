<?php
/**
 * Shortcode Directives
 *
 * [PROGRAM_URI]
 * Copyright (c) 2020 Michael Uno; Licensed under <LICENSE_TYPE>
 */

/**
 * Adds the 'Log' form section to the 'General' tab.
 *
 * @since    0.0.3
 */
class ShortcodeDirectives_AdminPage__FormSection_Log extends ShortcodeDirectives_AdminPage__FormSection_Base {

    /**
     *
     * @since   0.0.3
     */
    protected function _getArguments( $oFactory ) {
        return array(
            'section_id'    => 'log',
            'tab_slug'      => $this->_sTabSlug,
            'title'         => __( 'Log', 'shortcode-directives' ),
        );
    }

    /**
     * Get adding form fields.
     * @since    0.0.3
     * @return   array
     */
    protected function _getFields( $oFactory ) {
        $_aLog = get_option( ShortcodeDirectives_Registry::$aOptionKeys[ 'log' ], array() );
        return array(
            array(
                'field_id'          => 'delete_on_uninstall',
                'type'              => 'system',
                'show_title_column' => false,
                'value'             => implode( PHP_EOL, $_aLog ),
            )
        );
    }

}