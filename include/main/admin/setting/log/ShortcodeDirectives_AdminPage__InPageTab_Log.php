<?php
/**
 * Shortcode Directives
 *
 *
 * http://en.michaeluno.jp/shortcode-directivies
 * Copyright (c) 2020 Michael Uno; Licensed under <LICENSE_TYPE>
 *
 */

/**
 * Adds the 'Log' tab to the 'Settings' page of the loader plugin.
 *
 * @since    0.0.3
 * @extends  ShortcodeDirectives_AdminPage__InPageTab_Base
 */
class ShortcodeDirectives_AdminPage__InPageTab_Log extends ShortcodeDirectives_AdminPage__InPageTab_Base {

    /**
     * @return      array
     */
    protected function _getArguments( $oFactory ) {
        return array(
            'tab_slug'  => 'log',
            'title'     => __( 'Log', 'shortcode-directives' ),
        );
    }
    
    /**
     * Triggered when the tab is loaded.
     */
    protected function _load( $oFactory ) {

        // Form sections
        new ShortcodeDirectives_AdminPage__FormSection_Log( $oFactory, $this->_sPageSlug, $this->_sTabSlug );

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
