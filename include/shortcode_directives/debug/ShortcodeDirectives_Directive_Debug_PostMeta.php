<?php


class ShortcodeDirectives_Directive_Debug_PostMeta {

    public $aPostTypeSlugs;

    /**
     * Performs necessary set-ups.
     */
    public function __construct( array $aPostTypeSlugs ) {

        $_oOption = ShortcodeDirectives_Option::getInstance();
        if ( ! $_oOption->get( array( 'debug', 'debug_mode' ) ) ) {
            return;
        }

        add_filter( 'the_content', array( $this, 'replyToModifyContent' ) );

        $this->aPostTypeSlugs = $aPostTypeSlugs;

    }
    public function replyToModifyContent( $sPostContent ) {

        if ( ! is_singular( $this->aPostTypeSlugs ) ) {
            return $sPostContent;
        }
        if ( ! is_main_query() ) {
            return $sPostContent;
        }
        $_oPost     = get_post();
        $_aPostData = array();
        foreach( ( array ) get_post_custom_keys( $_oPost->ID ) as $_sKey ) {    // This way, array will be unserialized; easier to view.
            $_aPostData[ $_sKey ] = get_post_meta( $_oPost->ID, $_sKey, true );
        }
        return $sPostContent
            . "<h3>" . ShortcodeDirectives_Registry::NAME . " DEBUG - Post Meta</h3>"
            . "<p><em>(This is only shown when the plugin debug mode is enabled.)</em></p>"
            . ShortcodeDirectives_Debug::get( $_aPostData );

    }

}