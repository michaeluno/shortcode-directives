<?php
/**
 * Shortcode Directives
 * 
 * [PROGRAM_URI]
 * Copyright (c) 2020 Michael Uno
 * 
 */

/**
 * Provides base methods for plugin event actions.
 
 * @package      Shortcode Directives
 * @since    0.0.1
 */
abstract class ShortcodeDirectives_Event_Action_Base extends ShortcodeDirectives_WPUtility {
    
    /**
     * Sets up hooks.
     * @since    0.0.1
     * @param       string      $sActionHookName
     * @param       integer     $iParameters        The number of parameters.
     */
    public function __construct( $sActionHookName, $iParameters=1 ) {

        add_action( 
            $sActionHookName, 
            array( 
                $this, 
                'doAction' 
            ),
            10, // priority
            $iParameters
        );    

    }
    
    /**
     * 
     * @callback        action       
     */
    public function doAction( /* $aArguments */ ) {
        
        $_aParams = func_get_args() + array( null );
        ShortcodeDirectives_Debug::log( $_aParams );
        
    }
    
}