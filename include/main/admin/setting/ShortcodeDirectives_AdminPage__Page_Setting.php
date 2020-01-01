<?php
/**
 * Shortcode Directives
 * 
 * [PROGRAM_URI]
 * Copyright (c) 2020 Michael Uno; Licensed under <LICENSE_TYPE>
 * 
 */

/**
 * Adds the `Settings` page.
 * 
 * @since    0.0.1
 */
class ShortcodeDirectives_AdminPage__Page_Setting extends ShortcodeDirectives_AdminPage__Page_Base {

    /**
     * @param  $oFactory
     * @return array
     */
    protected function _getArguments( $oFactory ) {
        return array(
            'page_slug'     => ShortcodeDirectives_Registry::$aAdminPages[ 'setting' ],
            'title'         => __( 'Shortcode Directives', 'shortcode-directives' ),
            // 'screen_icon'   => ShortcodeDirectives_Registry::getPluginURL( "asset/image/screen_icon_32x32.png" ),
        );
    }

    /**
     * A user constructor.
     * 
     * @since    0.0.3
     * @return   void
     */
    protected function _construct( $oFactory ) {
        
        // Tabs
        new ShortcodeDirectives_AdminPage__InPageTab_PostType( $oFactory, $this->_sPageSlug );
        new ShortcodeDirectives_AdminPage__InPageTab_General( $oFactory, $this->_sPageSlug );
        new ShortcodeDirectives_AdminPage__InPageTab_Log( $oFactory, $this->_sPageSlug );
        new ShortcodeDirectives_AdminPage__InPageTab_Data( $oFactory, $this->_sPageSlug );

    }   

}
