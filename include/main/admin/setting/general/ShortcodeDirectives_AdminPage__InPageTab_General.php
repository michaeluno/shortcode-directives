<?php
/**
 * Shortcode Directives
 *
 *
 * [PROGRAM_URI]
 * Copyright (c) 2020 Michael Uno; Licensed under <LICENSE_TYPE>
 *
 */

/**
 * Adds the 'General' tab to the 'Settings' page of the loader plugin.
 *
 * @since    0.0.3
 * @extends  ShortcodeDirectives_AdminPage__InPageTab_Base
 */
class ShortcodeDirectives_AdminPage__InPageTab_General extends ShortcodeDirectives_AdminPage__InPageTab_Base {

    /**
     * @return      array
     */
    protected function _getArguments( $oFactory ) {
        return array(
            'tab_slug'  => 'general',
            'title'     => __( 'General', 'shortcode-directives' ),
        );
    }
    
    /**
     * Triggered when the tab is loaded.
     */
    protected function _load( $oFactory ) {

        // Form sections
//        new ShortcodeDirectives_AdminPage__FormSection_Sample( $oFactory, $this->_sPageSlug, $this->_sTabSlug );
//        new ShortcodeDirectives_AdminPage__FormSection_PostTypes( $oFactory, $this->_sPageSlug, $this->_sTabSlug );

    }

    /**
     * @param $oFactory
     */
    protected function _do( $oFactory ) {
        echo "<div class='right-submit-button'>"
                . get_submit_button()
            . "</div>";
    }

}
