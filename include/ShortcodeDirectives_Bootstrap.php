<?php
/**
 * Shortcode Directives
 * 
 * [PROGRAM_URI]
 * Copyright (c) 2020 Michael Uno
 * 
 */

/**
 * Loads the plugin.
 * 
 * @since       0.0.1
 */
final class ShortcodeDirectives_Bootstrap extends ShortcodeDirectives_AdminPageFramework_PluginBootstrap {
    
    /**
     * User constructor.
     */
    protected function construct()  {}        

        
    /**
     * Register classes to be auto-loaded.
     * 
     * @since    0.0.1
     */
    public function getClasses() {
        
        // Include the include lists. The including file reassigns the list(array) to the $_aClassFiles variable.
        $_aClassFiles   = array();
        $_bLoaded       = include( dirname( $this->sFilePath ) . '/include/class-list.php' );
        if ( ! $_bLoaded ) {
            return $_aClassFiles;
        }
        return $_aClassFiles;
                
    }

    /**
     * Sets up constants.
     */
    public function setConstants() {
    }    
    
    /**
     * Sets up global variables.
     */
    public function setGlobals() {
    }    
    
    /**
     * The plugin activation callback method.
     */    
    public function replyToPluginActivation() {

        $this->_checkRequirements();
        
    }
        
        /**
         * 
         * @since            3
         */
        private function _checkRequirements() {

            $_oRequirementCheck = new ShortcodeDirectives_AdminPageFramework_Requirement(
                ShortcodeDirectives_Registry::$aRequirements,
                ShortcodeDirectives_Registry::NAME
            );
            
            if ( $_oRequirementCheck->check() ) {            
                $_oRequirementCheck->deactivatePlugin( 
                    $this->sFilePath, 
                    __( 'Deactivating the plugin', 'shortcode-directives' ),  // additional message
                    true    // is in the activation hook. This will exit the script.
                );
            }        
             
        }    

        
    /**
     * The plugin activation callback method.
     */    
    public function replyToPluginDeactivation() {
        
        ShortcodeDirectives_WPUtility::cleanTransients( 
            array(
                ShortcodeDirectives_Registry::TRANSIENT_PREFIX,
                'apf_',
            )
        );
        
    }        
    
        
    /**
     * Load localization files.
     * 
     * @callback    action      init
     */
    public function setLocalization() {
        
        // This plugin does not have messages to be displayed in the front-end.
        if ( ! $this->bIsAdmin ) { 
            return; 
        }
        
        load_plugin_textdomain( 
            ShortcodeDirectives_Registry::TEXT_DOMAIN, 
            false, 
            dirname( plugin_basename( $this->sFilePath ) ) . '/' . ShortcodeDirectives_Registry::TEXT_DOMAIN_PATH
        );
        
    }        
    
    /**
     * Loads the plugin specific components. 
     * 
     * @remark        All the necessary classes should have been already loaded.
     */
    public function setUp() {
        
        // This constant is set when `uninstall.php` is loaded.
        if ( defined( 'DOING_PLUGIN_UNINSTALL' ) && DOING_PLUGIN_UNINSTALL ) {
            return;
        }
            
        // Include PHP files.
        // $this->_include();
            
        // Option Object - must be done before the template object.
        // The initial instantiation will handle formatting options from earlier versions of the plugin.
        ShortcodeDirectives_Option::getInstance();
     
        // Admin pages
        if ( $this->bIsAdmin ) {            
        
            new ShortcodeDirectives_AdminPage( 
                ShortcodeDirectives_Registry::$aOptionKeys[ 'setting' ], 
                $this->sFilePath 
            );

        }


        new ShortcodeDirectives_Loader(
            // array_keys( $_oOption->getAsArray( $_oOption->get( array( 'post_types', 'supported' ) ) ) )
        );

        // Events
        // new ShortcodeDirectives_Event;    
        
    }
        /**
         * Includes additional files.
         */
        private function _include() {}
    
}