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
                'field_id'          => 'keep',
                'type'              => 'inline_mixed',
                'show_title_column' => false,
                'content'           => array(
                    array(
                        'field_id'          => 'enable',
                        'type'              => 'checkbox',
                        'label'             => __( 'Keep', 'shortcode-directives' ),
                    ),
                    array(
                        'field_id'          => 'length',
                        'type'              => 'number',
                        'attributes'        => array(
                            'min'   => 0,
                            'step'  => 1,
                            'style' => 'max-width: 6em;',
                        ),
                    ),
                    array(
                        'field_id'          => '_items',
                        'save'              => false,
                        'content'           => "<label>"
                                . sprintf( __( '%1$s items.', 'shortcode-directives' ), '' )
                            . "</label>",
                    ),
                ),
            ),
            array(
                'field_id'          => '_log',
                'type'              => 'system',
                'show_title_column' => false,
                'value'             => implode( PHP_EOL, $_aLog ),
                'save'              => false,
            ),
            array(
                'field_id'          => '_clear_log',
                'type'              => 'submit',
//                'show_title_column' => false,
                'title'             => __( 'Clear Log', 'shortcode-directives' ),
                'value'             => __( 'Clear', 'shortcode-directives' ),
                'save'              => false,
            ),
        );
    }

    protected function _validate( $aInputs, $aOldInputs, $oAdminPage, $aSubmitInfo ) {

        // If the pressed button is not the one with the check box, do not set a field error.
        if ( '_clear_log' === $aSubmitInfo[ 'field_id' ] ) {
            delete_option( ShortcodeDirectives_Registry::$aOptionKeys[ 'log' ] );
            $oAdminPage->setSettingNotice( __( 'Cleared log.', 'shortcode-directives' ), 'updated' );
            return $aOldInputs;
        }

        return $aInputs;

    }


}