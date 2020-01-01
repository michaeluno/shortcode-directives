<?php
/**
 * Shortcode Directives
 * 
 * [PROGRAM_URI]
 * Copyright (c) 2020 Michael Uno
 * 
 */

/**
 * Plugin event handler.
 * 
 * @package      Shortcode Directives
 * @since    0.0.1
 */
class ShortcodeDirectives_Event {

    /**
     * Triggers event actions.
     */
    public function __construct() {

        // This must be called after the above action hooks are added.
        // $_oOption               = ShortcodeDirectives_Option::getInstance();
// ShortcodeDirectives_Debug::log( $_oOption->get() );

    }
    
}