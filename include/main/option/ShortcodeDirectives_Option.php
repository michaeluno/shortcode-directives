<?php
/**
 * Shortcode Directives
 * 
 * [PROGRAM_URI]
 * Copyright (c) 2020 Michael Uno
 * 
 */

/**
 * Handles plugin options.
 * 
 * @since    0.0.1
 */
class ShortcodeDirectives_Option extends ShortcodeDirectives_Option_Base {

    /**
     * Stores instances by option key.
     * 
     * @since    0.0.1
     */
    static public $aInstances = array(
        // key => object
    );

    /**
     * Returns the instance of the class.
     * 
     * This is to ensure only one instance exists.
     * 
     * @since      3
     * @return     ShortcodeDirectives_Option
     */
    static public function getInstance( $sOptionKey='' ) {
        
        $sOptionKey = $sOptionKey 
            ? $sOptionKey
            : ShortcodeDirectives_Registry::$aOptionKeys[ 'setting' ];
        
        if ( isset( self::$aInstances[ $sOptionKey ] ) ) {
            return self::$aInstances[ $sOptionKey ];
        }
        $_sClassName = apply_filters( 
            ShortcodeDirectives_Registry::HOOK_SLUG . '_filter_option_class_name',
            __CLASS__ 
        );
        self::$aInstances[ $sOptionKey ] = new $_sClassName( $sOptionKey );
        return self::$aInstances[ $sOptionKey ];
        
    }         
        
    /**
     * Checks whether the plugin debug mode is on or not.
     * @return      boolean
     */ 
    public function isDebug() {
        return defined( 'WP_DEBUG' ) && WP_DEBUG;
    }
    
}