<?php
/**
 * Shortcode Directives
 *
 * [PROGRAM_URI]
 * Copyright (c) 2020 Michael Uno; Licensed under <LICENSE_TYPE>
 */

/**
 * Adds the 'Reset' form section to the 'Manage Options' tab.
 *
 * @since    0.0.3
 */
class ShortcodeDirectives_AdminPage__FormSection_DoReset extends ShortcodeDirectives_AdminPage__FormSection_Base {

    /**
     *
     * @since   0.0.3
     */
    protected function _getArguments( $oFactory ) {
        return array(
            'section_id'    => 'do_reset',
            'tab_slug'      => $this->_sTabSlug,
            'title'         => __( 'Reset', 'shortcode-directives' ),
            'save'          => false,
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
                'field_id'          => 'reset_confirmation_check',
                'title'             => __( 'Reset Options', 'shortcode-directives' ),
                'type'              => 'checkbox',
                'label'             => __( 'I understand the options will be erased by pressing the reset button.', 'shortcode-directives' ),
                'save'              => false,
                'value'             => false,
            ),
            array(
                'field_id'          => 'reset',
                'type'              => 'submit',
                'reset'             => true,
                'skip_confirmation' => true,
                // 'show_title_column' => false,
                'value'             => __( 'Reset', 'shortcode-directives' ),
            )
        );

    }

    /**
     * Validates the submitted form data.
     *
     * @since    0.0.2
     */
    public function _validate( $aInputs, $aOldInput, $oAdminPage, $aSubmitInfo ) {

        $_aErrors   = array();

        try {

            if ( ! $aInputs[ 'reset_confirmation_check' ] ) {
                $_sMessage = __( 'Please check the check box to confirm you want to reset the settings.', 'custom-translation-files' );
                $_aErrors[ $this->_sSectionID ][ 'reset_confirmation_check' ] = $_sMessage;
                throw new Exception( $_sMessage );
            }

            // If the pressed button is not the one with the check box, do not set a field error.
            if ( 'reset' !== $aSubmitInfo[ 'field_id' ] ) {
                return $aInputs;
            }

        } catch ( Exception $oException ) {

            // An invalid value is found. Set a field error array and an admin notice and return the old values.
            $oAdminPage->setFieldErrors( $_aErrors );
            $oAdminPage->setSettingNotice( __( 'There was something wrong with your input.', 'custom-translation-files' ) );
            return $aOldInput;

        }

        return $aInputs;

    }

}