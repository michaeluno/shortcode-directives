<?php
/**
 * Shortcode Directives
 * 
 * [PROGRAM_URI]
 * Copyright (c) 2020 Michael Uno
 * 
 */


/**
 * Deals with the plugin admin pages.
 * 
 * @since    0.0.1
 */
class ShortcodeDirectives_AdminPage extends ShortcodeDirectives_AdminPageFramework {

    /**
     * User constructor.
     */
    public function start() {
        
        if ( ! $this->oProp->bIsAdmin ) {
            return;
        }     
        add_filter( 
            "options_" . $this->oProp->sClassName,
            array( $this, 'replyToSetOptions' )
        );
        
    }
        /**
         * Sets the default option values for the setting form.
         * @return      array       The options array.
         */
        public function replyToSetOptions( $aOptions ) {
            $_oOption    = ShortcodeDirectives_Option::getInstance();
            return $aOptions + $_oOption->get();            
        }
        
    /**
     * Sets up admin pages.
     */
    public function setUp() {
        
        $this->setRootMenuPage( 'Tools' );
                    
        // Add pages      
        new ShortcodeDirectives_AdminPage__Page_Setting( $this );

        $this->_doPageSettings();
        
    }

        /**
         * Page styling
         * @since    0.0.1
         * @return   void
         */
        private function _doPageSettings() {
                        
            $this->setPageTitleVisibility( false ); // disable the page title of a specific page.
            $this->setInPageTabTag( 'h2' );                
            // $this->setPluginSettingsLinkLabel( '' ); // pass an empty string to disable it.
            
            $this->enqueueStyle( ShortcodeDirectives_Registry::getPluginURL( 'include/main/asset/css/admin.css' ) );
        
        }

}